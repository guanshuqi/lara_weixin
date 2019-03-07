<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redis;

class WeixinUserModel extends Model
{
    //
    public $table="p_wx_user";
    public $timestamp=false;

    public static $redis_wx_access_token='str:wx_access_token';
    /**
     * 获取access_token
     */
    public static function getAccessToken(){
        $access_token=Redis::get(self::$redis_wx_access_token);
        if(!$access_token){
            $url='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.env('WEIXIN_APPID').'&secret='.env('WEIXIN_APPSERECT').'';
            $data=file_get_contents($url);
            $data=json_decode($data,true);
            $token=$data['access_token'];
            Redis::set(self::$redis_wx_access_token,$token);
            Redis::setTimeout(self::$redis_wx_access_token,3600);
        }

        return $access_token;
    }
}
