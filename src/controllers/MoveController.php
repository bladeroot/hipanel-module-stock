<?php

/*
 * Stock Module for Hipanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-stock
 * @package   hipanel-module-stock
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2016, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\stock\controllers;

use hipanel\actions\Action;
use hipanel\actions\ComboSearchAction;
use hipanel\actions\IndexAction;
use hipanel\actions\SmartPerformAction;
use hipanel\base\CrudController;
use hipanel\filters\EasyAccessControl;
use Yii;

class MoveController extends CrudController
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            [
                'class' => EasyAccessControl::class,
                'actions' => [
                    'delete' => 'move.delete',
                    'index' => 'move.read',
                    '*' => 'move.get-directions',
                ],
            ],
        ]);
    }

    public function actions()
    {
        return array_merge(parent::actions(), [
            'index' => [
                'class' => IndexAction::class,
                'findOptions' => ['with_parts' => 1],
                'data' => function ($action) {
                    return [
                        'types' => $action->controller->getTypes(),
                        'moveTypes' => $action->controller->getMoveTypes(),
                    ];
                },
            ],
            'directions-list' => [
                'class' => ComboSearchAction::class,
                'on beforeSave' => function ($event) {
                    /** @var Action $action */
                    $action = $event->sender;
                    $action->dataProvider->query->action('get-directions');
                },
            ],
            'delete' => [
                'class' => SmartPerformAction::class,
                'success' => Yii::t('hipanel', 'Deleted'),
            ],
        ]);
    }

    public function getTypes()
    {
        return $this->getRefs('type,model', 'hipanel:stock');
    }

    public function getMoveTypes()
    {
        return $this->getRefs('type,move', 'hipanel:stock', ['orderby' => 'no_asc', 'with_recursive' => true]);
    }
}
