<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[CashRecord]].
 *
 * @see CashRecord
 */
class CashRecordQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return CashRecord[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return CashRecord|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
