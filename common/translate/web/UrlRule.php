<?php
/**
 * @link https://github.com/LAV45/yii2-translated-behavior
 * @copyright Copyright (c) 2015 LAV45!
 * @author Alexey Loban <lav451@gmail.com>
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

namespace lav45\translate\web;

/**
 * Class UrlRule
 * @package lav45\translate\web
 */
class UrlRule extends \yii\web\UrlRule
{
    use LanguageUrlTrait;

    /**
     * @inheritdoc
     */
    public function createUrl($manager, $route, $params)
    {
        $params = $this->checkLanguageParams($params);
        return parent::createUrl($manager, $route, $params);
    }
}