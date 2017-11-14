<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[ServicerUserInfo]].
 *
 * @see ServicerUserInfo
 */
class ServicerUserInfoQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return ServicerUserInfo[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return ServicerUserInfo|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
