<?php

namespace common\models\payment;

/**
 * This is the ActiveQuery class for [[TouchPayment]].
 *
 * @see TouchPayment
 */
class TouchPaymentQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return TouchPayment[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return TouchPayment|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
