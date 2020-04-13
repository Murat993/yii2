<?php

namespace common\services;

use common\models\LinkMsForm;
use common\models\Changelog;
use common\models\MsSurvey;
use common\models\Question;
use common\models\Scenario;
use common\models\Tariff;
use common\models\Task;
use common\models\User;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Description of UtilityService
 *
 * @author ikorostelev
 */
class UtilityService
{
    const TYPE_ADMIN = 1;
    const TYPE_MS = 2;
    const TYPE_CLIENT = 3;
    const TYPE_SUP = 4;

    public function getStatusList()
    {
        $array = [
            User::STATUS_ACTIVE => Yii::t('app', 'Активирован'),
            User::STATUS_DELETED => Yii::t('app', 'Удален'),
            User::STATUS_DEACTIVATED => Yii::t('app', 'Деактивирован'),
        ];
        return $array;
    }


    public function getUserRoleList()
    {
        $array = [
            User::ROLE_ADMIN => Yii::t('app', 'Администратор'),
            User::ROLE_SUPERVISOR_GLOBAL => Yii::t('app', 'Глобальный супервайзер'),
            User::ROLE_SUPERVISOR => Yii::t('app', 'Супервайзер'),
            User::ROLE_MYSTIC => Yii::t('app', 'Тайный покупатель'),
            User::ROLE_MYSTIC_GLOBAL => Yii::t('app', 'Глобальный тайный покупатель'),
            User::ROLE_CLIENT_USER => Yii::t('app', 'Клиент-пользователь'),
            User::ROLE_CLIENT_SUPER => Yii::t('app', 'Клиент-Администратор'),
        ];
        return $array;
    }

    public function getRoleLabel($type)
    {
        $list = $this->getUserRoleList();
        return $list[$type];
    }

    public function getStatusLabel($status)
    {
        $list = $this->getStatusList();
        return $list[$status] ? $list[$status] : '-';
    }

    public function getTariffsAsMap()
    {
        $tariffs = Tariff::find()->all();
        if ($tariffs) {
            return ArrayHelper::map($tariffs, 'id', 'name');
        } else {
            return [];
        }
    }

    public function getAnswerTypes()
    {
        $array = [
            Question::ANSWER_ONE_VAR => Yii::t('app', 'Возможен один вариант'),
            Question::ANSWER_NUM => Yii::t('app', 'Число'),
            Question::ANSWER_MULTIPLE_VAR => Yii::t('app', 'Возможно несколько вариантов'),
            Question::ANSWER_TEXT => Yii::t('app', 'Текст'),
        ];
        return $array;
    }

    public function getFileTypes()
    {
        $array = [
            Task::AUDIO => Yii::t('app', 'Аудиофайл'),
            Task::PHOTO => Yii::t('app', 'Фотография'),
        ];
        return $array;
    }

    public function getFileTypeLabel($filetype)
    {
        $list = $this->getFileTypes();
        return $list[$filetype];
    }

    public function getAnswerLabel($answer)
    {
        $list = $this->getAnswerTypes();
        return $list[$answer];
    }

    public function getScenariosAsMap($client_id)
    {
        $scenarios = Scenario::find()->where(['id_client' => $client_id])->all();
        if ($scenarios) {
            return ArrayHelper::map($scenarios, 'id', 'name');
        } else {
            return [];
        }
    }

    public function getChangelogActions()
    {
        $array = [
            Changelog::TYPE_CREATE => Yii::t('app', 'Create Action'),
            Changelog::TYPE_DELETE => Yii::t('app', 'Delete Action'),
            Changelog::TYPE_UPDATE => Yii::t('app', 'Update Action')
        ];
        return $array;
    }

    public function getTableLabels()
    {
        $array = [
//            'answer_option' => Yii::t(),
//            'answer_option_lang' => Yii::t(),
            'article' => Yii::t('app', 'Артикул'),
//            'article_lang' => Yii::t(),
            'client' => Yii::t('app', 'Client ID'),
//            'client_user' => Yii::t(),
//            'client_user_filial' => Yii::t(),
//            'employee' => Yii::t(),
//            'employee_filial' => Yii::t(),
//            'filial' => Yii::t(),
//            'filial_structure_unit' => Yii::t(),
//            'geo_unit' => Yii::t(),
//            'geo_user' => Yii::t(),
//            'group_template' => Yii::t(),
//            'lang' => Yii::t(),
//            'ms_survey' => Yii::t(),
//            'position' => Yii::t(),
//            'question' => Yii::t(),
            'questionary' => Yii::t('app', 'Шаблоны анкет'),
//            'questionary_lang' => Yii::t(),
//            'question_answer' => Yii::t(),
//            'question_check' => Yii::t(),
//            'question_lang' => Yii::t(),
//            'scenario' => Yii::t(),
            'survey' => Yii::t('app', 'Анкетирование'),
//            'survey_lang' => Yii::t(),
//            'survey_filial' => Yii::t(),
//            'tariff' => Yii::t(),
//            'task' => Yii::t(),
//            'task_answer' => Yii::t(),
//            'task_lang' => Yii::t(),
            'user' => Yii::t('app', 'User ID'),
        ];
        return $array;
    }

    public function getTableLabel($table)
    {
        $list = $this->getTableLabels();
        return $list[$table];
    }

    public function getChangelogActionLabel($action)
    {
        $list = $this->getChangelogActions();
        if ($list) {
            return $list[$action];
        }
    }

    public function getSurveyStatuses()
    {
        return [
            MsSurvey::STATUS_NEW => Yii::t('app', 'Новые'),
            MsSurvey::STATUS_MS_ASSIGNED => Yii::t('app', 'Назначено'),
            MsSurvey::STATUS_IN_PROCESS => Yii::t('app', 'В работе у ТП'),
            MsSurvey::STATUS_MODERATION => Yii::t('app', 'На модерации'),
            MsSurvey::STATUS_MODERATION_START => Yii::t('app', 'В работе у Супервайзера'),
            MsSurvey::STATUS_COMPLETED => Yii::t('app', 'Выполнено'),
        ];
    }

    public function getSurveyStatusesForMS()
    {
        return [
            MsSurvey::STATUS_NEW => Yii::t('app', 'Новые'),
            MsSurvey::STATUS_MS_ASSIGNED => Yii::t('app', 'Новые'),
            MsSurvey::STATUS_IN_PROCESS => Yii::t('app', 'В работе'),
            MsSurvey::STATUS_MODERATION => Yii::t('app', 'На модерации'),
            MsSurvey::STATUS_MODERATION_START => Yii::t('app', 'На модерации'),
            MsSurvey::STATUS_COMPLETED => Yii::t('app', 'Выполнено'),
        ];
    }

    public function getSurveyStatusesForSupervisor()
    {
        return [
            MsSurvey::STATUS_NEW => Yii::t('app', 'Новые'),
            MsSurvey::STATUS_MS_ASSIGNED => Yii::t('app', 'Назначено'),
            MsSurvey::STATUS_IN_PROCESS => Yii::t('app', 'В работе'),
            MsSurvey::STATUS_MODERATION => Yii::t('app', 'На модерации'),
            MsSurvey::STATUS_MODERATION_START => Yii::t('app', 'В работе у Супервайзера'),
            MsSurvey::STATUS_COMPLETED => Yii::t('app', 'Опубликовано'),
        ];
    }

    public function getSurveyStatusLabel($answer, $type = null)
    {
        if ($type) {
            switch ($type) {
                case self::TYPE_ADMIN:
                    $list = $this->getSurveyStatuses();
                    break;
                case self::TYPE_MS:
                    $list = $this->getSurveyStatusesForMS();
                    break;
                case self::TYPE_SUP:
                    $list = $this->getSurveyStatusesForSupervisor();
                    break;
            }
        } else {
            $list = $this->getSurveyStatuses();
        }
        return $list[$answer];
    }

    public function getRoleListForClient()
    {
        return [
            User::ROLE_CLIENT_USER => Yii::t('app', 'Клиент - пользователь'),
            User::ROLE_SUPERVISOR => Yii::t('app', 'Супервайзер'),
            User::ROLE_MYSTIC => Yii::t('app', 'Тайный покупатель'),
        ];
    }

    public function getRoleListForAdmin()
    {
        return [
            User::ROLE_ADMIN => Yii::t('app', 'Администратор'),
            User::ROLE_SUPERVISOR_GLOBAL => Yii::t('app', 'Глобальный супервайзер'),
            User::ROLE_MYSTIC_GLOBAL => Yii::t('app', 'Глобальный тайный покупатель'),
            User::ROLE_CLIENT_USER => Yii::t('app', 'Клиент-пользователь'),
            User::ROLE_CLIENT_SUPER => Yii::t('app', 'Клиент-Администратор'),
        ];
    }

    public function getMsDateTypeList()
    {
        return [
            LinkMsForm::DATE_SINGLE => Yii::t('app', 'Одна дата'),
            LinkMsForm::DATE_RANGE => Yii::t('app', 'Период'),
            LinkMsForm::DATE_MULTIPLE => Yii::t('app', 'Несколько дат')
        ];
    }

    public function getMsDateTypeLabel($datetype)
    {
        $list = $this->getMsDateTypesList();
        if ($list) {
            return $list[$datetype];
        }
    }

    public function getUploadEntityTypes()
    {
        return [
            UploadManager::TYPE_FILIAL_STRUCTURE => \Yii::t('app', 'Структура объектов'),
            UploadManager::TYPE_FILIALS => \Yii::t('app', 'Объекты'),
            UploadManager::TYPE_EMPLOYEES => \Yii::t('app', 'Сотрудник')
        ];
    }

}
