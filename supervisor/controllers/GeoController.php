<?php

namespace supervisor\controllers;

use Yii;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * GeoController implements the CRUD actions for GeoUnit model.
 */
class GeoController extends BaseController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }


    public function actionGetCities($id_country)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return Yii::$app->geoService->getCitiesAsMap($id_country);
    }
}
