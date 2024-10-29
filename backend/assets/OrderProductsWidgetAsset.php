<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class OrderProductsWidgetAsset extends AssetBundle
{
    public $sourcePath = '@backend/assets/order-products';
    public $css = [
        'order-products.css?v=1',
    ];
    public $js = [
        'order-products.js?v=1',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\web\JqueryAsset',
    ];
}
