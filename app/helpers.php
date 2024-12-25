<?php
//获取js css的路径
use App\Services\Business\UserService;
use Carbon\Carbon;
use phpseclib\Crypt\AES;
use Curl\Curl;


if ( ! function_exists('getCdnUrl')) {
    function getCdnUrl($url)
    {
        $mixUrl = file_get_contents(public_path('mix-manifest.json'));
        if ($mixUrl) {
            $mixUrl = json_decode($mixUrl, true);
            $url    = @$mixUrl[$url] ?: $url;
        }

        return $url;
    }
}

if ( ! function_exists('getVersionUrl')) {
    function getVersionUrl($url)
    {
        return $url . '?web_version=2023071801';
    }
}


/**
 * AES加密
 * @param $data
 * @return string
 */
if ( ! function_exists('aesEncryptNew')) {
    function aesEncryptNew($data = '', $key = '832ujr2#$r2198jf')
    {
        static $crypt = null;
        if (is_null($crypt)) {
            $crypt = new phpseclib\Crypt\AES(phpseclib\Crypt\AES::MODE_ECB);
            $crypt->setKey($key);
        }
        $temp   = $crypt->encrypt($data);
        $temp   = base64_encode($temp);
        $result = $temp;

        return $result;
    }
}

/**
 * AES解密
 * @param $data
 * @return string
 */
if ( ! function_exists('aesDecryptNew')) {
    function aesDecryptNew($data = '', $key = '832ujr2#$r2198jf')
    {
        static $crypt = null;
        $result = $data;
        // 判断是否是密文，通过Base64编码特征识别。
        if (strlen($data) % 4 == 0 && preg_match('/^([+\\/A-Za-z0-9]+)={0,3}$/', $data)) {
            // 对密文进行解密。
            if (is_null($crypt)) {
                $crypt = new phpseclib\Crypt\AES(phpseclib\Crypt\AES::MODE_ECB);
                $crypt->setKey($key);
            }
            try {
                $temp = base64_decode($data);
                // 检查密文长度。
                if (strlen($temp) % $crypt->block_size == 0) {
                    $result = $crypt->decrypt($temp);
                }
            } catch (LengthException $e) {
                // noop.
            } catch (ErrorException $e) {
                // noop.
            } catch (Exception $e) {
                // noop.
            }
        }

        return $result;
    }
}

if ( ! function_exists('encryptECB')) {
    // $key 密钥必须是16字节（128位）
    function encryptECB($content, $key)
    {
        $cipher    = "AES-128-ECB"; // 使用AES-128算法和ECB模式
        $options   = OPENSSL_RAW_DATA; // 使用原始数据格式，不进行Base64编码
        $encrypted = openssl_encrypt($content, $cipher, $key, 0);

        return $encrypted;
    }
}

if ( ! function_exists('decryptECB')) {
    function decryptECB($encrypted, $key)
    {
        $cipher    = "AES-128-ECB"; // 使用AES-128算法和ECB模式
        $options   = OPENSSL_RAW_DATA; // 使用原始数据格式，不进行Base64编码
        $decrypted = openssl_decrypt($encrypted, $cipher, $key, 0);

        return $decrypted;
    }
}

if ( ! function_exists('encryptCBC')) {
    function encryptCBC($content, $key, $iv)
    {
        $cipher    = "AES-128-CBC";
        $options   = OPENSSL_RAW_DATA; // 使用原始数据格式，不进行Base64编码
        $encrypted = openssl_encrypt($content, $cipher, $key, 0, $iv);

        return $encrypted;
    }
}

if ( ! function_exists('decryptCBC')) {
    function decryptCBC($content, $key, $iv)
    {
        $cipher    = "AES-128-CBC";
        $options   = OPENSSL_RAW_DATA; // 使用原始数据格式，不进行Base64编码
        $decrypted = openssl_decrypt($content, $cipher, $key, 0, $iv);

        return $decrypted;
    }
}


/**
 * 发送钉钉消息
 * @param $title
 * @param $message
 * @param $config
 * @param $at
 * @throws ErrorException
 */
if ( ! function_exists('sendDingDing')) {
    function sendDingDing($title, $message, $config, $at = [])
    {
        // 发送钉钉消息
        $msg['msgtype']           = "markdown";
        $msg['markdown']['title'] = $title;
        $msg['markdown']['text']  = $message;
        if ( ! empty($at)) {
            $msg['at'] = [
                'atMobiles' => $at,
                'isAtAll'   => false,
            ];
        }
        $curl = new \Curl\Curl();
        $curl->setHeader('Content-type', 'application/json');
        $curl->setHeader(CURLOPT_SSL_VERIFYPEER, 0);
        $curl->setHeader(CURLOPT_SSL_VERIFYHOST, 0);
        $curl->post(config("dingding." . $config), json_encode($msg));
        $curl->close();
    }
}


/**
 * 发送钉钉卡片消息
 * @param $title
 * @param $message
 * @param $config
 * @param $btns
 * @param  int  $btnOrientation
 * @param  array  $at
 */
if ( ! function_exists('sendDingDingCardBtn')) {
    function sendDingDingCardBtn($title, $message, $config, $btns, $btnOrientation = 0, $at = [])
    {
        // 发送钉钉消息
        $msg['msgtype']                      = "actionCard";
        $msg['actionCard']['title']          = $title;
        $msg['actionCard']['text']           = $message;
        $msg['actionCard']['btns']           = $btns;
        $msg['actionCard']['btnOrientation'] = $btnOrientation;
        if ( ! empty($at)) {
            $msg['at'] = [
                'atMobiles' => $at,
                'isAtAll'   => false,
            ];
        }
        $curl = new \Curl\Curl();
        $curl->setHeader('Content-type', 'application/json');
        $curl->setHeader(CURLOPT_SSL_VERIFYPEER, 0);
        $curl->setHeader(CURLOPT_SSL_VERIFYHOST, 0);
        $curl->post($config, json_encode($msg));
        $curl->close();
    }
}


/** curl请求
 * @param $url
 * @param  string  $data
 * @param  array  $header
 * @param  false  $is_post
 * @return bool|string
 */
if ( ! function_exists('curl_request')) {
    function curl_request($url, $data = '', $header = [], $is_post = false, $timeout = 30)
    {
        $oCurl = curl_init();
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
        //请求超时时间
        curl_setopt($oCurl, CURLOPT_TIMEOUT, $timeout);

        if ( ! empty($header)) {
            curl_setopt($oCurl, CURLOPT_HTTPHEADER, $header);
        }

        //关闭https验证
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);

        //至关重要，CURLINFO_HEADER_OUT选项可以拿到请求头信息
        curl_setopt($oCurl, CURLINFO_HEADER_OUT, true);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
        if ($is_post) {
            curl_setopt($oCurl, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($oCurl, CURLOPT_POST, 1);
            curl_setopt($oCurl, CURLOPT_POSTFIELDS, $data);
        }

        $sContent = curl_exec($oCurl);

        curl_close($oCurl);

        return $sContent;
    }
}


/** 发送钉钉消息
 * @param $dingding
 * @param $message
 * @param  array  $at
 * @return Curl
 */
if ( ! function_exists('sendDingTalkMessage')) {
    function sendDingTalkMessage($dingding, $message, $at = [])
    {
        $data = [
            'msgtype' => 'text',
            'text'    => [
                'content' => $message,
            ],
        ];
        // at有值
        if (count($at)) {
            $data['at']['atMobiles'] = $at;
        }

        $curl = new \Curl\Curl();
        $curl->setHeader("Content-Type", "application/json");
        $rt = $curl->post($dingding, json_encode($data));
        $curl->close();

        return $rt;
    }
}

if ( ! function_exists('logerror')) {
    /**
     * Write some error information to the log.
     * @param  string  $message
     * @param  array  $context
     * @return void
     */
    function logerror($message, $context = [])
    {
        app('log')->error($message, $context);
    }
}

/** 获取指定redis
 * @param  $db
 * @return mixed
 */
if ( ! function_exists('getSelectRedis')) {
    function getSelectRedis($db = '')
    {
        $db    = $db === '' ? config('database.redis.default.database') : $db;
        $redis = \Illuminate\Support\Facades\Redis::connection('default');
        $redis->select($db);

        return $redis;
    }
}

/**
 * 二进制转64进制
 * @param  string  $bin  待转的二进制字符串
 * @return string
 */
if ( ! function_exists('bin2Base64')) {
    function bin2Base64($bin)
    {
        $str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+=';
        for ($i = strlen($bin) % 6; $i < 6 && $i != 0; $i++) {
            $bin = '0' . $bin;
        }
        $splits    = str_split($bin, 6);
        $sixtyFour = '';
        foreach ($splits as $split) {
            $sixtyFour .= $str[bindec($split)];
        }

        return $sixtyFour;
    }
}


/**
 * 64进制转2进制
 * @param  string  $base64  待转的64进制字符串
 * @return string
 */
if ( ! function_exists('base642bin')) {
    function base642bin($base64)
    {
        $str    = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+=';
        $length = strlen($base64);
        $bin    = '';
        for ($i = 0; $i < $length; $i++) {
            $minBin = decbin(strpos($str, $base64[$i]));
            for ($j = strlen($minBin) % 6; $j < 6 && $j != 0 && $i != 0; $j++) {
                $minBin = '0' . $minBin;
            }
            $bin .= $minBin;
        }

        return $bin;
    }
}


if ( ! function_exists('innerMobile')) {
    function innerMobile($mobile)
    {
        if (
            //            substr($mobile, 0, 8) == '13000000'
            //            ||
        in_array($mobile, config('inner_mobile.mobiles'))
        ) {
            return true;
        }

        return false;
    }
}

if ( ! function_exists('diffMonth')) {
    function diffMonth($date1, $date2)
    {
        $timestamp1 = strtotime($date1);
        $timestamp2 = strtotime($date2);
        $months     = abs((date('Y', $timestamp1) - date('Y', $timestamp2)) * 12 + (date('m', $timestamp1) - date('m', $timestamp2)));

        return $months;
    }
}


if ( ! function_exists('OceanEngineApiConversion')) {
    function OceanEngineApiConversion($callback_param, $type)
    {
        //事件映射表
        $event_mapping = [
            1   => 'phone',  //电话拨打
            2   => 'form',  //表单提交
            3   => 'view',  //关键页面浏览
            4   => 'active',  //激活
            5   => 'active_register',  //注册
            6   => 'active_pay',  //付费
            7   => 'phone_confirm',  //智能电话-确认拨打
            8   => 'phone_connect',  //智能电话-确认接通
            9   => 'phone_effective',  //智能电话-有效接通
            10  => 'consult_effective',  //有效咨询
            11  => 'in_app_order',  //app内下单
            12  => 'in_app_uv',  //app内访问
            13  => 'in_app_cart',  //app内添加购物车
            14  => 'in_app_pay',  //app内付费
            15  => 'game_addiction',  //关键行为
            16  => 'customer_effective',  //有效获客
            17  => 'coupon',  //卡券领取
            18  => 'in_app_detail_uv',  //app内详情页到站uv
            19  => 'next_day_open',  //次留
            20  => 'page_view',  //访问目标页面
            21  => 'shopping',  //商品购买
            22  => 'wechat',  //微信复制
            23  => 'other',  //其他
            24  => 'multiple',  //多转化
            25  => 'loan_completion',  //完件
            26  => 'pre_loan_credit',  //预授信
            27  => 'loan_credit',  //授信
            28  => 'idcard_information',  //身份证信息填写完成
            29  => 'bankcard_information',  //银行卡信息填写完成
            30  => 'personal_information',  //补充个人信息填写完成
            31  => 'certification_information',  //用户活体认证信息上传完成
            32  => 'lt_roi',  //广告变现ROI
            33  => 'loan',  //放款
            34  => 'authorization',  //授权
            35  => 'consult_clue',  //留资咨询
            36  => 'stay_time',  //店铺停留
            37  => 'purchase_roi',  //付费ROI
            38  => 'notify_download',  //预约下载
            39  => 'premium_payment',  //保险支付
            40  => 'ug_roi',  //ROI三目标
            41  => 'in_wechat_login',  //微信内注册
            42  => 'in_wechat_pay',  //微信内付费
            43  => 'clue_confirm',  //回访_信息确认
            44  => 'clue_interflow',  //回访_加为好友
            45  => 'clue_high_intention',  //回访_高潜成交
            46  => 'submit_certification',  //提交认证
            47  => 'identification',  //身份认证
            48  => 'first_order',  //首次完单（司机）
            49  => 'first_rental_order',  //首次发单（乘客）
            50  => 'rental_order_finish',  //完成订单（乘客）
            51  => 'clue_pay_succeed',  //支付_存在意向
            52  => 'retention_14d',  //14日留存
            53  => 'retention_30d',  //30日留存
            54  => 'retention_2d',  //2日留存
            55  => 'retention_3d',  //3日留存
            56  => 'retention_4d',  //4日留存
            57  => 'retention_5d',  //5日留存
            58  => 'retention_6d',  //6日留存
            59  => 'retention_7d',  //7日留存
            60  => 'premium_ROI',  //保费ROI
            61  => 'ltv0',  //ltv0
            62  => 'arrival_lead',  //到店线索（汽车）
            63  => 'failed_lead',  //战败线索（汽车）
            64  => 'first_class',  //到课
            65  => 'finish_class',  //完课
            66  => 'conversion_class',  //正价课购买
            67  => 'add_teacher',  //添加老师
            68  => 'in_app_next_day_open',  //拉活次留
            69  => 'withdraw_insurance',  //短期退保
            70  => 'open_account',  //开户
            71  => 'impression',  //激活首日广告展示
            72  => 'ipu_qualify',  //首日广告展示达标
            73  => 'lu_click',  //LU页面二跳点击
            74  => 'lu_convert',  //LU页面二跳转化
            75  => 'withdraw_m2',  //M2内退保
            76  => 'lu_cost',  //LU搜索广告主消耗
            77  => 'low_loan_credit',  //次级授信
            78  => 'high_loan_credit',  //高价值授信
            79  => 'unfollow_in_wechat',  //微信取关
            80  => 'clue_detail_plan',  //线索-购买方案详谈
            81  => 'clue_formal_order',  //线索-正式成单
            82  => 'lu_show',  //LU页面二跳show
            83  => 'in_app_order_7d',  //app内7日下单
            84  => 'key_action5',  //关键行为衍生事件五
            85  => 'key_action4',  //关键行为衍生事件四
            86  => 'key_action3',  //关键行为衍生事件三
            87  => 'key_action2',  //关键行为衍生事件二
            88  => 'key_action1',  //关键行为衍生事件一
            89  => 'clue_order_again',  //线索_续费成单
            90  => 'purchase_roi_2d',  //付费ROI-2日
            91  => 'purchase_roi_7d',  //付费ROI-7日
            92  => 'in_app_retention_30d',  //拉活30日留存
            93  => 'in_app_retention_28d',  //拉活28日留存
            94  => 'in_app_retention_14d',  //拉活14日留存
            95  => 'in_app_retention_7d',  //拉活7日留存
            96  => 'in_app_retention_6d',  //拉活6日留存
            97  => 'in_app_retention_5d',  //拉活5日留存
            98  => 'in_app_retention_4d',  //拉活4日留存
            99  => 'in_app_retention_3d',  //拉活3日留存
            100 => 'in_app_retention_2d',  //拉活2日留存
            101 => 'order_refund',  //退款数据流
            102 => 'premium_upgrade',  //保险升级
            103 => 'arpu0',  //arpu门槛
            104 => 'button_click',  //按钮点击
            105 => 'wechat_qrcode_show',  //微信_二维码展示
            106 => 'wechat_qrcode_try',  //微信_长按二维码
            107 => 'work_wechat_added',  //微信_添加企业微信
            108 => 'work_wechat_dialog',  //微信_用户首次消息
            109 => 'work_wechat_confirm',  //微信_用户确认意向
            110 => 'first_pay',  //首购
            111 => 'supply_active_pay',  //付费-新
            112 => 'retention_days',  //留存天数
            113 => 'link_active',  //评论区链接激活
            114 => 'micro_game_ltv',  //小程序广告变现ltv
            115 => 'work_wechat_unfriend',  //企业微信_取消好友
            116 => 'im_reply',  //智能客服_系统回复
            117 => 'im_dialog',  //智能客服_用户开口
            118 => 'im_arouse',  //智能客服_唤起
            119 => 'is_risk_user',  //是否风险用户
            120 => 'is_low_quality_user',  //是否低质用户
        ];

        $event_type = $event_mapping[$type];

        $curl  = new Curl();
        $url   = 'https://analytics.oceanengine.com/api/v2/conversion';
        $param = [
            'event_type' => $event_type,
            'context'    => [
                'ad' => [
                    'callback'   => $callback_param,
                    'match_type' => 0,
                ],
            ],
            'timestamp'  => time(),
        ];

        $curl->setHeader('Content-Type', 'application/json');
        $curl->post($url, json_encode($param));

        $curl->close();
        if (isset($curl->error_code) && $curl->error_code != '0') {
            $desc    = json_encode(['request' => $param, 'res' => $curl->error_code], JSON_UNESCAPED_UNICODE);
            $message = "** 消息推送-巨量行为上报请求异常 **\n描述：{$desc}";
            sendDingTalkMessage(config("dingding.dev_push_smg"), $message, config("dingding.notify_mobile"));
        }

        return $curl->response;
    }
}


if ( ! function_exists('isUnicomMobile')) {
    function isUnicomMobile($mobile)
    {
        $preNum       = substr($mobile, 0, 3);
        $unicomPreNum = ['130', '131', '132', '145', '155', '156', '166', '167', '171', '175', '176', '185', '186', '196'];
        if (in_array($preNum, $unicomPreNum)) {
            return true;
        }
        $preNum       = substr($mobile, 0, 4);
        $unicomPreNum = ['1704', '1707', '1708', '1709', '1710', '1711', '1712', '1713', '1714', '1715', '1716', '1717', '1718', '1719'];
        if (in_array($preNum, $unicomPreNum)) {
            return true;
        }

        return false;
    }
}

/**
 * 判断是否是微信
 * @return bool
 */
if ( ! function_exists('isWechat')) {
    function isWechat()
    {
        return isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false;
    }
}

if ( ! function_exists('ipToCityInfo')) {
    // 内网：tcp://192.168.50.229:6001  外网：tcp://119.3.40.33:6001
    function ipToCityInfo($ip, $id = null)
    {
        $client = stream_socket_client("tcp://119.3.40.33:6001", $error_code, $error_message);
        if ( ! $client) {
            return [];
            throw new Exception('服务异常');
        }

        $data = [
            'method' => 'Ip.Address', // 不要动
            'params' => [$ip], // ip 数组
            'id'     => $id ?: config('app.app_id'),
        ];
        fwrite($client, json_encode($data));

        $response = fread($client, 2048);

        fclose($client);

        $result = json_decode($response, true);
        // 通过 $data['error'] 是否为空来判断请求是否正常
        if ($result['error']) {
            return [];
            throw new Exception('解析失败');
        }

        $resArr              = json_decode($result['result'], true);
        $resArr['state']     = $resArr['continent'] ?? '';
        $resArr['countries'] = $resArr['country'] ?? '';
        if ($resArr['province'] == '保留') {
            $resArr['province'] = '';
        }
        if ($resArr['city'] == '保留') {
            $resArr['city'] = '';
        }

        return $resArr;
    }
}


