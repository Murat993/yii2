<?php
/**
 * Created by PhpStorm.
 * User: narims
 * Date: 20.07.18
 * Time: 10:27
 */

namespace common\services;


use Yii;
use common\helpers\FilesHelper;
use yii\web\UploadedFile;

class FileService
{

    public function saveFile($file, $folder, $old_file_name = null, $beforename = null){
        $basePath = Yii::getAlias('@common/') . "uploads";
        $fullPath = "{$basePath}/{$folder}";

        \Yii::$app->imagesService->checkDir($basePath);

        \Yii::$app->imagesService->checkDir($fullPath);

        if ($old_file_name && $old_file_name != '') {

            /**
             * пробуем удалить главную картинку
             */
            FilesHelper::deleteFile("{$fullPath}/{$old_file_name}");
        }

        $newname = empty($beforename) ? $this->generateFileName($file) : $beforename;
        if (is_array($file)) {
            foreach ($file as $f) {
                if ($f instanceof UploadedFile) {
                    $f->saveAs($fullPath . "/" . $newname);
                }
            }
        } else {
            $file->saveAs($fullPath . "/" . $newname);
        }

        return $newname;
    }

    /**
     * Возвращает новое имя файла
     * @param UploadedFile $file
     */
    public function generateFileName($file)
    {
        $path_info = pathinfo($file);
        return time() . "-" . $path_info['filename'] . "." . strtolower($path_info['extension']
            );
    }

}