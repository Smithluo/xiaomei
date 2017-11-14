<?php

/* @var $this yii\web\View */

$this->title = '小美诚品 管理后台';
?>
<div class="site-index">
    <div class="body-content">

<strong>@all TIPS</strong>
<span style="color: red; font-size: large"> 商品列表页显示商品的服务商分成比例，如果商品没有设置服务商分成比例，则获取商品对应品牌的分成比例。如果都没设置，显示红色警告</span>
        <!-- 商品告警 -->

        <!-- 工具 -->
        <div class="row" style="border: solid 1px darkgrey">
            <div class="col-lg-3">
                <h3>远程打印和共享</h3>
                <pre>
同时按下WIN键(ctrl和alt键中间的那个)和R键
在“打开（O）” 后面的输入框中粘贴 \\192.168.0.123
按下 Enter键，根据页面提示填写账号密码
    账号：administrator    密码：xiaomei
提示密码错误的解决方案：
控制面板 -> 用户帐户 -> 凭据管理器  修改登录的账号密码

共享打印机驱动安装失败0x00000bcb错误
在控制面板的Windows Update中查看已安装的更新
卸载KB4022722安全更新。

</pre>
                <pre>
后台看到的时间显示 1970-01-01 16:00 表示没有操作过，
商品、订单出现这个时间的是bug，遇到的话，转给开发
</pre>
            </div>

            <div class="col-lg-1">
                <h4><strong>线上网站</strong></h4>
                <p><a class="btn btn-default" target="_blank" href="http://www.xiaomei360.com/">PC商城</a></p>
                <p><a class="btn btn-default" target="_blank" href="http://m.xiaomei360.com/">微信商城</a></p>
                <p><a class="btn btn-default" target="_blank" href="http://m.xiaomei360.com/areuok">旧后台</a></p>
                <p>
                    <a class="btn btn-default" target="_blank" href="http://xiaomei_ftp:8081/redmine/">
                        <span style="color: red;"><strong>RedMine</strong></span>
                    </a>
                </p>
                <p><a class="btn btn-default" target="_blank" href="http://service.xiaomei360.com/">服务商后台</a></p>
                <p><a class="btn btn-default" target="_blank" href="http://supplier.xiaomei360.com/">品牌商后台</a></p>
                <p><a class="btn btn-default" target="_blank" href="http://home.xiaomei360.com/">信息站点</a></p>

            </div>
            <div class="col-lg-1">
                <h4><strong>办公常用工具</strong></h4>
                <p><a class="btn btn-default" target="_blank" href="http://cli.im/url/">二维码生成工具</a></p>
                <p><a class="btn btn-default" target="_blank" href="http://www.yiichina.com/doc/guide/2.0">Yii2权威指</a></p>
                <p><a class="btn btn-default" target="_blank" href="http://fanyi.baidu.com/">百度翻译</a></p>
                <p><a class="btn btn-default" target="_blank" href="http://translate.google.cn/">google翻译</a></p>
                <p><a class="btn btn-default" target="_blank" href="http://json.cn/">Json格式化</a></p>
                <p><a class="btn btn-default" target="_blank" href="https://1024tools.com/unserialize">反序列化</a></p>
                <p><a class="btn btn-default" target="_blank" href="http://tool.oschina.net/">osChina工具</a></p>
            </div>

            <div class="col-lg-7">
                <div class="col-lg-6">
                    <h4><strong>企业邮箱配置</strong></h4>
                    <pre>
<a class="btn btn-default" target="_blank" href="http://ym.163.com/"><strong>企业邮箱</strong> ym.163.com/</a>
FoxMail配置：
接收服务器(POP) :pop.ym.163.com
发送服务器(SMTP):smtp.ym.163.com
每个人的邮箱都是名字的拼音
</pre>
                    <h4><strong>ERP报错常规检查</strong></h4>
                    <pre>
1【升级】系统提示有升级时，执行升级
2【系统日期格式】导入数据的日期格式是 yyyy-M-d，系统默认格式是yyyy/M/d
    Win7系统单击屏幕右下角的日期，
        在（日期和时间）选项卡上 点击更改日期和时间(D)...
        在弹出窗口上点击 更改日历设置，
        在新弹窗上修改时间的 短日期格式
    Win10系统在（设置）中搜索“日期和时间”，
        点击页面底部的（更改日期和时间格式），修改短日期的格式为yyyy/M/d
</pre>
                </div>

                <div class="col-lg-6">
                    <h4><strong>内网环境配置</strong></h4>
                    <pre>
192.168.0.123   xiaomei_ftp  #保留用于登录redmine

#------------  以下代码用于预发布环境验证 ------------
192.168.0.110   mjs.xiaomei360.com  js.xiaomei360.com
192.168.0.110   img.xiaomei360.com  adminjs.xiaomei360.com
192.168.0.144   www.xiaomei360.com  m.xiaomei360.com
192.168.0.144   home.xiaomei360.com backend.xiaomei360.com
192.168.0.144   service.xiaomei360.com supplier.xiaomei360.com
#--- 以上代码用于预发布环境验证 | 以下代码仅用于内网测试 ---
192.168.0.110   www.xiaomei360.com  m.xiaomei360.com
192.168.0.110   home.xiaomei360.com backend.xiaomei360.com
192.168.0.110   service.xiaomei360.com supplier.xiaomei360.com
在服务器上指定api.xiaomei360.com的指向，默认指向本机

大TPLink WIFI：小美wifi 密码：xiaomei2016 网段 192.168.1.1   手机使用
小TPLink WIFI：XMCP 密码：xiaomei2016 网段 192.168.0.1      开发测试用
DNS：218.59.181.182  115.159.157.26
</pre>
                </div>
            </div>
        </div>

        <!-- 内部通讯 -->
        <div class="row" style="border: solid 1px darkgrey">
            <h4><strong>内部通讯</strong></h4>
            <div class="col-lg-2">
                <pre>
邓俊
13077807890
15910725138

李玉贤	18124052459
赖雪婷	15818543436
刘阳	    13647216397
</pre>
            </div>
            <div class="col-lg-4">
                <pre>
【市场部】
李丽	    13510115932 华北区域(北京、天津、黑龙江、吉林、山东、辽宁、内蒙、河北)
陈进	    18886371703 华西区域(山西,重庆,四川,西藏,陕西,甘肃,青海,宁夏,新疆)
李秋萍	    18818871935 华南区域(福建,江西,湖南,广东,广西,海南,贵州,云南)
黄椿烨	    13126477169 华北区域(北京、天津、黑龙江、吉林、山东、辽宁、内蒙、河北)
王姣君	    13974490661 华东区域(上海,江苏,浙江,安徽,河南,湖北)
朱贤彬	    13636383223
李粤晖	    15919857477
乔文洁	    15102937311
</pre>
            </div>
            <div class="col-lg-6">
                <div class="col-lg-3">
                <pre>
【运营部】
匡彪	    13723405045
肖湘秀	    15999670256

温海兰	    18664305801
罗红辉	    18124780319
周嘉浩	    13189571562
</pre>
                </div>
                <div class="col-lg-3">
                <pre>
【招商采购部】
吴喜芝	    13049889166
余雪琴	    17702687330
莫莉萍	    13751087191

林伟施	    13728703835
曾龄仪	    15219796983
吴天晴	    15220285190
</pre>
                </div>
                <div class="col-lg-3">
                <pre>
【仓储】
欧纯	    13669611792
罗元亮	    15989395310
李桂昇	    13723425011
蔡桂滨	    15014367577
罗志峰	    13411073646
</pre>
                </div>
                <div class="col-lg-3">
                <pre>
【平台技术部】
肖云	    13510601717
张文琦	    18611759455
王许洋	    18666211369
陈泽森	    13556215779
康宣鹏	    15889325915
杨嘉俊	    15767976640

吴焕周	    17620345055
於欢	    17620323766
</pre>
                </div>
            </div>

        </div>

        <!-- 当前状态 -->
        <!-- V3.3 -->
        <div class="row">
            <h2>V3.3_m2057_sc1361_yii2262</h2>
            <div class="col-lg-6">
                <h3>发布内容</h3>
                <pre>
双节庆活动
团采秒杀互动支持配置 是否按箱购买、发货箱规
团采秒杀 状态判定的修正, 售罄状态的修改
商品信息、团采秒杀活动 起售数量、是否按箱、发货箱规 数据关系的校验
商品信息变更 即时发送邮件， 商品错误信息每天上午10点定时校验，有错则发邮件给商品运营
</pre>
            </div>

            <div class="col-lg-6">
                <h3>发版步骤</h3>
                <pre>
满10000包邮的运费模板安装(已安装)
团采秒杀活动配置新的 是否按箱购买、发货箱规
</pre>
            </div>
        </div>

        <hr />
        历史版本更新内容
        <!-- V3.2 -->
        <div class="row">
            <h2>V3.2_m2040_sc1334_yii2218</h2>
            <div class="col-lg-6">
                <h3>发布内容</h3>
                <pre>
第三方用户导入订单、查看自己的订单列表
</pre>
            </div>

            <div class="col-lg-6">
                <h3>发版步骤</h3>
                <pre>
【@肖云】
    order.xiaomei360.com  域名解析
    order.xiaomei360.com/admin 路由添加，用户权限配置
    分配角色  3rd_order_import  第三方导入订单
    order.xiaomei360.com/web/ 目录下添加 uploads目录 并赋予 777 权限
</pre>
            </div>
        </div>
        <!-- V3.1 -->
        <div class="row">
            <h2>V3.1_m2038_sc1313_yii2151</h2>
            <div class="col-lg-6">
                <h3>发布内容</h3>
                <pre>
【1】弃用原关联商品配置，创建sku 与 spu 的关联,实现商品详情页的多商品 同时加入购物车、同时立即购买
【配置权限】
/spu/* 给运营
/activity-sort/* 给运营

<span style="color: red"><strong>后续添加SPU 在 <a href="http://backend.xiaomei360.com/spu/create">创建SPU功能页面</a> 逐个添加，然后在商品信息编辑页面做关联。
系统功能兼容没有设置SPU和规格的商品。不需要统一导入，需要做哪个就单独做。</strong></span>
</pre>
                <h3>运营调整</h3>
                <pre>
【关联商品】商品的关联商品 原则上配置 同一SPU的不同规格的SKU，商品的关联商品不宜多，在商品详情页会全部显示出来，
    用户可同时加多个商品到购物车,也可同时立即购买。直发商品不关联非直发商品，非直发商品不关联直发商品，也不关联本品牌以外的商品
配置活动中心的广告位图片、链接
</pre>
            </div>

            <div class="col-lg-6">
                <h3>发版步骤</h3>
                <pre>
【1】创建sku 与 spu 的关联  php yii temp/import-spu-name
导入 运营提供的SPU名称列表
导出 数据库中的 SPU id => name 表，给运营 做匹配，
导入 运营提供的 goods_id、sku_size、spu_id 表

参与可领取优惠券的商品 每天00:30 自动更新 优惠券的标签，优惠券活动的结束时间建议设置为某天的 23:59:59结束
优惠券可是指 领取类型：（不需要领取，自动使用）用于满赠、满减、物料，（用户主动领取）品牌绑定的领券活动，（后台手动赠送）
30 00 * * *  php yii cron/check-events 自动脚本 设置 商品打标签；（php yii cron/set-coupon-tag 废弃）

【2】修改品牌绑定的优惠券活动的领券方式为 用户手动领取    temp/update-brand-event-receive-type
</pre>
            </div>
        </div>
        <!-- V3.0 -->
        <div class="row">
            <h2>V3.0_m2013_sc1205_yii2031</h2>
            <div class="col-lg-6">
                <h3>发布内容</h3>
                <pre>
【<strong>@匡彪</strong>】PC美妆学院 微信站的文章复制到PC站的前提是 有名称一致的分类，所以在修改一端的 文章分类名称时最好在另一端同步修改
【<strong>@匡彪</strong>】复制后的文章 需要重新上传封面图 285*210px，相册封面是取相册的前几张图，视频封面会自动截取视频的某一帧，下载完全是新创建
【<strong>@匡彪</strong>】PC美妆学院首页要显示的视频 由开发人员配置，运营提供要显示的视频对应的文章ID
两端视频详情页 显示视频描述
【<strong>@匡彪</strong>】【<strong>@CC</strong>】商品信息导出 条码有两列 一列带有ZF等类型标记(有前缀的条形码)，一列不带标记显示(条形码)，
    运营、采购使用导出的表时 请删掉自己不用的那一列
【<strong>@匡彪</strong>】文章的【图文资源】->(文章来源)需要配置，不然在PC美妆学院的文章详情页会显示空白占位
【<strong>@匡彪</strong>】文章的区域(国家)维度在【系统】->(区域管理)中添加，父级ID 填 0
</pre>
            </div>

            <div class="col-lg-6">
                <h3>发版步骤</h3>
                <pre>
【已上线】满额包邮 500——8000 按从小到大的顺序逐个安装，逐个配置区域
权限配置:/region/
</pre>
                <h3>遗留问题</h3>
                <pre>
</pre>
            </div>
        </div>

        <!-- V2.9 -->
        <div class="row">
            <h2>V2.9_m1992_sc1165_yii1927</h2>
            <div class="col-lg-6">
                <h3>发布内容</h3>
                <pre>
【运费政策】
SKU的配送方式保留但不生效，团采/秒杀 使用活动配置的配送方式
非团采/秒杀商品的购买 按品牌的配送方式结算，商品详情页取品牌的配送方式
暂时不考虑 购物车结算时同一个品牌的商品有多个配送方式 的场景
【礼包活动】
礼包的库存数 取 SKU满足套数的最小值
只能通过购物车立即购买，不能加入购物车，礼包内的SKU共享库存
不使用全局会员折扣，不参与 满赠、满减、优惠券活动不判定SKU是否按箱购买，
<strong>不判定SKU是否上架—— 如果商品没货，下架时库存改为0</strong>
</pre>
            </div>

            <div class="col-lg-6">
                <h3>发版步骤</h3>
                <pre>
【运费政策】旧后台安装 满2500包邮的配送政策，添加区域：全国，区域运费规则：运费到付，单笔订单满2500元包邮
分配权限
礼包活动配置  http://backend.xiaomei360.com/gift-pkg
礼包商品配置  http://backend.xiaomei360.com/gift-pkg-goods
活动聚合页显示配置 http://backend.xiaomei360.com/super-pkg/index
</pre>
                <h3>遗留问题</h3>
                <pre>
【礼包活动】PC首页显示的热门互动当前只支持配商品，不支持配礼包活动
</pre>
            </div>
        </div>
        <!-- V2.8 -->
        <div class="row">
            <h2>V2.8_m1953_sc1135_yii1816 发版时间：2017年6月16日</h2>
            <div class="col-lg-6">
                <h3>发布内容</h3>
                <pre>
微信端【美妆学院】
两端商品详情页 会员等级优惠 文案修改
微信端 商品详情页的布局修改、信息顺序调整
邀请赠券
</pre>
            </div>

            <div class="col-lg-6">
                <h3>运营需要填充的数据</h3>
                <pre>
1、配置美妆知识库文章来源 /resource-site/create
2、配置美妆知识库需要显示的推荐品牌 /knowledge-show-brand/create
3、美妆知识库 添加 相册 /gallery/create
4、美妆知识库的相册 上传图片 /gallery-img/index
5、美妆知识库 创建文章关联相册，文章分类：美妆知识库 资源类型：相册

微信文章 新增个3配置项目 文章类型、来源站点、关联相册(只在文章类型是相册的时候需要配置)
    文章 4种：文章(默认)、相册、视频、下载
    相册 关联相册
    视频 在文章的富文本里从本地上传视频
    下载 微信端不显示，本期不用配置
</pre>
            </div>
        </div>

        <!-- V2.7 -->
        <div class="row">
            <h2>V2.7_m1869_sc_1100_yii1713</h2>
            <div class="col-lg-6">
                <h3>发布内容</h3>
                <pre>
1 关于优惠券功能的优化改进
2 注册认证分两步走
3 用户采购历史记录列表，用于快速补货
4 用户中心新增积分流水对账列表页面
5 新增发货时间提醒
6 满赠库存修复
7 到货提醒功能
8 国籍icon、直发icon、新品icon
9 超管后台增加用户详情页面
10 WAP站点开发
11 两个站点下单流程统一
【积分流水】两端都在积分兑换也有入口，微信端在个人中心也有入口
小美直发品牌 的默认配送方式修改为 小美直发(满额包邮)
</pre>
            </div>

            <div class="col-lg-6">
                <h3>遗留问题</h3>
                <pre>
商品详情页的 满赠、满减活动 显示，当商品同时参与两种活动的时候应该都显示出标记，实际只显示了一个

<strong><span style="color: red">【雷】</span>用户主动领 （从领取时间开始算有效时段的）券时触发，当前没有使用场景</strong>
优惠券支持在绑定的时候设置券的可用时段——从领券开始算，固定时长（如 1周）后实效
那么券的可用时段可能在互动结束后有延续，如：活动时间5月1日——5月30日，5月30日领券，则券在 5月30日至6月5日可用，
此时，券在个人中心可减，在下单时因活动实效而变得不可用——5月30日以后 优惠券在订单结算过程中不会显示出来
                    </span></pre>
            </div>
        </div>

        <!-- V2.5 -->
        <div class="row">
            <h2>V2.5版本_170317-170331_m1552_sc953_YII1402) 秒杀活动 + 团采/秒杀 库存共享 + 优惠券包 + 品牌详情改版 + 小美头条改版：</h2>
            <div class="col-lg-6">
                <h3>上线</h3>
                <pre>
【约束条件】一个SKU不能同时在生效的团采活动和秒杀活动中个都出现

为秒杀 配置的 【小美直发包邮】 运费插件上线
团采/秒杀的商品 不需要通过复制(可直接使用上架的商品)，
参与 团采/秒杀的商品 普通购买与通过活动购买 共享库存，防止超卖；秒杀订单30分钟内未支付的将自动取消
团采/秒杀的商品 可配置独立的配送方式和起售数量

秒杀的商品 限购数量为 整个活动对应的订单中活动商品的累计可购买数量
团采的商品 限购数量为 每次订单中活动商品的可购买数量

【团采/秒杀的状态】
a)即将开始  ——当前时间【不在】活动时段内
b)进行中    ——当前时间【在】活动时段内 && 库存 > 0
c)已售罄    ——当前时间【在】活动时段内 && 库存 <= 0
</pre>
            </div>

            <div class="col-lg-6">
                <h3>历史遗留问题</h3>
                <pre>
优惠券、满减  强制二选一，先给出提示，上线后打补丁：显示优惠券和满减，让用户自己选择使用哪种
微信商城里的订单列表中 参与满减的商品没有显示 满减标签
<span style="color: red"><strong>APP端 商品详情页加入购物车要考虑满赠 的最大可购买数量，采购车中加减商品数量也要考虑满赠活动</strong>
【接口】检查foreach 中的 model方法调用，能改用批量的改批量操作
订单列表，订单详情中的商品，如果不是团拼商品 已下架的商品链接到首页，以后要改成404
【数据统计】添加分页和可选择排序项  【搜索统计】关键词搜索统计没有给出页面
商品搜索列表页面 按 满减标签 搜索
服务商用户列表的 1150 1185，1193，1194  对应的个人信息都是空的 —— 是否删除这些测试账号
o_servicer_user_info的删除要考虑 user表是否有用到，有用到则不能删除
</span></pre>
            </div>
        </div>
        <!-- V2.4 -->
        <div class="row">
            <h3>V2.4版本_170227-170317(m1488_sc911_后台1321) 优惠券 + 总单管理 + 后台订单修改 + 服务商分成修正：</h3>
            <div class="col-lg-6">
                <pre>
【CC、欧纯】小美直发 运费规则调整，不同区域满足一定金额包邮，不满足可由用户选择到付运费还是现付运费
【陈聪】用户信息导出，添加 实际支付次数、最后一次支付时间 字段
支持销售人员 配置 负责的区域 只能配置到省、市 两级区域，因为用户信息只存储了两级区域。
服务商的管辖区域也是同样道理
</pre>
            </div>

            <div class="col-lg-6">
                <pre>
【匡彪】满赠、满减活动配置，生效时段;没有设置 参与活动的商品 的活动 将被视为没任何商品参与，即 实际未生效
      每天的00:00:00 系统自动修正 商品的 满赠、满减、标签，其他标签还是运营手动维护 ——本期优惠券上线不显示券标
按商品金额分区域 满足一定数额包邮，不满足的预付固定费用
优惠券 功能上线，管理员才有权限操作优惠券
</span></pre>
            </div>
        </div>

        <!-- V2.3 -->
        <div class="row">
            <h3>V2.3版本_170213-0223(m1432_sc864_后台1190) 运营需求 + 服务商后台分配权限 + 支付减库存 + 发货单 + 后台用户可用积分显示：</h3>
            <div class="col-lg-6">
                <pre>
<strong>@陈聪</strong> 微信：个人中心，不同地区用户显示不同的联系客户方式（对应服务商的联系人信息）
<strong>@匡彪</strong> <strong>@于雪琴</strong>
两端注册：企业名称修改为店铺名称;用户名 加placeholder 建议填写真实姓名
两端下单：小样信息自动添加至订单备注
两端：团采增加建议零售价
PC端：商品列表页单选项，删除包邮筛选项，增加散批筛选项
PC端：搜索框下面显示 “热搜榜” 三个字
后台：商品列表与商品运营列表功能合并，商品添加有效期字段，打标功能在二级页面展示,列表页可切换商品上下架和是否设为明星单品
</pre>
            </div>

            <div class="col-lg-6">
                </pre>
                <pre>
---- 更多内容见邮件：2017/02/23（本周四）预备上线内容  xiaoyun@xiaomei360.com ----
微信 帮助中心
易宝网银支付
物流信息查询
商品上架前两端预览
服务商权限分离
服务商区域划分
后台用户可用积分显示
</span></pre>
            </div>
        </div>

        <!-- V2.2 -->
        <div class="row">
            <h3>V2.2版本_170107_170215(m1389_sc827_后台1105)满减活动_总订单_服务商区域运费现付 更新内容如下：</h3>
            <div class="col-lg-6">
                <pre>
<strong>@吕颂扬</strong>
    服务商分成 在订单真实完成(不能退换，这个动作是系统自动做的)后分配到服务商钱包中，服务商可正常提现。
    服务商的门店列表中显示自己业务员包括自己，服务商的后台首页、人员管理页面的业务员列表不显示服务商自己
    天津服务商可以审核 天津、唐山、秦皇岛、廊坊 4个城市的会员
    未审核、审核拒绝的门店登录时显示 河南服务商提供更多联系人信息，用户中心的显示修正过了
    服务商审核用户时 提示 邮箱不正确的问题修复了，实际受影响的门店有2个
    后台创建服务商、服务商后台创建业务员之后可以在 用户管理模块的 零售店编辑页面中 绑定服务商ID的显示列表中看到最新创建的业务员

<strong>@CC</strong> <strong>@匡彪</strong> <strong>@陈聪</strong>
    用户未收到货的订单 被置为 真实完成的状态 已修复。订单的发货时间，支付时间都正常了
    原因：订单发货、手动支付 没有修改对应操作的时间。
<strong>@匡彪</strong> <strong>@陈聪</strong> 用户列表页的用户类型  筛选功能修复——显示、导出 都生效

<strong>@陈聪</strong> <strong>@邹庆红</strong>
    零售店详情页编辑用户所属省份 修改为搜索筛选，零售店列表页套用新模板
    零售店列表页可编辑用户所属省份

<strong>@陈聪</strong> <strong>@吕颂扬</strong> 用户等级不再有 未审核，用户注册默认等级为普通会员，审核时只修改审核状态即可
</pre>
            </div>
                <pre>
<strong>@匡彪</strong> <strong>@余雪琴</strong>
关于满减
    满减活动规则的 增删改查，满减活动不配置活动策略，在满减策略模块配置满减规则时关联满减活动
    暂时不考虑多个满减；参与满减活动的商品要手动打上满减标，满减活动结束，手动去掉满减标
    后续支持多个满减时，如果一个商品同时参与多个满减活动，则只会在一个活动中参与满减的计算。
    请注意活动名称的用词，使语句连贯，注意检查；
    添加满减标签，参与满减活动的商品需要手动添加标签，商品打标操作和满赠一致
关于商品、品牌
    运营商品列表页面的商品复制功能关闭，商品主图、轮播图需要重新上传，
    商品列表页的 是否上架、交易类型支持筛选全部，明星单品 只支持筛选是明星单品的，导出结果与筛选结果一致
    新建品牌 默认不上架、服务商分成比例为0，如果不对，请手动修改至正确。修复不上传logo图的报错，logo图必须上传
    商品添加 的时候可以不选择配送方式，则该商品默认使用对应品牌的配送方式
    商品编辑 的时候可以不修改配送方式，如果编辑的商品之前没有设过配送方式，则该商品默认使用对应品牌的配送方式
    商品信息编辑之后会在左上角显示跳转到PC站该商品的详情页，便于检查商品信息显示是否正确
关于运费现付
    生效的商品范围：小美直发的普通商品(积分兑换商品继续使用到付)
    生效的收获地址范围：新疆、湖北、河南、山西、天津、河北三个市
    开关位置：m站 UsersModel.class.php的order_fee方法的 $isFpbsArea值置为false 则不按预付规则计算运费
</pre>
            </div>
        </div>

        <div class="row">
            <!-- V2.1 -->
            <div class="col-lg-6">
                <h3>V2.1版本_161223(m1296、sc741、后台968)后台功能搬迁_下单接口修复：</h3>
                <pre>
【微信端】
    【已上线】热批商品添加超链接到明星单品的列表页（category）
    【已上线】普通商品详情页与PC端详情页信息同步 原装进口、提供证件等
【新后台】
    【已上线】订单入库接口，赠品的parent_id修正
    【已上线】<strong>@匡彪</strong> <strong>@陈聪</strong> 零售店列表 提供 注册时段、最近登录时段的筛选
    【已上线】<strong>@陈聪</strong> <strong>@CC</strong> 新后台的数据导出已优化，导出数据如果报错，添加筛选条件，减少每次导出的总数量
    【已上线】<strong>@KIKI</strong> 新上品牌没有在PC首页的新上品牌模块出现的，在新后台修正品牌的排序权重
    【已上线】<strong>@KIKI</strong> 商品图片商品相册、关联商品、配件、关联文章 暂时未搬迁到新后台
    【已上线】<strong>@KIKI</strong> 商品的购买类型 普通商品、积分兑换 只能在创建的时候选择，不能修改，避免忘记改价格造成错误
    【已上线】<strong>@KIKI</strong> 旧后台的商品、订单相关功能转移到新后台。商品的计件单位、在商品添加和编辑时修改。商品分类中的计件单位无效
                    商品单位 弃用品牌中的单位，用脚本把品牌中的单位填充没有单位的商品  删除字段： o_category.measure_unit
    【已上线】<strong>@CC</strong> 修正导出订单的商品所属品牌为商品品牌。（之前小美直发的商品会显示成第一个商品的品牌） 订单列表的时间改为时间控件
    【已上线】一次支付多个订单，只发一条付款短信
</pre>
            </div>

            <!-- V2.0 -->
            <div class="col-lg-6">
                <h3>V2.0版本版本更新内容如下：</h3>
                <pre>
小美诚品品牌下的商品 在商品详情页右侧的【产品目录】显示所有的小美直发商品
【M站、PC站】
    【商品】<strong>@KIKI</strong> 积分商品通过列表页最后一个按钮<span class="glyphicon glyphicon-asterisk"></span> 来创建，
          注意创建后积分商品为下架状态，需要修改价格和上架状态才生效。积分商品可设置购买需要的等级
          <strong>@匡彪</strong> <strong>@KIKI</strong> 商品列表添加 购买需要的等级 的显示
    【积分商城】  标记 extension_code = 'integral_exchange'
        积分兑换商品不进入浏览历史，用户支付成功时插入积分交易流水，后台支付逻辑也有积分记录
        积分商品 全部设置为小美诚品供应商，配送方式为运费到付
    【确认订单】<strong>@匡彪</strong>手动确认收货 脚本确认收货 都会修改积分流水的状态，用户确认收货后不能退款
【新后台】
  <strong>@Linda</strong> <strong>@CC</strong>订单列表添加 购买类型：普通商品、团采、积分兑换的筛选
  <strong>@匡彪</strong> <strong>@KIKI</strong> 商品列表页商品名称 可直接跳转到PC站的商品详情页,字段内容修正
  <strong>@陈聪</strong> <strong>@吕颂扬</strong>用户列表页 不需要缩放页面可以看到所有按钮
        审核意见 在鼠标经过【审核状态】的时候显示，页面搜索审核意见的关键词功能正常使用，
</pre>
            </div>

        </div>

        <div class="row">
            <!-- V1.9 -->
            <div class="col-lg-6">
                <h3>V1.9版本版本更新内容如下：</h3>
                <pre>
【已修复】小美直发商品显示正确的发货地、服务。小美品牌页显示所有小美直发的商品
【已修复】PC站首页的热门品牌 的 新上品牌 排序按最后一次修改时间逆序排列
【已修复】服务商后台收支对账查询时间跨度不对
【已修复】微信站未登录状态点击商品链接显示空白
【已修复】用户access_token为空时支付宝支付，订单状态不变
【已修复】购物车有不能购买的商品时，用户无法下单
【已上线】M站新模板、用户退货申请填写原因
【已上线】M站、PC站 不显示渠道信息
【已上线】PC站、M站 商品详情页中  满赠商品添加链接
【已上线】PC站、M站、M站后台 支付成功，判断普通会员已支付金额满10000自动升级
【已上线】M站 套模板
</pre>
            </div>

            <!-- V1.8 -->
            <div class="col-lg-6">
                <h3>V1.8版本(m站1184、sc站660、新后台724)版本更新内容如下：</h3>
                <pre>
【PC\微信】团采进度刷新即显示当前进度，不需要清空缓存；商品列表梯度价格
    团购活动 何时开团、那些活动开团可由开发配置；
    is_hot做为购物车推荐商品的标记，is_best、is_new、is_spec废弃
【PC站】商品详情页首图默认显示750 高清原图；注册时用户名不为空即可；浏览器兼容
【微信站】 购物车页面小计件数；商品库存单位
【新后台】 <strong>@匡彪</strong>
    首页配置增加热批商品、微信端热门品牌、楼层相关配置——>内部共享\操作相关\
    可以分配用户所属的省份、业务员、服务商  有操作文档——>内部共享\操作相关\
    商品关键词配置，使用英文逗号做分割，对搜索优化有帮助
    系统自动修改最近2周内上架(最近编辑过的不算)的商品为新品上架
    数据监测 用户登录成功后的每一个动作都有记录
    <strong>@KK</strong>
    商品上架/编辑 添加<strong>物料配比</strong>字段，
    品牌需要添加<strong>国家</strong>字段，在品牌黄页(品牌政策)验证。
    商品部门编辑商品保存时自动更新最小价格，避免忘记清理缓存导致线上显示错误
【微信公众号】 用户发任意消息，自动回复陈聪的联系方式（图片）
【服务商后台】浏览器兼容，调整修改密码不成功的样式，密码修改成功退出登录
</pre>
            </div>

        </div>

    </div>
</div>
