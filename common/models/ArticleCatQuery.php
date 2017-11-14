<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[ArticleCat]].
 *
 * @see ArticleCat
 */
class ArticleCatQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return ArticleCat[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return ArticleCat|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
