<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[BackOrder]].
 *
 * @see BackOrder
 */
class BackOrderQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return BackOrder[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return BackOrder|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
