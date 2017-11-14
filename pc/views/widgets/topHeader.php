<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-08-22
 * Time: 18:02
 */
?>

<div id="commonHeader" class="header clearfix">
    <div class="nav_search"><a href="/" class="nav_logo"></a>
        <div class="search">
            <div class="searchForm">
                <input autocomplete="off" id="key" placeholder="搜索 采购商品/品牌" class="text">
                <button class="button"><i></i>搜索</button>
            </div>
            <ul>
                <?php foreach ($keywordsList as $keyword): ?>
                <li><a href="<?= \yii\helpers\Url::to(['goods/index', 'keywords' => $keyword]) ?>"><?= $keyword ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="nav_basket">
            <div class="basket_num">
                <div class="bst_img"></div>
                <div class="bst_info">
                    <a href="<?= \yii\helpers\Url::to(['flow/index']) ?>">
                        <p>我的采购车</p>
                        <div class="cart_intro"><i class="cart_num"></i><em>{insert name='cart_num'}</em></div>
                    </a>
                </div>
            </div>
        </div>
        <!--增加装饰、渲染氛围  @garaaluo-->
        <div class="happyNewYear"></div>
    </div>
    <div class="nav_bar_bg">
        <div class="nav_bar">
            <div class="f_icon"><a href="<?= \yii\helpers\Url::to(['category/index']) ?>" target="_blank"><span>选品导购</span></a></div>
            <ul>
                <li style="padding: 0 20px;margin-left: 24px;" {if $curPage == 'index'}class="active"{/if}><a href="/" target="_blank">首页</a></li>
                <li class="li_img gt"><a target="_blank" href="<?= \yii\helpers\Url::to(['site/zhifa']) ?>" {if $curPage == 'zhifa'}class="active"{/if}>小美直发</a></li>
                <li class="li_img pp"><a target="_blank" href="<?= \yii\helpers\Url::to(['activity/index']) ?>" {if $curPage == 'activity_hot'}class="active"{/if}>活动中心</a></li>
                <li class="li_img hp"><a target="_blank" href="<?= \yii\helpers\Url::to(['coupon/index']) ?>" {if $curPage == 'coupon_center'}class="active"{/if}>领券中心</a></li>
                <li class="li_img hp"><a target="_blank" href="<?= \yii\helpers\Url::to(['article/academy']) ?>" {if $curPage == 'beauty_academy'}class="active"{/if}>小美学院</a></li>
            </ul>
            <div class="lev2_list">
                <a target="_blank" href="<?= \yii\helpers\Url::to(['goods/quick']) ?>">快速采购</a>
                <a target="_blank" href="<?= \yii\helpers\Url::to(['goods/rank']) ?>">小美榜单</a>
                <a target="_blank" href="<?= \yii\helpers\Url::to(['brand/lib']) ?>">品牌库</a>
                <a target="_blank" href="<?= \yii\helpers\Url::to(['goods/exchange']) ?>">积分商城</a>
            </div>
        </div>
    </div>
</div>