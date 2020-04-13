<?php
/**
 * Created by PhpStorm.
 * User: narims
 * Date: 27.06.18
 * Time: 15:31
 */

namespace ms\controllers;

use common\helpers\FilesHelper;
use common\models\MsSurvey;
use common\models\MsSurveyDate;
use common\models\TaskFile;
use common\models\User;
use ms\models\AnswersForm;
use common\models\Article;
use common\models\QuestionAnswer;
use common\models\Survey;
use common\models\Task;
use common\models\TaskAnswer;
use Yii;
use yii\base\Exception;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use yii\filters\AccessControl;
use common\models\search\TaskFile as TaskFileSearch;


class SurveyController extends BaseController
{
    const VIEW_TAB_INFO = 1;
    const VIEW_TAB_TASKS = 2;
    const VIEW_TAB_QUESTIONS = 3;

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
                            'index', 'view', 'create', 'update', 'delete',
                            'save-answers', 'render-task-form', 'save-task-answer',
                            'are-tasks-completed', 'start-survey', 'download',
                            'save-answer-on-blur', 'task-files', 'delete-task-file',
                            'view-instruction', 'update-employee-on-blur', 'update-datetime-on-blur'
                        ],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            if (Yii::$app->user->can('ms-permissions') && (Yii::$app->user->identity->role === User::ROLE_MYSTIC || Yii::$app->user->identity->role === User::ROLE_MYSTIC_GLOBAL)) {
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
                    'save-task-answer' => ['POST']
                ],
            ],
        ];
    }

    /**
     * Lists all Survey models.
     * @return mixed
     */
    public function actionIndex($tab = null)
    {
        $this->view->title = Yii::t('app', 'Анкетирование');
        $this->view->params['breadcrumbs'] = [
            $this->view->title
        ];
        $query = MsSurvey::find()
            ->with('surveyFilial.filial')
            ->with('surveyFilial.survey')
            ->where([
                'ms_id' => Yii::$app->user->getId(),
            ])->andWhere(['or',
                ['status' => MsSurvey::STATUS_MS_ASSIGNED],
                ['status' => MsSurvey::STATUS_IN_PROCESS]
            ])->orderBy('id DESC');
        $mainDataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
            'sort' => false
        ]);
        $historyQuery = MsSurvey::find()
            ->with('surveyFilial.filial')
            ->with('surveyFilial.survey')
            ->where([
                'ms_id' => Yii::$app->user->getId(),
            ])->andWhere(['or',
                ['status' => MsSurvey::STATUS_COMPLETED],
                ['status' => MsSurvey::STATUS_MODERATION_START],
                ['status' => MsSurvey::STATUS_MODERATION]
            ])->orderBy('id DESC');
        $historyDataProvider = new ActiveDataProvider([
            'query' => $historyQuery,
            'pagination' => false,
            'sort' => false
        ]);

        return $this->render('index', [
            'mainDataProvider' => $mainDataProvider,
            'historyDataProvider' => $historyDataProvider,
            'tab' => $tab
        ]);
    }

    public function actionView(int $ms_survey_id, int $id = null, int $tab = null)
    {
        $msSurvey = MsSurvey::find()->where(['id' => $ms_survey_id])->with('surveyFilial.survey')->one();
        if ($msSurvey->status !== MsSurvey::STATUS_IN_PROCESS && $msSurvey->status !== MsSurvey::STATUS_MS_ASSIGNED) {
            throw new ForbiddenHttpException('', 403);
        }
        $taskDataProvider = new ActiveDataProvider([
            'query' => Task::find()->where(['survey_id' => $msSurvey->surveyFilial->survey_id]),
            'pagination' => false,
            'sort' => false
        ]);
        $questionDataProvider = new ActiveDataProvider([
            'query' => Article::find()
                ->innerJoin('questionary', "article.questionary=questionary.id")
                ->where(['questionary.id' => $msSurvey->surveyFilial->survey->questionary_id])->orderBy('sorting ASC, id ASC'),
            'pagination' => false,
            'sort' => false
        ]);
        $model = $this->findModel($msSurvey->surveyFilial->survey_id);
        if ($msSurvey->surveyFilial->instruction) {
            $instruction = $msSurvey->surveyFilial->instruction;
        } elseif ($model->instruction) {
            $instruction = $model->instruction;
        }

        $msd = MsSurveyDate::findOne(['ms_survey_id' => $ms_survey_id, 'type' => MsSurveyDate::DATE_TYPE_VISITED]);
        $answersForm = new AnswersForm();
        if ($msd) {
            $answersForm->employee = $msd->employee_name;
            $answersForm->visit_date = $msd->date;
        } else {
            $zone = new \DateTimeZone('Asia/Almaty');
            $answersForm->visit_date = (new \DateTime(date('Y-m-d H:i:s')))->setTimezone($zone)->format('Y-m-d H:i:s');
            $answersForm->save($ms_survey_id);
        }
        return $this->render('view', [
            'msSurvey' => $msSurvey,
            'model' => $model,
            'taskDataProvider' => $taskDataProvider,
            'questionDataProvider' => $questionDataProvider,
            'answersForm' => $answersForm,
            'ms_survey_id' => $ms_survey_id,
            'employee' => $msSurvey->employee,
            'msStatus' => $msSurvey->status,
            'tab' => $tab,
            'id' => $id,
            'instruction' => $instruction
        ]);
    }

    protected function findModel($id)
    {
        if (($model = Survey::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    /**
     * save question by ajax request blur
     */
    public function actionSaveAnswerOnBlur()
    {
        if (Yii::$app->request->isAjax) {
            $ids = Yii::$app->request->get('ids');
            $ms_survey_id = Yii::$app->request->get('ms_survey_id');
            $survey_id = Yii::$app->request->get('survey_id');
            $text = Yii::$app->request->get('text');
            $q_ids = Yii::$app->request->get('question_ids');
            $questionAnswer = QuestionAnswer::find()->where(['question_id' => (int)$q_ids, 'ms_survey_id' => (int)$ms_survey_id])->one();
            if (!$questionAnswer) {
                $questionAnswer = new QuestionAnswer;
            }
            $questionAnswer->question_id = $q_ids;
            $questionAnswer->text = $text;
            $questionAnswer->ms_id = Yii::$app->user->getId();
            $questionAnswer->survey_id = $survey_id;
            $questionAnswer->ms_survey_id = $ms_survey_id;
            $questionAnswer->save();
        }
    }

    public function actionUpdateEmployeeOnBlur(int $ms_survey_id)
    {
        if (Yii::$app->request->isAjax) {
            $employee = Yii::$app->request->get('employee');
            $ms = MsSurvey::findOne($ms_survey_id);
            if ($ms) {
                $msd = MsSurveyDate::findOne(['ms_survey_id' => $ms_survey_id, 'type' => MsSurveyDate::DATE_TYPE_VISITED]);
                if (!$msd) {
                    $msd = new MsSurveyDate();
                    $msd->ms_survey_id = $ms_survey_id;
                    $msd->type = MsSurveyDate::DATE_TYPE_VISITED;
                }
                $msd->employee_name = $employee;
                return $msd->save();
            }
        }
    }

    public function actionUpdateDatetimeOnBlur(int $ms_survey_id)
    {
        if (Yii::$app->request->isAjax) {
            $datetime = Yii::$app->request->get('datetime');
            $ms = MsSurvey::findOne($ms_survey_id);
            if ($ms) {
                $msd = MsSurveyDate::findOne(['ms_survey_id' => $ms_survey_id, 'type' => MsSurveyDate::DATE_TYPE_VISITED]);
                if (!$msd) {
                    $msd = new MsSurveyDate();
                    $msd->ms_survey_id = $ms_survey_id;
                    $msd->type = MsSurveyDate::DATE_TYPE_VISITED;
                }
                $msd->date = $datetime;
                return $msd->save();
            }
        }
    }

    public function actionSaveAnswers($survey_id, $ms_survey_id)
    {
        $form = new AnswersForm();
        $form->calcScenario();
        if ($form->load(Yii::$app->request->post()) && $form->validate() && $form->save($ms_survey_id)) {
            $form->checkAnswers($ms_survey_id, $survey_id);
            MsSurvey::updateAll(['status' => MsSurvey::STATUS_MODERATION], ['id' => $ms_survey_id]);
            Yii::$app->session->setFlash('success', "Анкета успешно отправлена на модерацию.");
            $this->redirect(['index']);
        } else {
            throw new BadRequestHttpException();
        }
    }

    /**
     * Ajax render
     * @param $task_answer_id
     */
    public function actionRenderTaskForm($task_id, $ms_survey_id)
    {
        $task = Task::findOne($task_id);
        if (!$task) {
            throw new NotFoundHttpException();
        }
        $answer = TaskAnswer::find()->where(['task_id' => $task->id, 'ms_id' => Yii::$app->user->getId(), 'ms_survey_id' => $ms_survey_id])->one();
        if (!$answer) {
            $answer = new TaskAnswer();
            $answer->task_id = $task->id;
            $answer->ms_id = Yii::$app->user->getId();
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

    public function actionSaveTaskAnswer($task_id, $survey_id, $ms_survey_id)
    {
        $task = Task::findOne($task_id);
        if (!$task) {
            throw new NotFoundHttpException();
        }
        $model = TaskAnswer::find()->where(['task_id' => $task->id, 'ms_id' => Yii::$app->user->getId(), 'ms_survey_id' => $ms_survey_id])->one();
        if (!$model) {
            $model = new TaskAnswer();
            $model->task_id = $task->id;
            $model->ms_id = Yii::$app->user->getId();
            $model->ms_survey_id = $ms_survey_id;
        }
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($taksFile = UploadedFile::getInstances($model, 'file')) {
                $arrTaskFiles = [];
                $i = 0;
                foreach ($taksFile as $file) {
                    if ($task->filetype === Task::AUDIO) {
                        $userImagePath = Yii::$app->fileService->saveFile($file, 'task-answer');
                    } else {
                        $userImagePath = Yii::$app->imagesService->saveImage($file, 'task-answer');
                    }
                    $arrTaskFiles[$i]['type'] = $task->filetype;
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
        return $this->redirect(['view', 'id' => $survey_id, 'ms_survey_id' => $ms_survey_id, 'tab' => self::VIEW_TAB_TASKS]);
    }

    public function actionAreTasksCompleted($ms_id, $ms_survey_id)
    {
        $surveyId = MsSurvey::findOne($ms_survey_id)->surveyFilial->survey->id;
        $taskCount = Task::find()->where(['survey_id' => $surveyId])->count();

        $taskAnswers = TaskAnswer::find()->where(['ms_id' => $ms_id, 'ms_survey_id' => $ms_survey_id])->all();
        if ($taskCount == count($taskAnswers)) {
            return 1;
        } else {
            return 0;
        }
    }

    public function actionStartSurvey($ms_survey_id, $id)
    {
        $count = MsSurvey::updateAll(['status' => MsSurvey::STATUS_IN_PROCESS], ['id' => $ms_survey_id]);
        if ($count) {
            Yii::$app->session->setFlash('success', Yii::t('app', "Статус изменен"));
        } else {
            Yii::$app->session->setFlash('success', Yii::t('app', "Не удалось изменить статус"));
        }
        return $this->redirect(['view', 'id' => $id, 'ms_survey_id' => $ms_survey_id]);
    }

    public function actionDownload($instruction)
    {
        $path = Yii::getAlias('@common') . '/uploads/instruction/';
        $file = $path . $instruction;
        if (file_exists($file)) {
            return Yii::$app->response->sendFile($file);
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
                    $fileUrl = Yii::$app->params['msDomain'] . "uploads/instruction/" . $instruction;
                    return $this->redirect("https://docs.google.com/viewer?url={$fileUrl}");
                    break;
            }
        }
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

        return $this->render('task_files', [
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


}