<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[OrderAction]].
 *
 * @see OrderAction
 */
class OrderActionQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return OrderAction[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return OrderAction|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
