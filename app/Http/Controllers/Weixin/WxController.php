<?php

namespace App\Http\Controllers\Weixin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\WeixinUserModel;
use GuzzleHttp;
use Illuminate\Support\Facades\Redis;

class WxController extends Controller
{
    //刷新access_token
    public function reAccessToken(){
        //Redis::del(WeixinUserModel::$redis_weixin_access_token);
        echo WeixinUserModel::getAccessToken();
    }
    //创建菜单
    public function createMenu(){
        //获取access_token
        $access_token=WeixinUserModel::getAccessToken();
        $url= 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.$access_token;
        //请求微信接口
        $client = new GuzzleHttp\Client(['base_uri' => $url]);
        $data = [
            "button"    => [
                [
                    "type"  => "view",      // view类型 跳转指定 URL
                    "name"  => "百度一下",
                    "url"   => "https://www.baidu.com"
                ],
                [
                    "name"=>"小程序",
                    "sub_button"=>[
                        [
                            "type"=>"location_select",
                            "name"=>"发送位置",
                            "key"=>"rselfmenu_2_0",
                            "sub_button"=>[ ]
                        ],
                        [
                            "type"=>"click",
                            "name"=>"赞一下我们",
                            "key"=>"V1001_GOOD",
                            "sub_button"=>[ ]
                        ],
                        [
                            "type"=>"click",
                            "name"=>"今日歌曲",
                            "key"=>"V1001_TODAY_MUSIC",
                            "sub_button"=>[ ]
                        ],
                    ],
                ],
                [
                    "type"  => "click",      // click类型
                    "name"  => "客服",
                    "key"   => "kefu"
                ]
            ]
        ];
        $body = json_encode($data,JSON_UNESCAPED_UNICODE);//处理中文编码
        $r = $client->request('POST', $url, [
            'body' => $body
        ]);
        //解析微信返回信息
        $response_arr=json_decode($r->getBody(),true);
        //echo '<pre>';print_r($response_arr);echo '</pre>';
        if($response_arr['errcode']==0){
            echo '菜单创建成功';
        }else{
            echo '菜单创建失败'.'</pre>';
            echo $response_arr['errmsg'];
        }

    }
    //获取用户信息
    public function getUserInfo($openid){
        $access_token=WeixinUserModel::getAccessToken();
        $url='https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';
        $data = json_decode(file_get_contents($url),true);
        //echo '<pre>';print_r($data);echo '</pre>';
        return $data;
    }
    /**
     * 首次接入
     */
    public function valid(){
        echo $_GET['echostr'];
    }
    /*
     * 接受服务器事件推送
     */
    public function wxEvent(){
        $data=file_get_contents('http://input');
        //解析XML
        $xml=simplexml_load_string($data);
        //记录日志
        $log_str=date('Y-m-d H:i:s') . "\n" . $data . "\n<<<<<<<";
        file_put_contents('logs/wx_event.log',$log_str,FILE_APPEND);
        $event = $xml->Event;                       //事件类型
        $openid = $xml->FromUserName;               //用户openid
        //处理用户发送消息
        if($xml->MsgType=='event'){
            if($event=='subscribe'){                //扫码关注事件
                $sub_time=$xml->CreateTime;         //扫码关注事件
                $userInfo = $this->getUserInfo($openid);//获取用户信息
                //保存用户信息到数据库
                $user=WeixinUserModel::where(['openid'=>$openid])->first();
                if($user){
                    echo '此用户已关注了你';
                }else{
                    $user_data = [
                        'openid'            => $openid,
                        'add_time'          => time(),
                        'nickname'          => $userInfo['nickname'],
                        'sex'               => $userInfo['sex'],
                        'headimgurl'        => $userInfo['headimgurl'],
                        'subscribe_time'    => $sub_time,
                    ];

                    $id = WeixinUserModel::insertGetId($user_data);      //保存用户信息
                    $xml_response = '<xml><ToUserName><![CDATA['.$openid.']]></ToUserName><FromUserName><![CDATA['.$xml->ToUserName.']]></FromUserName><CreateTime>'.time().'</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA['.'欢迎关注小可爱的公众号'.']]></Content></xml>';
                    echo $xml_response;
                }

            }
        }
    }
}
