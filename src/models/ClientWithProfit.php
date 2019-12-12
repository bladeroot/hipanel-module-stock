<?php
/**
 * Client module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-client
 * @package   hipanel-module-client
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\stock\models;


use hipanel\base\ModelTrait;
use hipanel\modules\client\models\Client;
use hipanel\modules\stock\helpers\ProfitColumns;

/**
 * Class Client
 * @package hipanel\modules\client\models
 */
class ClientWithProfit extends Client implements ProfitModelInterface
{
    use ModelTrait;

    /**
     * @inheritDoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [
                [
                    'currency',
                    'total_price',
                    'unused_price',
                    'stock_price',
                    'rma_price',
                    "rent_price",
                    "rent_charge",
                    "leasing_price",
                    "leasing_charge",
                    "buyout_price",
                    "buyout_charge",
                ],
                'safe',
            ]
        ]);
    }

    /**
     * @inheritDoc
     */
    public function attributeLabels()
    {
        return $this->mergeAttributeLabels(ProfitColumns::getLabels());
    }
}