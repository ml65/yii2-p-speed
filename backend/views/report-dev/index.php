<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\widgets\Card;

/* @var $this yii\web\View */
/* @var $sqlProvider yii\data\ActiveDataProvider */
/* @var $searchProvider yii\data\ActiveDataProvider */
/* @var $columns array */

$this->title = 'SQL Data Provider Demo';
$this->params['breadcrumbs'][] = $this->title;
$this->title = Yii::$app->urlManager->getLastTitle();

?>

<?php Card::begin([]); ?>


<?php echo $this->render('_search', ['model' => $searchProvider]);  ?>

<?= GridView::widget([
        'dataProvider'  => $sqlProvider,
        'filterModel'   => $searchProvider,
        'columns'       => $columns,
        'showFooter'    => TRUE
        ]);
?>

<?php Card::end(); ?>

