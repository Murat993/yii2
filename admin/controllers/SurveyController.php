<?php

namespace admin\controllers;

use admin\models\AddFilialForm;
use admin\models\LinkMsForm;
use admin\models\AnswersForm;
use common\models\Article;
use common\models\MsSurvey;
use common\models\SurveyFilial;
use common\models\Task;
use common\models\User;
use common\services\notifications\NotificationService;
use common\translate\models\Lang;
use kartik\mpdf\Pdf;
use Yii;
use common\models\Survey;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;
use yii\web\Response;
use yii\web\UploadedFile;
use yii\widgets\ActiveForm;

/**
 * SurveyController implements the CRUD actions for Survey model.
 */
class SurveyController extends BaseController
{
    const TAB_TASKS = 1;
    const TAB_FILIALS = 2;

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => [
                            'index', 'view', 'create', 'update', 'delete', 'add-task', 'add-filial', 'edit-task',
                            'unlink-filial', 'management', 'edit-survey-filial', 'link-ms', 'unlink-ms', 'download',
                            'validate-filial-form', 'switch-user', 'revert-status', 'view-instruction', 'export-survey',
                            'delete-ms'
                        ],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            return Yii::$app->user->can('admin-permissions');
                        }
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Survey models.
     * @return mixed
     */
    public function actionIndex()
    {
        $this->view->title = Yii::t('app', 'Анкетирование');
        $this->view->params['breadcrumbs'] = [
            $this->view->title
        ];
        $dataProvider = new ActiveDataProvider([
            'query' => Survey::find()
                ->with([
                    'currentTranslate',
                    'hasTranslate',
                    'questionary',
                    'client',
                ])->orderBy('id DESC'),
            'sort' => false,
            'pagination' => false
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'langList' => Lang::getList()
        ]);
    }

    /**
     * Displays a single Survey model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView(int $id, $tab = 1)
    {
        $this->view->title = Yii::t('app', 'Просмотр анкетирования');
        $this->view->params['breadcrumbs'] = [
            [
                'label' => Yii::t('app', 'Анкетирование'),
                'url' => ['index']
            ],
            $this->view->title
        ];

        $query = Task::find()->where(['survey_id' => $id]);
        $tasks = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
            'sort' => false
        ]);

        $query = SurveyFilial::find()->with(['surveyScenario', 'filial', 'surveyScenario', 'supervisor', 'filial.city'])->where(['survey_id' => $id]);
        $filials = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
            'sort' => false
        ]);


        return $this->render('view', [
            'model' => $this->findModel($id),
            'tasks' => $tasks,
            'filials' => $filials,
            'tab' => $tab
        ]);
    }

    /**
     * Creates a new Survey model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $this->view->title = Yii::t('app', 'Создание анкетирования');
        $this->view->params['breadcrumbs'] = [
            [
                'label' => Yii::t('app', 'Анкетирование'),
                'url' => ['index']
            ],
            $this->view->title
        ];

        $model = new Survey();

        if ($model->load(Yii::$app->request->post())) {
            if ($instruction = UploadedFile::getInstance($model, 'instruction')) {
                $model->instruction = Yii::$app->fileService->saveFile($instruction, 'instruction');
            }
            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionDownload($instruction)
    {
        $path = Yii::getAlias('@common') . '/uploads/instruction/';
        $file = $path . $instruction;
        if (file_exists($file)) {
            return Yii::$app->response->sendFile($file, $instruction);
        }
    }

    /*
     * opens pdf inline
     * for doc/docx opens by google service
     */
    public function actionViewInstruction($instruction)
    {
        $path = Yii::getAlias('@common') . '/uploads/instruction/';
        $file = $path . $instruction;
        if (file_exists($file)) {
            $extension = pathinfo($instruction, PATHINFO_EXTENSION);
            switch ($extension) {
                case 'pdf':
                    return Yii::$app->response->sendFile($file, $instruction, ['inline' => true]);
                    break;
                case 'doc' :
                case 'docx':
                    $fileUrl = Yii::$app->params['adminDomain'] . "uploads/instruction/" . $instruction;
                    return $this->redirect("https://docs.google.com/viewer?url={$fileUrl}");
                    break;
            }
        }
    }

    /**
     * Updates an existing Survey model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $this->view->title = Yii::t('app', 'Редактирование анкетирования');
        $this->view->params['breadcrumbs'] = [
            [
                'label' => Yii::t('app', 'Анкетирование'),
                'url' => ['index']
            ],
            $this->view->title
        ];
        $model = $this->findModel($id);
        $oldInstruction = $model->instruction;

        if ($model->load(Yii::$app->request->post())) {
            if ($instruction = UploadedFile::getInstance($model, 'instruction')) {
                $model->instruction = Yii::$app->fileService->saveFile($instruction, 'instruction', $oldInstruction);
            } else {
                $model->instruction = $oldInstruction;
            }
            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionAddTask($survey_id)
    {
        $model = new Task();
        if ($model->load(Yii::$app->request->post())) {
            $model->survey_id = $survey_id;
            if ($model->save()) {
                return $this->redirect(['view', 'id' => $survey_id, 'tab' => self::TAB_TASKS]);
            }
        }

        return $this->renderAjax('../task/create', [
            'model' => $model,
        ]);
    }

    public function actionEditTask($id)
    {
        $model = Task::findOne($id);
        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->survey_id, 'tab' => self::TAB_TASKS]);
            }
        }

        return $this->renderAjax('../task/update', [
            'model' => $model,
        ]);

    }

    public function actionAddFilial($client_id, $survey_id)
    {
        $model = new AddFilialForm();
        $supervisors = Yii::$app->userService->getSupervisorsAsMap($client_id);
        $supervisors = $supervisors ?: Yii::$app->userService->getSupervisorsGlobalAsMap();
        $scenarios = Yii::$app->utilityService->getScenariosAsMap($client_id);
        if ($model->load(Yii::$app->request->post())) {
            $model->typecastAttributes();
            $entry = Yii::$app->filialService->createSurveyFilialFromForm($model, $survey_id);
            if ($instruction = UploadedFile::getInstance($model, 'instruction')) {
                $entry->instruction = Yii::$app->fileService->saveFile($instruction, 'instruction');
            }
            if ($entry->save()) {
                Yii::$app->surveyService->createEmptyMsSurvey($entry->id, $entry->task_count);
                return $this->redirect(['view', 'id' => $survey_id, 'tab' => self::TAB_FILIALS]);
            }
        }

        return $this->renderAjax('../filial/add/create', [
            'model' => $model,
            'client_id' => $client_id,
            'supervisors' => $supervisors,
            'scenarios' => $scenarios,
        ]);
    }

    public function actionValidateFilialForm()
    {
        $model = new AddFilialForm();
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
    }


    public function actionEditSurveyFilial($survey_id, $survey_filial_id)
    {
        $oldModel = SurveyFilial::findOne($survey_filial_id);
        $form = Yii::$app->filialService->convertSurveyFilialToForm($survey_filial_id);
        $survey = Survey::findOne(['id' => $survey_id]);
        $client_id = $survey->client_id;
        $supervisors = Yii::$app->userService->getSupervisorsAsMap($client_id);
        $supervisors = $supervisors ?: Yii::$app->userService->getSupervisorsGlobalAsMap();
        $scenarios = Yii::$app->utilityService->getScenariosAsMap($client_id);
        if ($form->load(Yii::$app->request->post())) {
            $oldInstruction = $form->instruction;
            if ($instruction = UploadedFile::getInstance($form, 'instruction')) {
                $form->instruction = Yii::$app->fileService->saveFile($instruction, 'instruction', $oldModel->instruction);
            } else {
                $form->instruction = $oldInstruction;
            }
            $result = Yii::$app->filialService->updateSurveyFilialFromForm($form, $survey_filial_id);
            if ($result) {
                return $this->redirect(['view', 'id' => $survey_id, 'tab' => self::TAB_FILIALS]);
            } else {
                Yii::$app->session->setFlash('error', 'Не удалось изменить данные!');
                return $this->redirect(['view', 'id' => $survey_id, 'tab' => self::TAB_FILIALS]);
            }
        }

        return $this->renderAjax('../filial/add/update', [
            'model' => $form,
            'supervisors' => $supervisors,
            'scenarios' => $scenarios
        ]);
    }

    public function actionUnlinkFilial($survey_filial_id, $survey_id)
    {
        $bool = Yii::$app->filialService->unlinkFilial($survey_filial_id);
        if ($bool) {
            Yii::$app->session->setFlash('success', '');
            return $this->redirect(['view', 'id' => $survey_id, 'tab' => self::TAB_FILIALS]);
        } else {
            Yii::$app->session->setFlash('error', 'Ошибка');
            return $this->redirect(['view', 'id' => $survey_id, 'tab' => self::TAB_FILIALS]);
        }
    }


    public function actionManagement($id)
    {
        $this->view->title = Yii::t('app', 'Управление анкетированием');
        $this->view->params['breadcrumbs'] = [
            [
                'label' => Yii::t('app', 'Анкетирование'),
                'url' => ['index']
            ],
            [
                'label' => Yii::t('app', 'Просмотр анкетирования'),
                'url' => ['view?id=' . $id]
            ],
            $this->view->title
        ];

        $tQuery = Task::find()->where(['survey_id' => $id]);
        $tasks = new ActiveDataProvider([
            'query' => $tQuery,
            'pagination' => false,
            'sort' => false
        ]);

        $sfQuery = SurveyFilial::find()->where(['survey_id' => $id])->with('surveyScenario', 'filial.city', 'supervisor');
        $filials = new ActiveDataProvider([
            'query' => $sfQuery,
            'pagination' => false,
            'sort' => false
        ]);

        $msQuery = MsSurvey::find()
            ->where(['survey_filial' => Yii::$app->filialService->collectSurveyFilialKeys($id)])
            ->orderBy('status DESC')
            ->with('ms', 'surveyFilial.filial.city', 'msSurveyDate');
        $msSurvey = new ActiveDataProvider([
            'query' => $msQuery,
            'pagination' => false,
            'sort' => false,
        ]);

        $model = Survey::find()->where(['id' => $id])->with('client', 'questionary')->one();

        $counters = Yii::$app->surveyService->countMsSurveysByStatus($msQuery);
        return $this->render('management/management', [
            'model' => $model,
            'tasks' => $tasks,
            'filials' => $filials,
            'msSurvey' => $msSurvey,
            'counters' => $counters
        ]);
    }

    public function actionLinkMs($survey_filial_id)
    {
        $survey_filial = SurveyFilial::findOne(['id' => $survey_filial_id]);
        $msList = Yii::$app->userService->msMapByClient($survey_filial->survey->client_id, $survey_filial->filial->city_id);
        $msList = $msList ?: Yii::$app->userService->msMapGlobal($survey_filial->filial->city_id);
        $employeeList = Yii::$app->userService->employeesMapByFilial($survey_filial->filial_id);
        $model = new LinkMsForm();

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }


        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->saveAsMsSurvey($survey_filial_id)) {
            Yii::$app->session->setFlash('success', 'Успех');
            // отправка письма уведомления тайному покупателю о новом задании
            $this->sendMSNewTask($model->ms_id, $survey_filial->survey->id, $survey_filial_id);
            return $this->redirect('/survey/management?id=' . $survey_filial->survey->id);
        }

        return $this->renderAjax('management/add_ms', [
            'model' => $model,
            'msList' => $msList,
            'employeeList' => $employeeList,
            'survey_filial_id' => $survey_filial_id
        ]);
    }

    public function actionUnlinkMs($ms_survey_id, $survey_id)
    {
        $res = Yii::$app->surveyService->unlinkMsSurvey($ms_survey_id);
        if ($res) {
            Yii::$app->session->setFlash('success', 'Успех');
        } else {
            Yii::$app->session->setFlash('error', 'Ошибка');
        }
        return $this->actionManagement($survey_id);

    }


    /**
     * Deletes an existing Survey model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionDeleteMs($ms_survey_id, $survey_id)
    {
        if (MsSurvey::deleteAll(['id' => $ms_survey_id])) {
            Yii::$app->session->setFlash('success', 'Опрос успешно удален');
        } else {
            Yii::$app->session->setFlash('error', 'Не удалось удалить опрос');
        }
        return $this->redirect(['survey/management', 'id' => $survey_id]);
    }

    /**
     * Finds the Survey model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Survey the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Survey::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    private function sendMSNewTask($ms_id, $id, $survey_filial_id)
    {
        $ms_survey = MsSurvey::find()->where([
            'ms_id' => $ms_id,
            'status' => MsSurvey::STATUS_MS_ASSIGNED,
            'survey_filial' => $survey_filial_id
        ])->orderBy(['id' => SORT_DESC])->all();
        $ms_survey = $ms_survey[0];

        $user = User::findOne(['id' => $ms_id]);
        $lang = Yii::$app->language;
        return Yii::$app->notification->sendMessage(NotificationService::EMAIL, [
            'from' => Yii::$app->params['supportEmail'],
            'to' => $user->email,
            'subject' => Yii::t('app', 'Новое задание № ' . $ms_survey->id),
            'compose_view' => [
                'html' => '@common/mail/views/html/send-ms-new-task-' . $lang,
                'text' => '@common/mail/views/text/send-ms-new-task-' . $lang
            ],
            'compose_params' => [
                'id' => $id,
                'ms_survey_id' => $ms_survey->id
            ]
        ]);
    }

    public function actionExportSurvey($id, $survey_name)
    {
        $questionDataProvider = new ActiveDataProvider([
            'query' => Article::find()->where(['questionary' => $id]),
            'pagination' => false
        ]);
        $this->view->title = $survey_name;


        $content = $this->renderPartial('_blank', [
            'dataProvider' => $questionDataProvider,
            'answersForm' => new AnswersForm(),
        ]);

        $pdf = new Pdf([
            // set to use core fonts only
            'mode' => Pdf::MODE_UTF8,
            // A4 paper format
            'format' => Pdf::FORMAT_A4,
            // portrait orientation
            'orientation' => Pdf::ORIENT_PORTRAIT,
            // stream to browser inline
            'destination' => Pdf::DEST_BROWSER,
            // your html content input
            'content' => $content,
            // format content from your own css file if needed or use the
            // enhanced bootstrap css built by Krajee for mPDF formatting
            'cssFile' => '@admin/web/themes/plan/assets/css/bootstrap.css',
            'cssInline' => '@admin/web/themes/plan/assets/css/core.css',
            'filename' => 'Анкетирование ' . $survey_name . '.pdf',
            // any css to be embedded if required
            // set mPDF properties on the fly
            'options' => ['title' => 'PLAN MSP ' . $survey_name],
            // call mPDF methods on the fly
            'methods' => [
                'SetHeader' => ['PLAN MSP'],
                'SetFooter' => ['{PAGENO}'],
                'SetAuthor' => 'PLAN MSP',
                'SetCreator' => 'PLAN MSP',
            ]
        ]);
        return $pdf->render();
    }

    /**
     * Переключаем пользователя на супервайзера
     *
     * @param $user_id
     * @param $ms_survey_id
     */
    public function actionSwitchUser($user_id, $ms_survey_id, $survey_id)
    {
        $initialId = Yii::$app->user->id;
        if ($initialId == $user_id) {
            throw new NotFoundHttpException(Yii::t('app', 'Невозможно зайти под тем же пользователем'));
        } else {
            $userModel = \common\models\User::findOne(['id' => $user_id]);
            if (!$userModel) {
                throw new NotFoundHttpException(Yii::t('app', 'Пользователь не найден'));
            }
            $duration = 0;
            Yii::$app->user->switchIdentity($userModel, $duration);
            Yii::$app->session->set('user.idbeforeswitch', $initialId);
            Yii::$app->session->set('user.surveybeforeswitch', $survey_id);
            $this->redirect(Yii::$app->params['supervisorDomain'] . 'ms-survey/view?id=' . $ms_survey_id);
        }

    }

    public function actionRevertStatus($ms_survey_id, $survey_id)
    {
        $model = MsSurvey::findOne($ms_survey_id);
        $model->status = MsSurvey::STATUS_MODERATION_START;
        if ($model->save()) {
            Yii::$app->session->setFlash('success', 'Успех');
        }
        Yii::$app->session->setFlash('error', 'Ошибка');

        return $this->actionManagement($survey_id);

    }
}
