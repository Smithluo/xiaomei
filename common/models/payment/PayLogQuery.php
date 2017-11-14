<?php

namespace common\models\payment;

/**
 * This is the ActiveQuery class for [[PayLog]].
 *
 * @see PayLog
 */
class PayLogQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return PayLog[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return PayLog|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
