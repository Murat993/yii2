<?php
/**
 * Created by PhpStorm.
 * User: narims
 * Date: 27.06.18
 * Time: 15:31
 */

namespace supervisor\controllers;

use common\models\LinkMsForm;
use common\models\MsSurvey;
use common\models\MsSurveyDate;
use common\models\Question;
use common\models\QuestionAnswer;
use common\models\QuestionCheck;
use common\models\SurveyFilial;
use common\models\TaskAnswer;
use common\models\TaskFile;
use common\models\User;
use yii\base\Exception;
use supervisor\models\AnswersForm;
use common\models\Article;
use common\models\Task;
use supervisor\models\MsSurveySearchForm;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\web\UploadedFile;
use yii\widgets\ActiveForm;
use common\helpers\FilesHelper;
use common\models\search\TaskFile as TaskFileSearch;

class MsSurveyController extends BaseController
{
    const TAB_QUESTIONS = 1;
    const TAB_TASK = 2;
    const TAB_INFO = 3;


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
                            'index', 'view', 'render-moderate-form', 'moderate-answer', 'publish-survey',
                            'stats', 'start-survey', 'download', 'update-question-answer', 'link-ms', 'unlink-ms',
                            'save-task-answer', 'render-task-form', 'task-files', 'delete-task-file', 'update-date-visited',
                            'view-instruction'
                        ],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            if (Yii::$app->user->can('supervisor-permissions') && (Yii::$app->user->identity->role === User::ROLE_SUPERVISOR || Yii::$app->user->identity->role === User::ROLE_SUPERVISOR_GLOBAL)) {
                                return true;
                            } else {
                                return false;
                            }
                        },
                    ],
                ],
            ],

            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                    'save-task-answer' => ['POST'],
                    'moderate-answer' => ['POST']
                ],
            ],
        ];
    }

    public function actionView($id, $tab = null, $open_modal = false, $completed = 0)
    {
        $model = MsSurvey::find()->where(['id' => $id])->with('surveyFilial', 'surveyFilial.survey.client', 'surveyFilial.surveyScenario', 'msSurveyDate')->one();
        $taskDataProvider = new ActiveDataProvider([
            'query' => Task::find()->where(['survey_id' => $model->surveyFilial->survey_id]),
            'pagination' => false
        ]);
        $questionaryIds = array_column((new Query())
            ->select(['questionary_id'])
            ->from('survey')
            ->where(['id' => $model->surveyFilial->survey_id])->all(), 'questionary_id');
        $questionDataProvider = new ActiveDataProvider([
            'query' => Article::find()
                ->innerJoin('questionary', "article.questionary=questionary.id")
                ->where(['in', 'questionary.id', $questionaryIds])
                ->with([
                    'currentTranslate', // loadabing data associated with the current translation
                    'hasTranslate' // need to display the status was translated page
                ])->orderBy('sorting ASC, id ASC'),
            'pagination' => false,
            'sort' => false
        ]);

        return $this->render('view', [
            'model' => $model,
            'taskDataProvider' => $taskDataProvider,
            'questionDataProvider' => $questionDataProvider,
            'answersForm' => new AnswersForm(),
            'tab' => $tab,
            'open_modal' => $open_modal,
            'completed' => $completed,
            'id' => $id
        ]);
    }

    /**
     * update QuestionAnswer ActiveForm ajax request
     */
    public function actionUpdateQuestionAnswer()
    {
        if (Yii::$app->request->isAjax) {
            $questionAnswer = Yii::$app->request->get('questionAnswer');
            $questionId = Yii::$app->request->get('questionId');
            $question = QuestionAnswer::findOne($questionId);
            $question->text = $questionAnswer;
            $question->save();
        }
    }

    public function actionRenderModerateForm($question_id, $answer_id, $completed = 0)
    {
        $question = Question::find()->where(['id' => $question_id])->with('questionChecks')->one();
        $question_answer = QuestionAnswer::findOne($answer_id);
        if (!$question || !$question_answer) {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
        $qn_check = QuestionCheck::find()->where(['answer_id' => $answer_id])->one();
        if (!$qn_check) {
            $qn_check = new QuestionCheck();
        }
        switch ($question->answer_type) {
            case Question::ANSWER_ONE_VAR:
                $qn_check->scenario = QuestionCheck::SCENARIO_OPTIONS;
                break;
            case Question::ANSWER_MULTIPLE_VAR:
                $qn_check->scenario = QuestionCheck::SCENARIO_OPTIONS_MULTI;
                break;
            case Question::ANSWER_NUM:
                $qn_check->scenario = QuestionCheck::SCENARIO_NUM;
                break;
            case Question::ANSWER_TEXT:
                $qn_check->scenario = QuestionCheck::SCENARIO_TEXT;
                break;
        }


        return $this->renderAjax('modals/_moderate_form', [
            'question' => $question,
            'questionAnswer' => $question_answer,
            'qn_check' => $qn_check,
            'completed' => $completed,
            'answer_id' => $answer_id
        ]);
    }

    public function actionModerateAnswer($ms_survey_id, $scenario)
    {
        $model = new QuestionCheck();
        $model->scenario = $scenario;
        $model->supervisor_id = Yii::$app->user->getId();
        if ($model->load(Yii::$app->request->post()) && $model->saveAnswers()) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Ответ сохранен успешно'));
            return $this->redirect([
                'view',
                'id' => $ms_survey_id,
                'tab' => self::TAB_QUESTIONS,
                'open_modal' => !($model->skip)
            ]);
        } else {
            Yii::$app->session->setFlash('error', Yii::t('app', 'Не удалось сохранить ответ'));
            return $this->redirect([
                'view',
                'id' => $ms_survey_id,
            ]);
        }
        throw new Exception();
    }

    public function actionPublishSurvey($ms_survey_id)
    {
        if (Yii::$app->surveyService->isMsSurveyValid($ms_survey_id)) {
            try {
                MsSurvey::updateAll(['status' => MsSurvey::STATUS_COMPLETED], ['id' => $ms_survey_id]);
                Yii::$app->session->setFlash('success', Yii::t('app', 'Анкета опубликована'));
                return $this->redirect(['index']);
            } catch (Exception $e) {
                Yii::$app->session->setFlash('error', Yii::t('app', 'Не удалось опубликовать анкету, ошибка сервера'));
                return $this->redirect(['view', 'id' => $ms_survey_id, 'tab' => self::TAB_QUESTIONS]);
            }
        }
        Yii::$app->session->setFlash('error', Yii::t('app', 'Не удалось опубликовать анкету, заполните все вопросы'));
        return $this->redirect(['view', 'id' => $ms_survey_id, 'tab' => self::TAB_QUESTIONS]);
    }

    public function actionStats()
    {
        $this->view->title = Yii::t('app', 'График');
        $this->view->params['breadcrumbs'] = [
            $this->view->title
        ];
        $searchModel = new MsSurveySearchForm();
        $searchModel->status = null;
        $searchModel->load(Yii::$app->request->queryParams);
        $query = new Query();
        $query->select([
            "scenario.name as name",
            "COUNT(ms_survey.id) as total",
            "COUNT(IF(ms_survey.status = NULL OR ms_survey.status = 0, 1, NULL)) as naznacheno",
            "COUNT(IF(ms_survey.status = 222, 1, NULL)) as in_process",
            "COUNT(IF(ms_survey.status = 333 OR ms_survey.status = 444, 1, NULL)) as moderate",
            "COUNT(IF(ms_survey.status = 555, 1, NULL)) as completed",])
            ->from('scenario')
            ->leftJoin("survey_filial", "scenario.id = survey_filial.id_scenario")
            ->leftJoin("ms_survey", 'survey_filial.id = ms_survey.survey_filial')
            ->leftJoin('filial', 'survey_filial.filial_id = filial.id')
            ->innerJoin('survey', 'survey.id = survey_filial.survey_id')
            ->groupBy(new \yii\db\Expression('scenario.id WITH ROLLUP'));
        $dataProvider = new ActiveDataProvider(['query' => $query, 'sort' => false,
            'pagination' => false]);
        $queryList = MsSurvey::find()
            ->with('ms', 'surveyFilial.filial', 'surveyFilial.survey', 'surveyFilial.surveyScenario')
            ->innerJoin('survey_filial', 'survey_filial.id = ms_survey.survey_filial')
            ->innerJoin('filial', 'filial.id = survey_filial.filial_id')
            ->innerJoin('survey', 'survey.id = survey_filial.survey_id')
            ->andWhere(['survey_filial.supervisor_id' => Yii::$app->user->id])->orderBy('id DESC');
        if ($searchModel->ms_id) {
            $query->andWhere(['ms_survey.ms_id' => $searchModel->ms_id]);
            $queryList->andWhere(['ms_id' => $searchModel->ms_id]);
        }
        if ($searchModel->city_id) {
            $query->andWhere(['filial.city_id' => $searchModel->city_id]);
            $queryList->andWhere(['filial.city_id' => $searchModel->city_id]);
        }

        if ($searchModel->geo_country && !$searchModel->city_id) {
            $ids = Yii::$app->geoService->getCityIdsByCountry($searchModel->geo_country);
            $query->andWhere(['in', 'filial.city_id', $ids]);
        }

        if ($searchModel->date) {
            $date = date('Y-m-d', strtotime($searchModel->date));
            $query->andWhere(['<=', 'survey.survey_from', $date]);
            $query->andWhere(['>=', 'survey.survey_to', $date]);
            $queryList->andWhere(['<=', 'survey.survey_from', $date]);
            $queryList->andWhere(['>=', 'survey.survey_to', $date]);
        }

        $dataProviderList = new ActiveDataProvider(['query' => $queryList,
            'sort' => false,
            'pagination' => false
        ]);
        return $this->render('stats', [
            'dataProvider' => $dataProvider,
            'dataProviderList' => $dataProviderList,
            'searchModel' => $searchModel,
            'employeeList'
        ]);
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
        $searchModel = new MsSurveySearchForm();
        $searchModel->load(Yii::$app->request->queryParams);
        $client_id = $this->getClientId();
        $query = MsSurvey::find()
            ->with('ms')
            ->joinWith('surveyFilial.filial')
            ->joinWith('surveyFilial.survey')
            ->select(' `ms_survey`.*, 
            (getRealPointsBySurveyMS(`survey_filial`.`survey_id`, `ms_survey`.`id`)*100)
            /getMaxPointsBySurvey(`survey_filial`.`survey_id`) as percent ')
            ->where([
                'status' => $searchModel->status
            ])->orderBy('id DESC');

        if ($client_id) {
            $query->andWhere([
                'filial.parent_id' => $client_id
            ]);
        }
        if ($searchModel->ms_id) {
            $query->andWhere(['ms_id' => $searchModel->ms_id]);
        }

        if ($searchModel->city_id) {
            $query->andWhere(['filial.city_id' => $searchModel->city_id]);
        }

        if ($searchModel->geo_country && !$searchModel->city_id) {
            $ids = Yii::$app->geoService->getCityIdsByCountry($searchModel->geo_country);
            $query->andWhere(['in', 'filial.city_id', $ids]);
        }
        $dataProvider = new ActiveDataProvider(['query' => $query,
            'sort' => false, 'pagination' => false]);
        switch ($searchModel->status) {
            case MsSurvey::STATUS_COMPLETED:
                $panel = "panel panel-success panel-bordered";
                $header = Yii::$app->utilityService->getSurveyStatusesForSupervisor()[$searchModel->status];
                break;
            case MsSurvey::STATUS_MODERATION:
                $panel = "panel panel-warning panel-bordered";
                $header = Yii::$app->utilityService->getSurveyStatusLabel($searchModel->status);
                break;
            case MsSurvey::STATUS_MODERATION_START:
                $panel = "panel panel-info panel-bordered";
                $header = Yii::$app->utilityService->getSurveyStatusLabel($searchModel->status);
                break;
        }

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'client_id' => $client_id,
            'panel' => $panel,
            'header' => $header
        ]);
    }

    public function actionStartSurvey($id)
    {
        $count = MsSurvey::updateAll(['status' => MsSurvey::STATUS_MODERATION_START], ['id' => $id]);
        if ($count) {
            Yii::$app->session->setFlash('success', Yii::t('app', "Статус изменен"));
        } else {
            Yii::$app->session->setFlash('success', Yii::t('app', "Не удалось изменить статус"));
        }
        return $this->redirect(['view', 'id' => $id]);
    }

    public function actionDownload($instruction)
    {
        $path = Yii::getAlias('@common') . '/uploads/instruction/';
        $file = $path . $instruction;
        if (file_exists($file)) {
            return Yii::$app->response->sendFile($file);
        }
    }

    /**
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
                    $fileUrl = Yii::$app->params['supervisorDomain'] . "uploads/instruction/" . $instruction;
                    return $this->redirect("https://docs.google.com/viewer?url={$fileUrl}");
                    break;
            }
        }
    }

    protected function findModel($id)
    {
        if (($model = MsSurvey::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    public function actionLinkMs($survey_filial_id, $ms_survey_id)
    {
        $survey_filial = SurveyFilial::find()->where(['id' => $survey_filial_id])->with('survey')->one();
        $msList = Yii::$app->userService->msMapByClient($survey_filial->survey->client_id);
        $msList = $msList ?: Yii::$app->userService->msMapGlobal();
        $employeeList = Yii::$app->userService->employeesMapByFilial($survey_filial->filial_id);
        $model = new LinkMsForm();

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }


        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->updateAsMsSurvey($ms_survey_id)) {
            Yii::$app->session->setFlash('success', 'Успех');
            return $this->redirect('stats');
        }

        return $this->renderAjax('modals/add_ms', [
            'model' => $model,
            'msList' => $msList,
            'employeeList' => $employeeList,
            'survey_filial_id' => $survey_filial_id,
            'ms_survey_id' => $ms_survey_id
        ]);
    }

    public function actionUnlinkMs($ms_survey_id)
    {
        $res = Yii::$app->surveyService->unlinkMsSurvey($ms_survey_id);
        if ($res) {
            Yii::$app->session->setFlash('success', 'Успех');
        } else {
            Yii::$app->session->setFlash('error', 'Ошибка');
        }
        return $this->redirect('stats');

    }

    public function actionSaveTaskAnswer($task_id, $survey_id, $ms_survey_id)
    {
        $ms_survey_model = MsSurvey::findOne($ms_survey_id);

        $task = Task::findOne($task_id);
        if (!$task) {
            throw new NotFoundHttpException();
        }
        $model = TaskAnswer::find()->where(['task_id' => $task->id, 'ms_id' => $ms_survey_model->ms_id, 'ms_survey_id' => $ms_survey_id])->one();
        if (!$model) {
            $model = new TaskAnswer();
            $model->task_id = $task->id;
            $model->ms_id = $ms_survey_model->ms_id;
            $model->ms_survey_id = $ms_survey_id;
        }
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($taksFile = UploadedFile::getInstances($model, 'file')) {
                $arrTaskFiles = [];
                $i = 0;
                foreach ($taksFile as $file) {
                    $checkFormat = substr($file->name, -3);
                    if ($checkFormat == 'mp3' || $checkFormat == 'wav' || $checkFormat == 'amr') {
                        $userImagePath = Yii::$app->fileService->saveFile($file, 'task-answer');
                        $arrTaskFiles[$i]['type'] = Task::AUDIO;
                    } else {
                        $userImagePath = Yii::$app->imagesService->saveImage($file, 'task-answer');
                        $arrTaskFiles[$i]['type'] = Task::PHOTO;
                    }
                    $arrTaskFiles[$i]['file_name'] = $userImagePath;
                    $arrTaskFiles[$i]['task_id'] = $model->task_id;
                    $arrTaskFiles[$i]['ms_survey_id'] = $model->ms_survey_id;
                    $i = ++$i;
                }

                Yii::$app->db->createCommand()->batchInsert('task_file', ['type', 'file_name', 'task_id', 'ms_survey_id'], $arrTaskFiles)->execute();

                $model->file = 1;
            }
            if (!$model->save()) {
                throw new Exception();
            }
        }
        return $this->redirect(['view', 'id' => $ms_survey_id, 'tab' => self::TAB_TASK]);
    }

    /**
     * Ajax render
     * @param $task_answer_id
     */
    public function actionRenderTaskForm($task_id, $ms_survey_id)
    {
        $ms_survey_model = MsSurvey::findOne($ms_survey_id);

        $task = Task::findOne($task_id);
        if (!$task) {
            throw new NotFoundHttpException();
        }
        $answer = TaskAnswer::find()->where(['task_id' => $task->id, 'ms_id' => $ms_survey_model->ms_id, 'ms_survey_id' => $ms_survey_id])->one();
        if (!$answer) {
            $answer = new TaskAnswer();
            $answer->task_id = $task->id;
            $answer->ms_id = $ms_survey_model->ms_id;
            $answer->ms_survey_id = $ms_survey_id;
        }
        $answer->scenario = $task->filetype;
        $description = "";
        if ($task->filetype == Task::PHOTO) {
            $description = Yii::t('app', "Файл должен быть в формате jpg, png, jpeg");
        } elseif ($task->filetype == Task::AUDIO) {
            $description = Yii::t('app', "Файл должен быть в формате mp3, wav");
        }
        return $this->renderAjax('modals/_upload_task_form', [
            'model' => $answer,
            'title' => $task->name,
            'description' => $description,
            'survey_id' => $task->survey_id,
            'ms_survey_id' => $ms_survey_id
        ]);
    }

    /**
     * Просмотр списка файлов прикреплённых к заданию
     * @param $id
     * @param $task_id
     * @param $ms_survey_id
     * @return string
     */
    public function actionTaskFiles($id, $task_id, $ms_survey_id)
    {
        $searchModel = new TaskFileSearch();
        $searchModel->task_id = $task_id;
        $searchModel->ms_survey_id = $ms_survey_id;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('@ms/views/survey/task_files', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'ms_survey_id' => $ms_survey_id,
            'id' => $id
        ]);
    }


    /**
     * Удаление файлов по одному
     *
     * @param $id
     * @param $file_name
     * @param $task_id
     * @param $ms_survey_id
     * @param $survey_id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDeleteTaskFile($id, $file_name, $task_id, $ms_survey_id, $survey_id)
    {
        FilesHelper::deleteFile(Yii::getAlias('@common/') . "uploads/task-answer/" . $file_name);

        $model = TaskFile::findOne($id);

        if (empty($model)) {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }

        $model->delete();

        if (!(TaskFile::find()->where(['task_id' => $task_id])->exists())) {
            $task = TaskAnswer::findOne(['id' => $task_id]);
            $task->file = null;
            $task->save();
        }

        return $this->redirect(['task-files', 'id' => $survey_id, 'task_id' => $task_id, 'ms_survey_id' => $ms_survey_id]);
    }

    /**
     * Обновление основной информации
     *
     * @param $ms_survey_id
     * @param null $id
     * @return string|Response
     */
    public function actionUpdateDateVisited($ms_survey_id, $id = null)
    {
        if ($id) {
            $model = MsSurveyDate::findOne($id);
        } else {
            $model = new MsSurveyDate();
        }

        $model->type = MsSurveyDate::DATE_TYPE_VISITED;
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['/ms-survey/view', 'id' => $ms_survey_id, 'tab' => self::TAB_INFO]);
        }

        $ms_survey = MsSurvey::findOne($ms_survey_id);

        return $this->render('update-info', [
            'model' => $model,
            'ms_survey_id' => $ms_survey_id,
            'title' => $ms_survey->surveyFilial->survey->name,
            'id' => $ms_survey->id,
        ]);
    }

}