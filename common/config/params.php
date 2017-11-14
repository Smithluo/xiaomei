<?php
return [
    'adminEmail' => 'admin@example.com',
    'supportEmail' => 'support@example.com',
    'user.passwordResetTokenExpire' => 3600,

    'order_pay_fee' => 0.006,
    'caches_base_dir' => '/alidata/www/caches/',
    'cache_file_name' => [
        'active_events' => 'active_events_info.json',   //  功能未完善

        'shop_config_params' => 'shop_config_params.json',  //  商城常用配置

        'user_rank_map' => 'user_rank_map.json',        //  用户等级
        'region_map' => 'region_map.json',              //  区域配置
        'region_app_map' => 'region_app_map.json',      //  IOS app 使用的区域映射
        'region_wechat_register_map' => 'region_wechat_register_map.json',  //  微信用户注册 使用的区域映射

        'goods_category' => 'goods_category.json',      //  商品分类
        'all_brand_cat_map' => 'all_brand_cat_map.json',//  品牌分类
        'hot_brand_cat_map' => 'brand_cat_map.json',    //  热门品牌

        'servicer_list' => 'servicer_list.json',            //  服务商、业务员列表
        'category_cache' => 'category_cache.json',
//        'servicer_info_map' => 'servicer_info_map.json',    //  服务商及审核对接人信息
    ],
    'm_region_cache_file' => '/alidata/www/m.xiaomei360.com/data/region_wechat_register_map.json',
    'tag_log_base_path' => '/alidata/log/mark/',

    //  不同平台的默认支付方式
    'platFormDefaultPayment' => [
        'm'         => 'wxpay',
        'pc'        => 'alipay',
        'ios'       => 'alipay',
        'android'   => 'alipay',
    ],
    'paymentMap' => [
        '1'     => '支付宝',
        '2'     => '银联企业用户支付',
        '3'     => '微信支付',
    ],

    'payIdCodeMap' => [
        '0'     => 'backend',   //  后台支付的订单 order_info.pay_id = 0
        '1'     => 'alipay',
        '2'     => 'yinlian',
        '3'     => 'wxpay',
    ],

    'brand_cat_tree_root' => 57,
    'goods_cat_tree_root' => 299,
    'area_cat_tree_root' => 101,

    //  商品属性配置
    'goods_attr_id' => [
        'region' => 165,    //  产地
        'effect' => 211,    //  功效
        'sample' => 212,    //  物料配比
    ],

    'shop_config' => [
        'no_picture' => 'http://m.xiaomei360.com/data/common/images/no_picture.gif',
        'img_base_url' => 'http://img.xiaomei360.com',
        'shop_url' => '/',
    ],

    'pcHost' => 'http://www.xiaomei360.com',
    'wechatHost' => 'http://m.xiaomei360.com',

    //  特殊渠道，服务商用户列表不出现特殊渠道的用户
    'spec_channel' => ['洽客'],

    //  已有服务商的省份id
    'province_has_server' => [
        '1857',     //  湖北省
        '1662',     //  河南省
        '3391',     //  新疆
        '234',      //  山西
        '20',       //  天津  唐山65、秦皇岛市81、廊坊市209 归天津服务商
        '499',      //  辽宁
        '3064',     //  陕西
        '706',      //  黑龙江
        '2466',     //  重庆市
    ],
    'city_has_server' => [65, 81, 209],

    'tianjinServeCityList' => [65, 81, 209],

    //  是、否型 字段的 dropdownList
    'is_or_not_map' => [
        1 => '是',
        0 => '否',
    ],

    //  新品上架的时效 2周 2 * 7 * 24 * 3600 = 1209600
    'goods_is_new_term' => 1209600,
    //  配置特例，之前添加的商品没有上架，现在需要显示 新品商家标签的商品
    'new_tag_goods_id_list' => [],

    'default_shipping_code' => 'fpd',       //  默认到付
    'default_shipping_id' => 3,             //  默认到付
    'free_shipping_code' => 'free',         //  包邮
    'zhiFaDefaultShippingCode' => 'fgaf',   //  直发商品默认配送方式  小美直发(满额包邮)
    'zhiFaDefaultShippingId' => '5',   //  直发商品默认配送方式  小美直发(满额包邮)
    'shippingIdShortDesc' => [
        2 => '包邮',
        3 => '到付',
        4 => '现付',
        5 => '小美满额包邮',
        6 => '小美包邮',
        7 => '满2500包邮',
        8 => '满500包邮',
        9 => '满1000包邮',
        10 => '满2000包邮',
        11 => '满3000包邮',
        12 => '满3500包邮',
        13 => '满5000包邮',
        14 => '满8000包邮',
    ],

    'employee_mobile' => [
        '13077807890',
        '15910725138',
        '18638155414',
        '13714009247',
        '13510601717',
        '18611759455',
        '13723405045',
        '13049889166',
        '18320778364',
        '18566793850',
        '13728703835',
        '18219208310',
        '17702687330',
        '13669611792',
        '15889536986',
        '15279325607',
        '15813681037',
        '15915874513',
        '15986725094',
        '18124052459',
        '15219796983',
        '15989395310',
        '13621278831',
        '13556215779',
        '18123694314',
        '13937102150',
        '13613044501',
        '13510115932',
        '17620323766',
        '17620345055',
        '15889325915',
        '15767976640',
        '13723425011',
        '15818543436',
        '15767976640',
        '13126477169',
        '18818871935',
        '13189571562',
        '15014367577',
        '13411073646',
        '13647216397',
        '15999670256',
        '15102937311',
        '13751087191',
    ],

    //  客户经理映射，用户未审核、已拒绝的
    //  tel 用于接收短信，officePhone 显示给用户，可联系到客户经理
    'accountManager' => [
        //  新疆联系方式
        '3391' => [
            'tel'           => '13999521216',   //  18509062898
            'officePhone'   => '13999521216',
            'consignee'     => '钟经理',
            'WeChatcode'    => 'http://mjs.xiaomei360.com/img/user/xinjiang.png',
        ],

        //  湖北联系方式
        '1857' => [
            'tel'           => '13807130009',
            'officePhone'   => '13807130009',
            'consignee'     => '范经理',
            'WeChatcode'    => 'http://mjs.xiaomei360.com/img/user/hubei.png',
        ],

        //  河南联系方式
        '1662' => [
            'tel'           => '13083661515',
            'officePhone'   => '13083661515',
            'consignee'     => '宋经理',
            'WeChatcode'    => 'http://mjs.xiaomei360.com/img/user/henanV3.png',
        ],

        //  天津联系方式
        '20' => [
            'tel'           => '13902081111',
            'officePhone'   => '13902081111',
            'consignee'     => '耿经理',
            'WeChatcode'    => 'http://mjs.xiaomei360.com/img/user/tianjin.png',
        ],

        //  山西联系方式
        '234' => [
            'tel'           => '18035111228',
            'officePhone'   => '18035111228',
            'consignee'     => '朱经理',
            'WeChatcode'    => 'http://mjs.xiaomei360.com/img/user/shanxi_jin.png',
        ],

        //  辽宁联系方式
        '499' => [
            'tel'           => '13940008131',
            'officePhone'   => '13940008131',
            'consignee'     => '夏小姐',
            'WeChatcode'    => 'http://mjs.xiaomei360.com/img/user/liaoning.png',
        ],

        //  陕西服务商
        '3064' => [
            'tel'           => '15829696919',
            'officePhone'   => '15829696919',
            'consignee'     => '刘经理',
            'WeChatcode'    => 'http://mjs.xiaomei360.com/img/user/shanxi_qin.png',
        ],

        //  黑龙江服务商
        '706' => [
            'tel'           => '13836142261',
            'officePhone'   => '13836142261',
            'consignee'     => '韩小姐',
            'WeChatcode'    => 'http://mjs.xiaomei360.com/img/user/heilongjiang.png',
        ],
        //  重庆服务商
        '2466' => [
            'tel'           => '13102327673',
            'officePhone'   => '13102327673',
            'consignee'     => '谭小姐',
            'WeChatcode'    => 'http://mjs.xiaomei360.com/img/user/chongqing.png',
        ],
    ],

    'accountManagerSpecial' => [
        //  河南服务商的分割点 service站 的 params 也有，改动时要同步
        '1662' => [
            'tel'           => '13974490661',
            'officePhone'   => '13974490661 ',  //  吕经理 15978858443
            'consignee'     => '王经理',
            'WeChatcode'    => 'http://mjs.xiaomei360.com/img/user/henan.png',
        ],

        //  特殊处理河北， 服务商支持配置到市、区级 后不需要特殊处理   city [65, 81, 209]
        '39' => [
            'tel'           => '13902081111',
            'officePhone'   => '13902081111',
            'consignee'     => '耿经理',
            'WeChatcode'    => 'http://mjs.xiaomei360.com/img/user/tianjin.png',
        ],

        //  市场部员工   李丽
        '13510115932' => [
            'tel'           => '13510115932',
            'officePhone'   => '13510115932',
            'consignee'     => '李经理',
            'WeChatcode'    => 'http://mjs.xiaomei360.com/img/user/13510115932.png',
        ],
        //  市场部员工   王姣君
        '13974490661' => [
            'tel'           => '13974490661',
            'officePhone'   => '13974490661',
            'consignee'     => '王经理',
            'WeChatcode'    => 'http://mjs.xiaomei360.com/img/user/13974490661.png',
        ],

        //  默认 内部销售部门主管
        'default' => [
            'tel'           => '18682415360',
            'officePhone'   => '18682415360',     //  0755-29490945 等电话接入到销售办公室再改回去
            'consignee'     => '美美',
            'WeChatcode'    => 'http://mjs.xiaomei360.com/img/user/WeChatcode.png',
        ],
    ],

    //  河南省启用新服务商的时间，此前注册的用户划给 1701 , 此后注册的用户划分给新服务商  实际时间确定为北京时间的2016-12-27
    'henanBreakPointStamp' => 1482739200,   //  2016-12-27 00:00:00  m站也有设置，改动时要同步

    //  满减与优惠券是否共存  true => 共存， false => 不共存
    'fullCutCouponCoexist' => false,
//    'mdm.admin.configs' => [
//        'advanced' => [
//            'app-backend' => [
//                '@common/config/main.php',
//                '@common/config/main-local.php',
//                '@backend/config/main.php',
//                '@backend/config/main-local.php',
//            ],
//            'app-frontend' => [
//                '@common/config/main.php',
//                '@common/config/main-local.php',
//                '@frontend/config/main.php',
//                '@frontend/config/main-local.php',
//            ],
//        ],
//    ],
    //国家图标的存储位置
    'country_dir' => '/alidata/www/m.xiaomei360.com/data/attached/country_icons/',
    'alipay_config' => [
        //应用ID,您的APPID。
        'app_id' => "2016042001316372",

        //商户私钥，您的原始格式RSA私钥
        'merchant_private_key' => "MIICXgIBAAKBgQDdBRWSSXb7tg2mmav2VzAiEVNuLU1NQkxl684LdLBUSyx9oCiXJ6tJVXW37DyLhSxsbdqivwTV2Xb3Czi91J9GhQqDwqpU4vSDDHDZSyEgjq/wrflATo8+ST48eVKlyscMkwPhv2lc2oJSGjgmkorb3Jl58eG7YfcCk3Aw9P6w2wIDAQABAoGAA7CACa8cQ1toou1Rx4zxCsCLSf2LmsyOhe0HxX0vLFkM5xPzWYKaA2Ff07An2pRgh3bV/X1+0SsOJ1WSnuibuAOuev8QXTXYrMPtX6MLYvRP1HrqlZoVBO1Bmc68jdoxS7omHSaK86m4yrPQIwaP02K0k3XGRqCXQ3VxODReZTECQQD/Ly7YeEd3O6is35iTca0sMPrc92ORo3IwhEdrPhOiZAyJlIb4IDBSkYxvEwbFvKcjfp8CePBZv/vaE5gp9wcnAkEA3bnxzcv2cG3UJfhQ//ysFY8nt/QrNQ25x6M+dAV093H+w0Vl22Ldv4bnOTkNYl5dUL+OGZd/8rZQCTtzcud5LQJBALuA1OAUSRbQTFlyFi9I2ODewIX6dTv/KBmEKOIhA9ZPw3KYIzBQnpEdB15aUaCbxQfsszPi32BjE9Ciky1KqQMCQQC4yPDGTEdz53Q4uLv4u0FHLmkxm6IusuOzh07TLoEOf8iMQNfkgH7B0dH+FJgc9PvcAeiRV3tgcaQ+LXfHuTV5AkEApkWYz+r5/z8yhLkNkhu0CVG863K4rWYRPOs9yWleSvn5BCYGJz07NS2j23qebQh6kDWvX+zt96Sfb4f/QITExQ==",

        //异步通知地址
        'notify_url' => "http://api.xiaomei360.com/v1/notify/alipay-rsp",

        //同步跳转
        'return_url' => "http://m.xiaomei360.com/default/flow/pay_done.html",
        //编码格式
        'charset' => "UTF-8",

        //签名方式 RSA RSA2
        'sign_type'=>"RSA",

        //支付宝网关 现网
        'gatewayUrl' => "https://openapi.alipay.com/gateway.do",
        //调试
        //'gatewayUrl' => "https://openapi.alipaydev.com/gateway.do",

        //支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
        'alipay_public_key' => "MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDDI6d306Q8fIfCOaTXyiUeJHkrIvYISRcc73s3vF1ZT7XN8RNPwJxo8pWaJMmvyTn9N4HQ632qJBVHf8sxHi/fEsraprwCtzvzQETrNRwVxLO5jVmRGi60j8Ue1efIlzPXV9je9mkjzOmdssymZkh2QhUrCmZYI/FCEa3/cNMW0QIDAQAB",
    ],

    'yeepay_config' => [
        'merchantAccount' => '10014504447',
        'merchantPrivateKey' => 'MIICdwIBADANBgkqhkiG9w0BAQEFAASCAmEwggJdAgEAAoGBAKCj/HHE2jrd6RuihE3J8Pa2eZTHSRaWtlh7N/rxPs6SRwKa9wH5RFMM7e99yBpOF/Zg9rI0U6blJx7wEDSeiXS/CnvdNgU6SjRseyk5dJXscTDo6mrQAbnsv9kVLsnLPKGq+QH5jNzajF/Y7qpfHOinyVykJBydsReklAIV6rWbAgMBAAECgYAmMth0tTqvhNVJnPuZA8wo3ntKxZi7plr171cAdR1aQa5hEDhsX7m/hfOOX3qVxrwr+iKvfYHmzdbfpHY2DPziQ6d4fP1RBC136Ipx7B6HKJfO6Xklpt8yVp4bxDC8zF/8MHd58aY7ZJLyoywwkvGPtkQQxch5OSG9VVr0a+ZEAQJBAPfStvD2x+TmtIK5i3FGYrV0lzL97oJ8n+8/woL4Um7GXVJMSE8PwVcc+KqXRC4jq2ahdt0Zxu3SpNy3m6XeuwsCQQCl8N4U5BGtucX9sWlextpCkYD7tIQwAL6xAibUPc8x744x+lpeyiqUi+73KmSg59k/x5W22wp9fNSzfJFuCAmxAkAcl2Hl0QLk5L0Eq/VrfyxaNPZQur0ursQg7SE6zP1trFMN8KETBgVPUJdbzxHdpN3cfFpjTdsGixvcHw7FBzpbAkEAoIPILukWeLe031vXk0hDJBVfcRsCqvRtgQeVy8QmQiV5pLqI5Bwm6B4b/5ZQVJ0wttM27PQgx5YSobQjcQ3xYQJBANb3PkVlMJoXXb55nFu4QxUiyCefz2izzeXoPx/gE5xi96kPcYoqgUt/jyhlP3oozC0Bcui73P6ifmqRFLTj4uI=',
        'merchantPublicKey' => 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCgo/xxxNo63ekbooRNyfD2tnmUx0kWlrZYezf68T7OkkcCmvcB+URTDO3vfcgaThf2YPayNFOm5Sce8BA0nol0vwp73TYFOko0bHspOXSV7HEw6Opq0AG57L/ZFS7JyzyhqvkB+Yzc2oxf2O6qXxzop8lcpCQcnbEXpJQCFeq1mwIDAQAB',
        'yeepayPublicKey' => 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCfBBwDpGo7MnQDU+8MKg20ui8T/SkTQokAkg4v7GD9++7DCX3stqyO/UvaiIHBQC2lpLWTYYLJvgOvhNfFBw3J1Yh9gkyApQb29UfhFli8M8WapVesaU2t3x7mRLkE1VctIXijY2gHA75KyJRoBdxoaQZWcj023MJL9VlgFiyIvwIDAQAB',
    ],

    //  美妆学院 首页显示的视频对应的文章
    'beauty_academy_show_video' => 252,

    //  邮件组
    'mailGroup' => [
        //  商品 上下架、库存 跨过起售数量  发送通知
        'goodsOperater' => [
            'wangxuyang@xiaomei360.com',
            'xiaoyun@xiaomei360.com',
            'yangjiajun@xiaomei360.com'
        ],
        //  商品信息不符合逻辑 或 可能存在错误 发送给商品负责人
        'goodsManager' => [
            'wenhailan@xiaomei360.com'
        ],
    ],
];
