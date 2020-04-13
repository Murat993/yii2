<?php
/**
 * Created by PhpStorm.
 * User: ikorostelev
 * Date: 31.10.2018
 * Time: 10:23
 */

namespace common\services;


use common\helpers\FilesHelper;
use PhpOffice\PhpSpreadsheet\IOFactory;
use yii\web\UploadedFile;

class UploadManager
{
    const TYPE_POSITION = 1;
    const TYPE_EMPLOYEES = 2;
    const TYPE_FILIALS = 3;
    const TYPE_FILIAL_STRUCTURE = 4;

    private $data = [];
    private $entityType;
    private $filepath;
    private $client_id;
    private $processor;

    public function __construct($form)
    {
        $file = UploadedFile::getInstance($form, 'file');
        if ($file) {
            $fileName = \Yii::$app->fileService->saveFile($file, 'imports');
            $basePath = \Yii::getAlias('@common/') . "uploads/imports/";
            $this->filepath = $basePath . $fileName;
            $reader = IOFactory::createReaderForFile($this->filepath);
            $spreadsheet = $reader->load($this->filepath);
            $this->data = $spreadsheet->getActiveSheet()->toArray();
            $this->entityType = $form->type;
            $this->client_id = $form->client_id;
            $this->prepareProcessor();
        } else {
            return false;
        }

    }

    private function extractData($tempName)
    {
        if (($fp = fopen($tempName, 'r')) !== FALSE) {
            while (($line = fgetcsv($fp, 0, ',')) !== FALSE) {
                $this->data[] = $line;
            }
        }
    }

    private function prepareProcessor()
    {
        switch ($this->entityType) {
//            case self::TYPE_POSITION:
//                $this->processor = \Yii::$app->employeeService;
//                break;
            case self::TYPE_EMPLOYEES:
                $this->processor = \Yii::$app->employeeService;
                break;
            case self::TYPE_FILIALS:
                $this->processor = \Yii::$app->filialService;
                break;
            case self::TYPE_FILIAL_STRUCTURE:
                $this->processor = \Yii::$app->structService;
                break;
        }
    }


    public function saveAll()
    {
        \Yii::info('ENTERED UPLOAD MANAGER');
        $successful = [];
        $errors = [];
        $i = 0;
        foreach ($this->data as $item) {
            if ($i > 0) {
                $transaction = \Yii::$app->db->beginTransaction();
                $result = $this->processor->import($item, $this->client_id);
                if ($result) {
                    $transaction->commit();
                    $successful[] = $result;
                } else {
                    $transaction->rollBack();
                    $errors[] = $result;
                }
            }
            $i++;
        }
        FilesHelper::deleteFile($this->filepath);
        $successfulCount = count($successful);
        $errorsCount = count($errors);
        $totalCount = $successfulCount + $errorsCount;

        \Yii::info("UPLOAD MANAGER. PROCESSED {$totalCount} records, 
        successful = {$successfulCount}, errors = {$errorsCount}");
        return ['success' => $successful, 'error' => $errors];
    }

}