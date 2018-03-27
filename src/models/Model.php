<?php

/*
 * Stock Module for Hipanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-stock
 * @package   hipanel-module-stock
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2016, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\stock\models;

use hipanel\base\Model as YiiModel;
use hipanel\base\ModelTrait;
use Yii;
use yii\helpers\Html;

class Model extends YiiModel
{
    use ModelTrait;

    const SCENARIO_COPY = 'copy';

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'url',
                    'descr',
                    'short',
                    'profile',
                    'last_prices',
                    'model',
                    'type',
                    'types',
                    'type_label',
                    'state',
                    'states',
                    'state_label',
                    'brand',
                    'brands',
                    'brand_label',
                    'tag',
                    'tags',
                    'tags_all',
                    'props',
                    'prop_tag',
                    'prop_tags',
                    'prop_tags_all',
                    'partno',
                    'with_counters',
                    'with_prices',
                    'show_system',
                    'show_hidden_from_user',
                    'dcs',
                    'counters',
                    'dtg',
                    'sdg',
                    'm3',

                    // Chassis
                    'UNITS_QTY',
                    '35_HDD_QTY',
                    '25_HDD_QTY',

                    // Server
                    'units_qty',
                    '35_hdd_qty',
                    '25_hdd_qty',
                    'ram_qty',
                    'cpu_qty',

                    // HDD
                    'FORMFACTOR',

                    // Motherboard
                    'RAM_AMOUNT',
                    'RAM_QTY',
                    'CPU_QTY',

                    // RAM
                    'RAM_VOLUME',
                ],
                'safe',
            ],
            [['is_favourite'], 'boolean'],
            [['id', 'type_id', 'tariff_id', 'profile_id'], 'integer'],
            [
                ['partno', 'model'],
                'match',
                'pattern' => '/^[a-zA-Z0-9+\/.,_ -]+$/',
                'message' => Yii::t('hipanel:stock', 'Field "{attribute}" can contain Latin characters, numbers, space, plus, dot'),
                'on' => ['create', 'update'],
            ],

            // Delete
            [['id'], 'integer', 'on' => ['delete']],

            // Create
            [['type', 'brand', 'model', 'partno'], 'required', 'on' => 'create'],

            // Update
            [['model', 'partno'], 'required', 'on' => 'update'],

            // Hide & Show
            [['id'], 'required', 'on' => ['mark-hidden-from-user', 'unmark-hidden-from-user']],

            // Copy
            [['type', 'brand', 'model', 'partno'], 'required', 'on' => 'copy'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return $this->mergeAttributeLabels([
            'partno' => Yii::t('hipanel:stock', 'Part No.'),
            'show_hidden_from_user' => Yii::t('hipanel:stock', 'Show hidden'),
            'profile' => Yii::t('hipanel:stock', 'Group'),
            // Chassis
            'UNITS_QTY' => Yii::t('hipanel:stock', 'Units'),
            '35_HDD_QTY' => Yii::t('hipanel:stock', '3.5" HDD'),
            '25_HDD_QTY' => Yii::t('hipanel:stock', '2.5" HDD'),
            // Server
            'units_qty' => Yii::t('hipanel:stock', 'Units'),
            '35_hdd_qty' => Yii::t('hipanel:stock', '3.5" HDD'),
            '25_hdd_qty' => Yii::t('hipanel:stock', '2.5" HDD'),
            'ram_qty' => Yii::t('hipanel:stock', 'RAM slots'),
            'cpu_qty' => Yii::t('hipanel:stock', 'CPU quantity'),
            // CPU
            'prop_tags' => Yii::t('hipanel:stock', 'Tags'),
            // HDD
            'FORMFACTOR' => Yii::t('hipanel:stock', 'Form factor'),
            // Motherboard
            'RAM_AMOUNT' => Yii::t('hipanel:stock', 'Max RAM'),
            'RAM_QTY' => Yii::t('hipanel:stock', 'RAM slots'),
            'CPU_QTY' => Yii::t('hipanel:stock', 'CPU sockets'),
            // RAM
            'RAM_VOLUME' => Yii::t('hipanel:stock', 'RAM volume'),
            'dcs' => Yii::t('hipanel:stock', 'DCS'),
            'brand' => Yii::t('hipanel:stock', 'Brand'),
            'model' => Yii::t('hipanel:stock', 'Model'),
            'model_types' => Yii::t('hipanel:stock', 'Model types'),
            'model_' => Yii::t('hipanel:stock', 'Model types'),
            'short' => Yii::t('hipanel:stock', 'Short'),
            'tags' => Yii::t('hipanel:stock', 'Tags'),
            /// STOCKS
            'dtg' => Yii::t('hipanel:stock', 'DTG'),
            'sdg' => Yii::t('hipanel:stock', 'SDG'),
            'm3' => Yii::t('hipanel:stock', 'M3'),
            'twr' => Yii::t('hipanel:stock', 'TWR'),
        ]);
    }

    public function renderReserves($dc)
    {
        $out = '';
        if (!empty($this->counters[$dc]['stock'])) {
            $out .= Html::tag('b', $this->counters[$dc]['stock'], ['title' => 'In stock']);
        }
        if (!empty($this->counters[$dc]['reserved'])) {
            $out .= '+' . Html::tag('b', $this->counters[$dc]['reserved'], ['class' => 'text-info', 'title' => 'Reserved']);
        }
        if (!empty($this->counters[$dc]['uu'])) {
            $out .= '+' . Html::tag('b', $this->counters[$dc]['uu'], ['class' => 'text-success', 'title' => 'Unused']);
        }
        if (!empty($this->counters[$dc]['rma'])) {
            $out .= '/' . Html::tag('b', $this->counters[$dc]['rma'], ['class' => 'text-danger', 'title' => 'RMA']);
        }

        return $out;
    }

    public function showModelPrices($data, $delimiter = ' /&nbsp;')
    {
        $prices = [];
        if (is_array($data)) {
            foreach ($data as $currency => $price) {
                $prices[] = Yii::$app->formatter->format($price, ['currency', $currency]);
            }

            return implode($delimiter, $prices);
        }

        return '';
    }
}
