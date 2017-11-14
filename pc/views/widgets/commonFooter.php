<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-08-22
 * Time: 18:02
 */
?>

<div id="commonFooter" class="footer_bg">
    <div class="ft_intro">
        <ul class="introduce_footer">
            <li><img src="http://js.xiaomei360.com/img/bottom/huoyuan.png">
                <p class="p1">货源正</p>
                <p class="p2">原装进口，品牌授权</p>
            </li>
            <li><img src="http://js.xiaomei360.com/img/bottom/shouxu.png">
                <p class="p1">手续全</p>
                <p class="p2">证件齐全，带中文标</p>
            </li>
            <li style="width:260px"><img src="http://js.xiaomei360.com/img/bottom/jiage.png">
                <p class="p1">价格优</p>
                <p class="p2">集采团购，优势低价</p>
            </li>
            <li style="width:260px"><img src="http://js.xiaomei360.com/img/bottom/piliang.png">
                <p class="p1">批量低</p>
                <p class="p2">一件起批，灵活补货</p>
            </li>
            <li style="width:200px"><img src="http://js.xiaomei360.com/img/bottom/fuwu.png">
                <p class="p1">服务好</p>
                <p class="p2">快速发货，7天退换</p>
            </li>
        </ul>
    </div>
    <div class="footer">
        <div class="aboutUs">
            <div class="aboutUs_content">
                <?php foreach ($articleCatList as $cat): ?>
                <dl>
                    <dt><?= $cat['cat_name'] ?></dt>
                    <dd>
                        <?php foreach ($cat['articleList'] as $article): ?>
                        <div><a href="<?= \yii\helpers\Url::to(['article/view',
                                'id' => $article['article_id'],
                            ]) ?>" target="_blank"><?= $article['title'] ?></a></div>
                        <?php endforeach; ?>
                    </dd>
                </dl>
                <?php endforeach; ?>

                <dl class="fore">
                    <dt class="clearfix"><span class="tel">
                    <p class="tel_num">/2949 0945</p>
                    <p class="tel_time">周一至周五 9:00-18:00</p></span><span class="tel_txt">
                    <p>电话咨询</p>
                    <p>+86 0755</p></span></dt>
                    <dd>
                        <div class="wx_item"><a href="javascript:;" class="wx_s"><i></i>微信客服</a>
                            <div class="wx_fw"><img src="http://js.xiaomei360.com//img/bottom/code_cc.png"></div>
                        </div>
                        <div class="qq_item"><a href="http://wpa.qq.com/msgrd?v=3&uin=2176570458&site=qq&menu=yes"
                                                class="qq_s"><i></i>在线客服</a></div>
                    </dd>
                </dl>
            </div>
        </div>
        <div class="bottom">
            <div class="bottom_content">
                <div class="com_info">
                    <span>©2016-2017  深圳小美网络科技有限公司版权所有</span>
                    <em>粤ICP备16015162号</em>
                    <a href="http://sc.xiaomei360.com/article.php?id=3" target="_black">欢迎合作</a>
                </div>
                <div class="com_link">
                    <a href="javascript:;" class="frendlink">友情链接</a>
                    <a href="http://www.hzpgc.com/" target="_blank">品观网</a>
                    <a href="http://www.chinabeauty.cn/" target="_blank">中国美妆网</a>
                </div>
            </div>
        </div>
    </div>
</div>