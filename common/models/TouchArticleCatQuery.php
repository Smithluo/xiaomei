<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[TouchArticleCat]].
 *
 * @see TouchArticleCat
 */
class TouchArticleCatQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return TouchArticleCat[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return TouchArticleCat|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
