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

    /**
     * 长链转短链
     * @param Request $request
     * @return array
     */
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
//        $city = $this->findCityByIp($ip);  // 根据ip 获取城市 taobao 带运营商

        $browser = $this->getBrowser();  // 获取浏览器信息
//        $browser = $this->get_broswer();  // 获取浏览器信息 带具体版本

        $os = $this->get_os();  // 获取系统信息



        if (isset($_SERVER['HTTP_X_UP_CALLING_LINE_ID']))
        {
            $getstr2 = $_SERVER['HTTP_X_UP_CALLING_LINE_ID'];
            echo $getstr2;
        }
        elseif (isset($_SERVER['HTTP_X_UP_SUBNO']))
        {
            $str3 = $_SERVER['HTTP_X_UP_SUBNO'];
            $getstr3 = preg_replace('/(.*)(11[d]{ 9 })(.*)/i','2',$str3);
            echo $getstr3;
        }
        elseif (isset($_SERVER['DEVICEID']))
        {
            Return $_SERVER['DEVICEID'];
        }
        else
        {
            echo $_SERVER['DEVICEID'];
        }


        dd(21);



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

    /**
     * 获取客户端操作系统信息包括win10
     * @param  null
     * @author  itdrean<itdream53@gmail.com>
     * @return string
     */
    public function get_os(){
        $agent = $_SERVER['HTTP_USER_AGENT'];
//        dd($agent);
        $os = false;

        if(strpos($agent, 'iPod')) {
            $os = 'ipod';
        }else if(strpos($agent, 'iPad')) {
            $os = 'ipad';
        }else if(strpos($agent, 'iPhone')) {
            $os = 'iphone';
        }else if (strpos($agent, 'Android')) {
            $os = 'android';
        }else if (preg_match('/win/i', $agent) && strpos($agent, '95'))
        {
            $os = 'Windows 95';
        }
        else if (preg_match('/win 9x/i', $agent) && strpos($agent, '4.90'))
        {
            $os = 'Windows ME';
        }
        else if (preg_match('/win/i', $agent) && preg_match('/98/i', $agent))
        {
            $os = 'Windows 98';
        }
        else if (preg_match('/win/i', $agent) && preg_match('/nt 6.0/i', $agent))
        {
            $os = 'Windows Vista';
        }
        else if (preg_match('/win/i', $agent) && preg_match('/nt 6.1/i', $agent))
        {
            $os = 'Windows 7';
        }
        else if (preg_match('/win/i', $agent) && preg_match('/nt 6.2/i', $agent))
        {
            $os = 'Windows 8';
        }else if(preg_match('/win/i', $agent) && preg_match('/nt 10.0/i', $agent))
        {
            $os = 'Windows 10';#添加win10判断
        }else if (preg_match('/win/i', $agent) && preg_match('/nt 5.1/i', $agent))
        {
            $os = 'Windows XP';
        }
        else if (preg_match('/win/i', $agent) && preg_match('/nt 5/i', $agent))
        {
            $os = 'Windows 2000';
        }
        else if (preg_match('/win/i', $agent) && preg_match('/nt/i', $agent))
        {
            $os = 'Windows NT';
        }
        else if (preg_match('/win/i', $agent) && preg_match('/32/i', $agent))
        {
            $os = 'Windows 32';
        }
        else if (preg_match('/linux/i', $agent))
        {
            $os = 'Linux';
        }
        else if (preg_match('/unix/i', $agent))
        {
            $os = 'Unix';
        }
        else if (preg_match('/sun/i', $agent) && preg_match('/os/i', $agent))
        {
            $os = 'SunOS';
        }
        else if (preg_match('/ibm/i', $agent) && preg_match('/os/i', $agent))
        {
            $os = 'IBM OS/2';
        }
        else if (preg_match('/Mac/i', $agent) && preg_match('/PC/i', $agent))
        {
            $os = 'Macintosh';
        }
        else if (preg_match('/PowerPC/i', $agent))
        {
            $os = 'PowerPC';
        }
        else if (preg_match('/AIX/i', $agent))
        {
            $os = 'AIX';
        }
        else if (preg_match('/HPUX/i', $agent))
        {
            $os = 'HPUX';
        }
        else if (preg_match('/NetBSD/i', $agent))
        {
            $os = 'NetBSD';
        }
        else if (preg_match('/BSD/i', $agent))
        {
            $os = 'BSD';
        }
        else if (preg_match('/OSF1/i', $agent))
        {
            $os = 'OSF1';
        }
        else if (preg_match('/IRIX/i', $agent))
        {
            $os = 'IRIX';
        }
        else if (preg_match('/FreeBSD/i', $agent))
        {
            $os = 'FreeBSD';
        }
        else if (preg_match('/teleport/i', $agent))
        {
            $os = 'teleport';
        }
        else if (preg_match('/flashget/i', $agent))
        {
            $os = 'flashget';
        }
        else if (preg_match('/webzip/i', $agent))
        {
            $os = 'webzip';
        }
        else if (preg_match('/offline/i', $agent))
        {
            $os = 'offline';
        }
        else
        {
            $os = '未知操作系统';
        }
        return $os;
    }

    /**
     * 根据ip获取城市、网络运营商等信息
     * @param $ip
     * @return mixed
     */
    public function findCityByIp($ip){
        $data = file_get_contents('http://ip.taobao.com/service/getIpInfo.php?ip='.$ip);
        return json_decode($data,$assoc=true);
    }

    /**
     * 获取用户浏览器类型
     * @return string
     */
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

    /**
     * 获取客户端浏览器信息 添加win10 edge浏览器判断
     * 获取浏览器信息带具体版本
     * @return string
     */
    public function get_broswer(){
        $sys = $_SERVER['HTTP_USER_AGENT'];  //获取用户代理字符串
        if (stripos($sys, "Firefox/") > 0) {
            preg_match("/Firefox\/([^;)]+)+/i", $sys, $b);
            $exp[0] = "Firefox";
            $exp[1] = $b[1];  //获取火狐浏览器的版本号
        } elseif (stripos($sys, "Maxthon") > 0) {
            preg_match("/Maxthon\/([\d\.]+)/", $sys, $aoyou);
            $exp[0] = "傲游";
            $exp[1] = $aoyou[1];
        } elseif (stripos($sys, "MSIE") > 0) {
            preg_match("/MSIE\s+([^;)]+)+/i", $sys, $ie);
            $exp[0] = "IE";
            $exp[1] = $ie[1];  //获取IE的版本号
        } elseif (stripos($sys, "OPR") > 0) {
            preg_match("/OPR\/([\d\.]+)/", $sys, $opera);
            $exp[0] = "Opera";
            $exp[1] = $opera[1];
        } elseif(stripos($sys, "Edge") > 0) {
            //win10 Edge浏览器 添加了chrome内核标记 在判断Chrome之前匹配
            preg_match("/Edge\/([\d\.]+)/", $sys, $Edge);
            $exp[0] = "Edge";
            $exp[1] = $Edge[1];
        } elseif (stripos($sys, "Chrome") > 0) {
            preg_match("/Chrome\/([\d\.]+)/", $sys, $google);
            $exp[0] = "Chrome";
            $exp[1] = $google[1];  //获取google chrome的版本号
        } elseif(stripos($sys,'rv:')>0 && stripos($sys,'Gecko')>0){
            preg_match("/rv:([\d\.]+)/", $sys, $IE);
            $exp[0] = "IE";
            $exp[1] = $IE[1];
        }else {
            $exp[0] = "未知浏览器";
            $exp[1] = "";
        }
        return $exp[0].'('.$exp[1].')';
    }


    /**
     * geoip2 根据ip获取 城市
     * @param $ip
     * @return array
     */
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

    /**
     * 获取统计uv 参数
     * @param $ip
     * @return string
     */
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

    /**
     *  获取短链部分
     * @param $len
     * @param null $chars
     * @return string
     */
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


    /**
     * 获取访客ip
     * @return array|false|string
     */
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

    /*
    //获取访客ip
    public function getIp()
    {
        $ip=false;
        if(!empty($_SERVER["HTTP_CLIENT_IP"])){
            $ip = $_SERVER["HTTP_CLIENT_IP"];
        }
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode (", ", $_SERVER['HTTP_X_FORWARDED_FOR']);
            if ($ip) { array_unshift($ips, $ip); $ip = FALSE; }
            for ($i = 0; $i < count($ips); $i++) {
                if (!eregi ("^(10│172.16│192.168).", $ips[$i])) {
                    $ip = $ips[$i];
                    break;
                }
            }
        }
        return ($ip ? $ip : $_SERVER['REMOTE_ADDR']);
    }*/

}
