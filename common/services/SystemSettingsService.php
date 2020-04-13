<?php

namespace common\services;

use Yii;
use yii\base\Component;
use \yii\base\Model;
use common\helpers\FilesHelper;
use common\models\SettingForm;

class SystemSettingsService extends Component {

    private $_settingFilePath;

    public function init()
    {
        parent::init();
        $this->_settingFilePath = Yii::getAlias(
                        "@common/uploads/" . 'settings.txt'
        );
        if (!file_exists($this->_settingFilePath)) {
            $defaultContent = $this->getDefault();
            FilesHelper::createFile($this->_settingFilePath, $defaultContent);
        }
    }

    /**
     * Получение настроек
     * 
     * @return Model
     */
    public function getSystemSettings()
    {
        $string = FilesHelper::readFile($this->_settingFilePath);

        $data = unserialize($string);
        if (!$data instanceof Model) {
            throw new SystemSettringServiceException(Yii::t('service.systemsetting', 'Неверные данные настроек.'));
        }
        $model = new SettingForm();
        $model->load($data->attributes, '');

        return $model;
    }

    /**
     * Сохранение настроек
     * 
     * @param Model $form
     */
    public function save(Model $form)
    {
        $content = serialize($form);
        FilesHelper::createFile($this->_settingFilePath, $content);
    }

    /**
     * Возвращает значение атрибута по умолчанию
     * 
     * @param string $attribute
     * @return mixed
     */
    public function getDefaultValue($attribute)
    {
        $string = $this->getDefault();
        $model = unserialize($string);
        if (!isset($model->{$attribute})) {
            return null;
        }

        return $model->{$attribute};
    }

    /**
     * Возвращает серилизованную строку настроек по умолчанию
     * 
     * @return string
     */
    private function getDefault()
    {
        $form = new SettingForm();
        $form->adminEmail = 'noreply@plan.com.kz';
        $form->answerLength = 3;
        return serialize($form);
    }

}
