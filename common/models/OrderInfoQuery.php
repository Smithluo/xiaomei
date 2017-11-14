<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[OrderInfo]].
 *
 * @see OrderInfo
 */
class OrderInfoQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return OrderInfo[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return OrderInfo|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
