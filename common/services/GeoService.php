<?php
/**
 * Created by PhpStorm.
 * User: ikorostelev
 * Date: 26.06.18
 * Time: 16:58
 */

namespace common\services;


use common\models\GeoUnit;
use yii\helpers\ArrayHelper;
use Yii;

class GeoService
{
    public function getCountriesAsMap()
    {
        $countries = GeoUnit::findAll(['level' => GeoUnit::LEVEL_COUNTRY]);
        if ($countries) {
            return ArrayHelper::map($countries, 'id', 'name');
        } else {
            return [];
        }
    }

    public function getCities($id_country = null)
    {
        $query = GeoUnit::find();
        if ($id_country) {
            $query->where(['parent_id' => $id_country, 'level' => GeoUnit::LEVEL_CITY]);
        } else {
            $query->where(['level' => GeoUnit::LEVEL_CITY]);
        }
        return $query->orderBy('name ASC')->all();
    }

//    public function getCitiesByCountryAsMap($id_country)
//    {
//        $cities = $this->getCities($id_country);
//        if ($cities) {
//            return ArrayHelper::map($cities, 'id', 'name');
//        } else {
//            return [];
//        }
//    }

    public function getCitiesAsMap($id_country = null)
    {
        $cities = $this->getCities($id_country);
        if ($cities) {
            return ArrayHelper::map($cities, 'id', 'name');
        } else {
            return [];
        }
    }

    public function getCitiesByClientAsMap($id_client)
    {
        $client = Yii::$app->clientService->getClient($id_client);
        return $this->getCitiesAsMap($client->geo_country);
    }

    public function getCityIdsByCountry($id_country)
    {
        $cities = $this->getCities($id_country);
        $result = [];
        foreach ($cities as $item) {
            $result[] = $item->id;
        }
        return $result;
    }

    public function getOrCreateCityByName($countryName, $name)
    {
        $city = GeoUnit::find()->where(['level' => GeoUnit::LEVEL_CITY])->andWhere(['like', 'name', $name])->one();
        if (!$city) {
            $country = GeoUnit::find()->where(['level' => GeoUnit::LEVEL_COUNTRY])->andWhere(['like', 'name', $countryName])->one();
            if ($country) {
                return $this->createUnit($name, GeoUnit::LEVEL_CITY, $country->id);
            } else {
                $newCountry = $this->createUnit($countryName, GeoUnit::LEVEL_COUNTRY);
                if ($newCountry) {
                    return $this->createUnit($name, GeoUnit::LEVEL_CITY, $newCountry->id);
                }
            }
        } else {
            return $city;
        }
    }


    private function createUnit($name, $level, $parent_id = null)
    {
        $model = new GeoUnit();
        $model->name = $name;
        $model->level = $level;
        $model->parent_id = $parent_id;
        if ($model->save()) {
            return $model;
        } else {
            return false;
        }
    }

    public function getCityByName($name)
    {
        return GeoUnit::find()->where(['like', 'name', $name])->andWhere(['level' => GeoUnit::LEVEL_CITY])->one();
    }

}