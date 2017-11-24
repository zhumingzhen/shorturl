<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Shorturl;
use GeoIp2\Database\Reader;


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
        // ****** 可以把 访问时间 ip 以及对应 短链记录到文件  每天0点执行定时任务 进行 数据库写入
        $ip = $this->getIp();  // 获取客户端ip

        $uvcookie = $this->getuvCookie($ip);  // 获取统计uv参数

        $city = $this->getCityByIp($ip);   // 根据ip 获取城市 可以考虑换 ip138

        $city = $this->findCityByIp($ip);
        dd($city);



        /*
        // 短链转长链
        $short_to_long = Shorturl::where('short_url',$short)->first();
        if ($short_to_long){
            // 有短链增加访问次数1
            $count = $short_to_long['count']+1;
            Shorturl::where('short_url',$short)->update(['count' => $count]);
            // 有短链记录跳转
            $lUrl = $short_to_long['long_url'];
            header("Location: $lUrl");
        }else{
            // 没有记录，跳转短链生成页面   (没有记录是否记录错误)
            $url = 'http://2dw.win';
            echo "<script> alert('未找到响应链接');location.href='$url';</script>";
        }
        */


    }

    //根据ip获取城市、网络运营商等信息
    public function findCityByIp($ip){
        $data = file_get_contents('http://ip.taobao.com/service/getIpInfo.php?ip='.$ip);
        return json_decode($data,$assoc=true);
    }

    //获取用户浏览器类型
    public function getBrowser(){
        $agent=$_SERVER["HTTP_USER_AGENT"];
        if(strpos($agent,'MSIE')!==false || strpos($agent,'rv:11.0')) //ie11判断
            return "ie";
        else if(strpos($agent,'Firefox')!==false)
            return "firefox";
        else if(strpos($agent,'Chrome')!==false)
            return "chrome";
        else if(strpos($agent,'Opera')!==false)
            return 'opera';
        else if((strpos($agent,'Chrome')==false)&&strpos($agent,'Safari')!==false)
            return 'safari';
        else
            return 'unknown';
    }


    public function getCityByIp($ip)
    {
        $reader = new Reader('/data/wwwroot/default/shorturl/public/GeoIP2-City.mmdb');
        $record = $reader->city($ip);

//        print($record->country->isoCode . "\n"); // 'US'
//        print($record->country->name . "\n"); // 'United States'
//        print($record->country->names['zh-CN'] . "\n"); // '美国'
//        print($record->mostSpecificSubdivision->name . "\n"); // 'Minnesota'
//        print($record->mostSpecificSubdivision->isoCode . "\n"); // 'MN'
//        print($record->city->name . "\n"); // 'Minneapolis'
//        print($record->postal->code . "\n"); // '55455'
//        print($record->location->latitude . "\n"); // 44.9733
//        print($record->location->longitude . "\n"); // -93.2323

//        print($record->country->names['zh-CN'] . "\n"); // '国家'
//        print($record->mostSpecificSubdivision->names['zh-CN'] . "\n"); // '省份'
//        print($record->city->names['zh-CN'] . "\n"); // '城市'

        $country = $record->country->names['zh-CN'];
        $province = $record->mostSpecificSubdivision->names['zh-CN'];
        $city = $record->city->names['zh-CN'];
        $ResCity = ['country'=>$country,'province'=>$province,'city'=>$city];

        return $ResCity;

    }

    public function getuvCookie($ip)
    {
        $expireTime = strtotime(date('Y-m-d',strtotime('+1 day')));
        $uvcookie = $expireTime.'-'.$ip;
        if(empty($_COOKIE['uvCookie'])){
            setcookie('uvCookie',$uvcookie, $expireTime);
        }else{
            $uvcookie = $_COOKIE['uvCookie'];
        }

        return $uvcookie;
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

    public function getIp()
    {
        if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
            $ip = getenv("HTTP_CLIENT_IP");
        else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
            $ip = getenv("REMOTE_ADDR");
        else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
            $ip = $_SERVER['REMOTE_ADDR'];
        else
            $ip = "unknown";
        return($ip);
    }
}
