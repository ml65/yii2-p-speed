<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\widgets\Card;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel backend\models\ReportDev */
/* @var $searchProvider yii\data\ActiveDataProvider */

$this->title = 'SQL Data Provider Demo';
$this->params['breadcrumbs'][] = $this->title;
$this->title = Yii::$app->urlManager->getLastTitle();

?>

<?php Card::begin([]); ?>

<?php echo $this->render('_search', ['model' => $searchModel]);  ?>

<?= GridView::widget([
        'dataProvider'  => $dataProvider,
        'filterModel'   => $dataProvider,
        'columns'       => $searchModel::getColumns(),
        'showFooter'    => TRUE
        ]);
?>

<?php Card::end(); ?>

