<?php

namespace backend\controllers;

use backend\models\ReportDevSearch;
use common\controllers\RefController;
use common\models\BaseActiveRecord;
use common\models\Product;
use Yii;
use yii\data\SqlDataProvider;
use yii\filters\AccessControl;

class ReportDevController extends RefController
{
    public $recsOnPage = 50;
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionIndex()
    {
        $params = Yii::$app->request->queryParams;

        $searchProvider = new ReportDevSearch();
        $sqlProvider = $searchProvider->search($params, $this->recsOnPage);
        $columns = $searchProvider->getColumns();

        return $this->render('index',['sqlProvider' => $sqlProvider, 'searchProvider' => $searchProvider, 'columns' => $columns]);
    }
}
