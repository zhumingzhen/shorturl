<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Shorturl;


class ShortUrlController extends Controller
{
    public function index()
    {
        return view('shortUrlIndex');
    }


    public function longtoshort(Request $request)
    {
        $lUrl = trim($request->get('lUrl'));
        // 获取短地址
        $randstr = $this->getrandomstring(5);

        // 查询随机数是否已存在
        $short_is_having = Shorturl::where('short_url',$randstr)->first();

        if ($short_is_having) {     // !$short_is_having->isEmpty()
            // 随机数已存在
            $res = array('code'=>400,'data'=>'','msg'=>'短链部分已存在...');
            return $res;
        }else{
            // 查询长链是否已存在
            $long_is_having = Shorturl::where('long_url',$lUrl)->first();
            if ($long_is_having) {
                // 长链已存在   // $long_is_having->short_url
                $res = array('code'=>200,'data'=>'http://2dw.win/'.$long_is_having['short_url'],'msg'=>'长链已存在,可复制使用。');
                return $res;
            }
            //准备SQL语句
            $save['short_url']=$randstr;
            $save['long_url']=$lUrl;
            $save['count']=1;
            $createRes = Shorturl::create($save);  // 如果save失败，返回false；如果成功，返回model。

            if ($createRes != false) {
                $res = array('code'=>200,'data'=>'http://2dw.win/'.$randstr,'msg'=>'短链生成成功，可复制使用。');
            }else{
                $res = array('code'=>400,'data'=>'','msg'=>'短链生成失败。。。');
            }
            return $res;
        }
    }

    public function shorttolong($short)
    {
        // 短链转长链
        $short_to_long = Shorturl::where('short_url',$short)->first();
        if ($short_to_long){
            // 有短链增加访问次数
            $save['count']=$short_to_long['count']+1;
            Shorturl::create($save);  // 如果save失败，返回false；如果成功，返回model。
            // 有短链记录跳转
            $lUrl = $short_to_long['long_url'];
            header("Location: $lUrl");
        }else{
            // 没有记录，跳转短链生成页面   (没有记录是否记录错误)
            $url = 'http://2dw.win';
            echo "<script> alert('未找到响应链接');location.href='$url';</script>";
        }

    }

    public function getrandomstring($len,$chars=null){
        if(is_null($chars)){
            $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        }
        mt_srand(1000*(double)time());
        $str = '';
        for($i = 0,$lc = strlen($chars)-1;$i<$len;$i++){
            $str.= $chars[mt_rand(0,$lc)];
        }
        return $str;
    }

    public function add()
    {
        
    }
}
