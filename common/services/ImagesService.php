<?php

namespace common\services;

use Yii;
use yii\base\Exception;
use maxlapko\components\handler\drivers\ImageMagic;
use yii\web\UploadedFile;

/**
 * сервис для обработки  изображений
 */
class ImagesService {

    const RESIZE_WIDTH = 1024;
    const RESIZE_HEIGHT = 768;
    const THUMB_WIDTH = 320;
    const THUMB_HEIGHT = 320;

    public function init()
    {
        if (!\Yii::$app->has("imageHandler", false))
            throw new ImagesServiceException("Не подключен компонент imageHandler");
    }

    /**
     * Получение изображения изображения из POST
     * 
     * @param Model $model
     * @param string $field
     * @param string $folder
     * @param string $old_file_name
     * @return string
     */
    public function loadImage($model, $field, $folder, $old_file_name = null)
    {

        if ($file = UploadedFile::getInstance($model, $field)) {
            if ($file !== null) {
                return $this->saveImage($file, $folder, $old_file_name);
            }
        }

        return $old_file_name ? $old_file_name : null;
    }

    /**
     * Сохранение изображения
     * 
     * @param UploadedFile $file
     * @param string $folder
     * @param string $old_file_name
     * @param string $setname //имя файла передается зараннее
     * @return string
     */
    public function saveImage($file, $folder, $old_file_name = null, $beforename = null)
    {
        $basePath = Yii::getAlias('@common/') . "uploads";
        $fullPath = "{$basePath}/{$folder}";

        \Yii::$app->imagesService->checkDir($basePath);

        \Yii::$app->imagesService->checkDir($fullPath);

        if ($old_file_name && $old_file_name != '') {

            /**
             * пробуем удалить главную картинку
             */
            $this->deleteFile("{$fullPath}/{$old_file_name}");
            /**
             * пробуем удалить тумбу
             */
            $this->deleteFile("{$fullPath}/thumb/{$old_file_name}");
        }

        $newname = empty($beforename) ? $this->generateImageName($file) : $beforename;
        if (is_array($file)) {
            foreach ($file as $f) {
                if ($f instanceof UploadedFile) {
                    $f->saveAs($fullPath . "/" . $newname);
                }
            }
        } else {
            $file->saveAs($fullPath . "/" . $newname);
        }
        \Yii::$app->imagesService->processing([
            'filepath' => $fullPath . "/",
            'file' => $newname,
            'resize_width' => 1024,
            'resize_height' => 768,
            'thumb_width' => 150,
            'thumb_height' => 150,
        ]);
        return $newname;
    }

    /**
     * удаление изображения
     * 
     * @param string $filename
     * @param string $folder
     * @return boolean
     */
    public function removeImage($filename, $folder)
    {
        if ($filename !== '') {
            $basePath = Yii::getAlias('@common/') . "uploads";
            $fullPath = "{$basePath}/{$folder}";

            $this->deleteFile("{$fullPath}/{$filename}");
            $this->deleteFile("{$fullPath}/thumb/{$filename}");


            return true;
        } else {
            return false;
        }
    }

    /**
     * Возвращает новое имя файла
     * @param UploadedFile $file
     */
    public function generateImageName($file)
    {
        $path_info = pathinfo($file);
        return time() . "-" . $path_info['filename'] . "." . strtolower($path_info['extension']
        );
    }

    /**
     * обрабатываем изображение, создаем тубму и кропим ее если надо
     * @param type $params
     * @return type
     */
    public function processing($params)
    {

        $filePath = $this->getParam($params, 'filepath');
        $outputFilePath = $this->getParam($params, 'output_filepath');

        $file = $this->getParam($params, 'file');

        $resizeWidth = $this->getParam($params, 'resize_width');
        $resizeHeight = $this->getParam($params, 'resize_height');
        $thumbWidth = $this->getParam($params, 'thumb_width');
        $thumbHeight = $this->getParam($params, 'thumb_height');
        $watermarkFilePath = $this->getParam($params, 'watermark_filepath');
        $crop = $this->getParam($params, 'crop');

        if (!file_exists($filePath . $file))
            return;

        if ($outputFilePath) {
            Yii::$app->imageHandler->load($filePath . $file)->save($outputFilePath . $file);
            $filePath = $outputFilePath;
        }

        $thumbPath = $filePath . 'thumb/';

        if (!$filePath)
            throw new ImagesServiceException('Неуказан путь до папки с изображениями ');
        if (!$file)
            throw new ImagesServiceException('Не указано имя фаила изображения');

        if (!$resizeWidth)
            $resizeWidth = self::RESIZE_WIDTH;
        if (!$resizeHeight)
            $resizeWidth = self::RESIZE_HEIGHT;
        if (!$thumbWidth)
            $thumbWidth = self::THUMB_WIDTH;
        if (!$thumbHeight)
            $thumbHeight = self::THUMB_HEIGHT;
        if (!$crop)
            $crop = true;

        if (!$watermarkFilePath)
            $watermarkFilePath = Yii::getAlias('@common/images/watermarkLight.png');

        $this->checkDir($filePath);

        $this->resizeImage($filePath . $file, $resizeWidth, $resizeHeight);

        $this->checkDir($thumbPath);

        $this->createThumb($filePath . $file, $thumbPath . $file, $thumbWidth, $thumbHeight, $crop);

        //$this->setWatermark($filePath.$file, $watermarkFilePath);
    }

    /**
     * создаем тумбу
     * @param type $file
     * @param type $resizeWidth
     * @param type $resizeHeight
     * @param type $proportional
     * @throws ImagesServiceException
     */
    public function createThumb($file, $thumb, $resizeWidth = self::THUMB_WIDTH, $resizeHeight = self::THUMB_HEIGHT, $crop = true, $proportional = true)
    {
        if (!file_exists($file))
            throw new ImagesServiceException('Файл не найден');
        Yii::$app->imageHandler->load($file);
        Yii::$app->imageHandler->resize($resizeWidth, $resizeHeight, $proportional)
                ->save($thumb);

        if ($crop) {
            Yii::$app->imageHandler->adaptiveThumb($resizeWidth, $resizeHeight)
                    ->save($thumb);
        }
    }

    /**
     * изменить размер заданного изображения
     * @param type $file
     * @param type $resizeWidth
     * @param type $resizeHeight
     * @param type $proportional
     * @throws ImagesServiceException
     */
    public function resizeImage($file, $resizeWidth = self::RESIZE_WIDTH, $resizeHeight = self::RESIZE_HEIGHT, $proportional = true)
    {
        if (!file_exists($file))
            throw new ImagesServiceException('Файл не найден');
        Yii::$app->imageHandler->load($file)
                ->resize($resizeWidth, $resizeHeight, $proportional)
                ->save($file);
    }

    /**
     * кроп изображения
     * @param type $file
     * @param type $cropWidth
     * @param type $cropHeight
     * @param type $startX
     * @param type $startY
     * @throws ImagesServiceException
     */
    public function cropImage($file, $cropWidth = self::THUMB_WIDTH, $cropHeight = self::THUMB_HEIGHT, $startX = false, $startY = false)
    {
        if (!file_exists($file))
            throw new ImagesServiceException('Файл не найден');
        Yii::$app->imageHandler->load($file)
                ->crop($cropWidth, $cropHeight, $startX, $startY)
                ->save($file);
    }

    /**
     * добавить водяной знак
     * @param type $file
     * @param type $watermarkFile
     * @param type $offsetX
     * @param type $offsetY
     * @param type $corner
     * @throws ImagesServiceException
     */
    public function setWatermark($file, $watermarkFile, $offsetX = 0, $offsetY = 0, $corner = ImageMagic::CORNER_CENTER)
    {
        if (!file_exists($file))
            throw new ImagesServiceException('Файл не найден');
        Yii::$app->imageHandler->load($file)
                ->watermark($watermarkFile, $offsetX, $offsetY, $corner)
                ->save($file);
    }

    public function checkDir($path)
    {
        if (!file_exists($path)) {
            mkdir($path);
        }
    }

    /**
     * При загрузке нового фаила или удалении модели удаляем фаилы
     */
    public function deleteFile($path)
    {
        if (file_exists($path)) {
            unlink($path);
        }
    }

    public function getParam($params, $name)
    {

        if (!empty($params) && is_array($params)) {
            if (!empty($params[$name])) {
                return $params[$name];
            }
        }

        return null;
    }

}

class ImagesServiceException extends Exception {

    public function getName()
    {
        return 'Images Service exception';
    }

}
