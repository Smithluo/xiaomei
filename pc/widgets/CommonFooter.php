<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-08-22
 * Time: 17:43
 */

namespace pc\widgets;

use common\models\ArticleCat;

class CommonFooter extends BaseWidget
{
    public function run()
    {
        parent::run();

        $articleCatList = ArticleCat::find()->alias('articleCat')->with([
            'articleList' => function ($query) {
                $query->andWhere([
                    'is_open' => 1,
                ])->orderBy([
                    'sort_order' => SORT_DESC,
                ]);
            },
        ])->where([
            'cat_type' => 1,
            'show_in_nav' => 1,
        ])->orderBy([
            'articleCat.sort_order' => SORT_DESC,
        ])->all();

        return $this->render('/widgets/commonFooter', [
            'articleCatList' => $articleCatList,
        ]);
    }
}