<?php

namespace brand\models;

/**
 * This is the ActiveQuery class for [[AdminUser]].
 *
 * @see AdminUser
 */
class AdminUserQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return AdminUser[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return AdminUser|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
