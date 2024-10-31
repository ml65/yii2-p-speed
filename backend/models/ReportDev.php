<?php

namespace backend\models;
use common\Cache;
use common\models\BaseActiveRecord;
use common\models\Product;
use common\validators\DbDateValidator;
use common\validators\RequiredIntegerValidator;
use Couchbase\SearchSort;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use yii\base\Model;
use yii\data\SqlDataProvider;

/**
 * Report Dev model
 *
 * @property integer $id
 * @property string $name
 */
class RepordDev extends Model
{
    public $period;
    public $region_id;
    public $periodStart;
    public $periodEnd;

    public $sqlProvider;


    public function init()
    {
        $totalCount = Yii::$app->db->createCommand('SELECT COUNT(*) FROM orders GROUP BY client ')
            ->queryScalar();

        $products = Product::find()->all();
        $sql = 'SELECT o.client';
        $columns = ['client'];
        $columns2 = [];
        /** @var  $product  Product */
        foreach ($products as $product) {
            $sql .= ",SUM(CASE WHEN op.product_id = ".$product->id." THEN op.q END) '".$product->name."'";
            $columns[] = $product->name;
        }
        $sql .= ' FROM orders o LEFT JOIN orders_products op ON op.order_id = o.id LEFT JOIN products p ON p.id = op.product_id GROUP BY o.client';

        $this->sqlProvider = new SqlDataProvider([
            'sql' => $sql,
            'sort' => [
                'attributes' => $columns
            ],
            'totalCount' => $totalCount
        ]);

    }

    public function formName()
    {
        return "ReportDev";
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
        return [
            'id'            => 'ID',
            'period'        => 'Период',
            'order-period'  => 'Период ордеров',
            'client'        => 'ФИО клиента',
            'region_id'     => 'Район',
            'text'          => 'Текст'
        ];
    }


    public function search($params, $recsOnPage = 20)
    {
        $this->region_id = (int)trim($params['region'] ?? 0);
        $this->text = trim($params['text'] ?? '');
        $this->period = trim($params['period'] ?? '');

        $dataProvider = $this->sqlProvider;

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

            if ($this->text) {
                $query->andWhere(['OR',
                    ['like', 'client', $this->text],
                ]);
            }
        }


        return $dataProvider;
    }

    public function _getHeaderStyle() {
        return array(
            'alignment' => array(
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
                'wrap' => TRUE,
            ),
            'font' => array('bold' => true),
            'fill' => array(
                'fillType'       => Fill::FILL_SOLID,
                'startColor' => array(
                    'argb' => 'FFF4CCCC',
                )
            )
        );
    }

    public function _getRowStyle() {
        return array(
            'alignment' => array(
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
                'wrap' => TRUE,
            ),
            'numberFormat' => array('code' => NumberFormat::FORMAT_NUMBER)
        );
    }




}