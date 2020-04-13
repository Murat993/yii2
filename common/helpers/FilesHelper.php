<?php

namespace common\helpers;

use yii\helpers\FileHelper;

class FilesHelper extends FileHelper {

    public static function CreateFile($path, $content)
    {
        $file = fopen($path, 'w+');
        if ($file) {
            fwrite($file, $content);
            fclose($file);
        }
    }

    public static function deleteFile($path)
    {
        if (file_exists($path)) {
            unlink($path);
        }
    }

    public static function readFile($path)
    {
        if (!file_exists($path)) {
            return null;
        }

        return file_get_contents($path);
    }

}
