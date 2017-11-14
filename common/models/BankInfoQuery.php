<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[BankInfo]].
 *
 * @see BankInfo
 */
class BankInfoQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return BankInfo[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return BankInfo|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
