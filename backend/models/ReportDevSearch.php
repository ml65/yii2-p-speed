<?php

namespace backend\models;
use common\Cache;
use common\models\BaseActiveRecord;
use common\models\Product;
use common\validators\DbDateValidator;
use common\validators\RequiredIntegerValidator;
use Couchbase\SearchSort;
use Yii;
use yii\base\Model;
use yii\data\SqlDataProvider;

/**
 * Report Dev model
 *
 * @property integer $id
 * @property string $name
 * @property integer $region_id
 * @property string $period
 */
class ReportDevSearch extends Model
{
    public $period = '';
    public $text = '';
    public $periodStart = '';
    public $periodEnd = '';
    public $region_id = '';
    public $client = '';
    public $columns;
    public $query;
    public $totalCount;
    public $product1;
    public $product2;
    public $product3;
    public $product4;
    public $product5;
    public $product6;
    public $product7;
    public $product8;
    public $product9;
    public $product10;
    public $product11;
    public $product12;
    public $product13;

    public static $attributeLabels = [
        'id'            => 'ID',
        'period'        => 'Период',
        'order-period'  => 'Период ордеров',
        'client'        => 'ФИО клиента',
        'region_id'     => 'Район',
        'text'          => 'Текст'
    ];

    public function search($params, $recsOnPage = 20)
    {
        $this->region_id = (int)trim($params['region'] ?? 0);
        $this->period = trim($params['period'] ?? '');
        return $this->getDataProvider($params);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['client','text', 'period'], 'string', 'max' => 250],
            [['region_id'], 'integer'],

        ];
    }

    /**
     * @return string[]
     */
    public function attributeLabels()
    {
        return self::$attributeLabels;
    }

    public function getAttributeLabel($attribute)
    {
        if (array_key_exists($attribute, self::$attributeLabels)) {
            return self::$attributeLabels[$attribute];
        } else {
            return "";
        }
    }

    public function getDataProvider($params)
    {
        $products = Product::find()->all();

        $product2 = Product::find()->where('id',1);
        // готовим алиасы
        $alias = [];
        $ptr = 0;
        foreach ($products as $product) {
            $alias[$product->name] = 'product' . $ptr++;
        }

        $sql = 'SELECT o.client';
        $sqlCount = 'SELECT COUNT(o.client)';
        $this->columns = [ [ 'attribute' => 'client', 'filter' => false, 'footer' => 'ИТОГО' ]];

        /** @var  $product  Product */
        foreach ($products as $product) {
            $productname = $alias[$product->name];
            $sql .= ",SUM(CASE WHEN op.product_id = ".$product->id." THEN op.q END) '".$productname."'";
            $this->columns[] = [
                'attribute' => $productname,
                'filter' => false,
                'value' => function($model, $key, $index, $obj) use ($productname) {
                    $obj->footer += $model[$productname];
                    return $model[$productname];
                }
            ];
            self::$attributeLabels[$productname] = $product->name;
        }

        $sql .= ' FROM orders o LEFT JOIN orders_products op ON op.order_id = o.id LEFT JOIN products p ON p.id = op.product_id';

        $p = [];
        $dop = '';
        if (preg_match('/([0-9]{1,2})\.([0-9]{1,2})\.([0-9]{4})[^0-9]+([0-9]{1,2})\.([0-9]{1,2})\.([0-9]{4})/', $this->period, $p)) {
            $this->periodStart = $p[3] . '-' . $p[2] . '-' . $p[1];
            $this->periodEnd = $p[6] . '-' . $p[5] . '-' . $p[4];
        }
        if ($this->periodStart) {
            $dop = "o.date >= '$this->periodStart'";
        }
        if ($this->periodEnd) {
            if ($dop) $dop .= " AND ";
            $dop .= "o.date <= '$this->periodEnd'";
        }
        if ($this->region_id) {
            if ($dop) $dop .= " AND ";
            $dop .= "o.region_id = $this->region_id";
        }
        if ($dop) {
            $sql .= " WHERE " . $dop;
        }

        $sql .= ' GROUP BY o.client';

        $sqlProvider = new SqlDataProvider([
            'sql' => $sql,
            'sort' => [
                'attributes' => $this->columns
            ],
        ]);

        return $sqlProvider;
    }

    public function getColumns()
    {
        return $this->columns;
    }

}