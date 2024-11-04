<?php

namespace backend\models;

use common\models\BaseActiveRecord;
use common\models\Product;
use common\validators\DbDateValidator;
use common\validators\RequiredIntegerValidator;
use Yii;
use yii\base\Model;

/**
 * Report Dev model
 *
 * ReportDev model
 * @property string $client
 * @property string $date
 * @property int $product1
 * @property int $product2
 * @property int $product3
 * @property int $product4
 * @property int $product5
 * @property int $product6
 * @property int $product7
 * @property int $product8
 * @property int $product9
 * @property int $product10
 * @property int $product11
 * @property int $product12
 * @property int $product13
*/
class ReportDev extends BaseActiveRecord
{

    public $period;
    public $periodStart;
    public $periodEnd;
    public $region_id;

    public static $attributeLabels = [
        "client" => "Клиент",
    ];

    static $columns;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'report_dev';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['client', 'phone'], 'string', 'max' => 250],
            [['region_id', 'number', 'sum'], 'integer'],
        ];
    }

    /**
     * @return string[]
     */
    public function attributeLabels()
    {
        return self::$attributeLabels;
    }

    /**
     * @return void
     * @throws \yii\db\Exception
     */
    public static function createView()
    {
        $products = Product::find()->all();
        $alias = [];
        $ptr = 0;
        foreach ($products as $product) {
            $alias[$product->name] = 'product' . $ptr++;
        }

        $sql = 'CREATE OR REPLACE 
            ALGORITHM = MERGE 
            DEFINER = CURRENT_USER 
            SQL SECURITY INVOKER
        VIEW report_dev
        AS
            SELECT o.client';

        self::$columns = [[ 'attribute' => 'client', 'filter' => false, 'footer' => 'ИТОГО' ]];

        /** @var  $product  Product */
        foreach ($products as $product) {
            $productname = $alias[$product->name];
            $sql .= ",SUM(CASE WHEN op.product_id = ".$product->id." THEN op.q END) '".$productname."'";
            self::$attributeLabels[$productname] = $product->name;
            self::$columns[] = [
                'attribute' => $productname,
                'filter' => false,
                'value' => function($model, $key, $index, $obj) use ($productname) {
                    $obj->footer += $model[$productname];
                    return $model[$productname];
                }
            ];
        }
        $sql .= ', FALSE as is_deleted, 1 as id, o.date as date, o.region_id as region_id';
        $sql .= ' FROM orders o LEFT JOIN orders_products op ON op.order_id = o.id 
            LEFT JOIN products p ON p.id = op.product_id GROUP BY o.region_id, o.client';

        Yii::$app->db->createCommand($sql)->execute();

    }

    /**
     * @inheritDoc
     */
    public function search($params, $recsOnPage = 20)
    {
        $this->region_id = (int)trim($params['region'] ?? 0);
        $this->period = trim($params['period'] ?? '');

        $dataProvider = parent::search($params, $recsOnPage);
        if ($dataProvider) {
            $p = [];
            if (preg_match('/([0-9]{1,2})\.([0-9]{1,2})\.([0-9]{4})[^0-9]+([0-9]{1,2})\.([0-9]{1,2})\.([0-9]{4})/', $this->period, $p)) {
                $this->periodStart = $p[3] . '-' . $p[2] . '-' . $p[1];
                $this->periodEnd = $p[6] . '-' . $p[5] . '-' . $p[4];
            }

            $query = $dataProvider->query;
            
            if ($this->periodStart) {
                $query->andWhere(['>=', 'date', $this->periodStart]);
            }
            if ($this->periodEnd) {
                $query->andWhere(['<=', 'date', $this->periodEnd]);
            }
            if ($this->region_id) {
                $query->andWhere(['region_id' => $this->region_id]);
            }
        }
        return $dataProvider;
    }

    public static function getColumns()
    {
        return self::$columns;
    }
}