<?php
/* PHP SDK
 * @version 2.0.0
 * @author connect@qq.com
 * @copyright © 2013, Tencent Corporation. All rights reserved.
 */

require_once(QQ_CONNECT_SDK_CLASS_PATH."Recorder.class.php");
require_once(QQ_CONNECT_SDK_CLASS_PATH."URL.class.php");
require_once(QQ_CONNECT_SDK_CLASS_PATH."ErrorCase.class.php");

class Oauth{

    const VERSION = "2.0";
    const GET_AUTH_CODE_URL = "https://graph.qq.com/oauth2.0/authorize";
    const GET_ACCESS_TOKEN_URL = "https://graph.qq.com/oauth2.0/token";
    const GET_OPENID_URL = "https://graph.qq.com/oauth2.0/me";

    protected $recorder;
    public $urlUtils;
    protected $error;


    function __construct(){
        $this->recorder = new Recorder();
        $this->urlUtils = new URL();
        $this->error = new ErrorCase();
    }

    /**
     * 拼接成如下链接,显示为点击允许访问的页面,来请求数据
     * https://graph.qq.com/oauth/show?which=Login&display=pc&response_type=code&client_id=101337109&redirect_uri=http://ishop.ipuxin.com/index.php?r=member/qqcallback&state=1b02258057f2a59782b4e01a210bf98d&scope=get_user_info,add_share,list_album,add_album,upload_pic,add_topic,add_one_blog,add_weibo,check_page_fans,add_t,add_pic_t,del_t,get_repost_list,get_info,get_other_info,get_fanslist,get_idolist,add_idol,del_idol,get_tenpay_addr
     *
     */
    public function qq_login(){
        $appid = $this->recorder->readInc("appid");
        $callback = $this->recorder->readInc("callback");
        //读取授权列表
        $scope = $this->recorder->readInc("scope");

        /**
         * 当用户使用qq登录后,除了返回code外,
         * 还会把我们发送过去的state原木原样的返回
         * 生成唯一随机串防CSRF攻击,原样返回参数
         */
        $state = md5(uniqid(rand(), TRUE));
        //将state写入session中
        $this->recorder->write('state',$state);

        //-------构造请求参数列表
        $keysArr = array(
            "response_type" => "code",
            "client_id" => $appid,
            "redirect_uri" => $callback,
            "state" => $state,
            "scope" => $scope
        );
        //调试: 查看返回的数据
//        var_dump($keysArr);exit();
        $login_url =  $this->urlUtils->combineURL(self::GET_AUTH_CODE_URL, $keysArr);
//        header("Location:$login_url");
        echo "<script>window.location.href='".$login_url."'</script>";
    }

    /**
     * QQ登录完成后的回调处理
     * @return mixed
     */
    public function qq_callback(){
        $state = $this->recorder->read("state");

        /**
         * 验证state防止CSRF攻击
         * 返回的state和本地的不一样,抛出异常提示
         */
        if($_GET['state'] != $state){
            $this->error->showError("30001");
        }

        //-------请求参数列表
        $keysArr = array(
            //这时,我们拿到了code,就可以告诉开放平台,我们要请求AccessToken
            "grant_type" => "authorization_code",
            "client_id" => $this->recorder->readInc("appid"),
            "redirect_uri" => urlencode($this->recorder->readInc("callback")),
            "client_secret" => $this->recorder->readInc("appkey"),
            "code" => $_GET['code']
        );

        //------构造请求access_token的url
        $token_url = $this->urlUtils->combineURL(self::GET_ACCESS_TOKEN_URL, $keysArr);
        $response = $this->urlUtils->get_contents($token_url);

        if(strpos($response, "callback") !== false){

            $lpos = strpos($response, "(");
            $rpos = strrpos($response, ")");
            $response  = substr($response, $lpos + 1, $rpos - $lpos -1);
            $msg = json_decode($response);

            if(isset($msg->error)){
                $this->error->showError($msg->error, $msg->error_description);
            }
        }

        $params = array();
        parse_str($response, $params);

        $this->recorder->write("access_token", $params["access_token"]);
        return $params["access_token"];

    }

    public function get_openid(){

        //-------请求参数列表
        $keysArr = array(
            "access_token" => $this->recorder->read("access_token")
        );

        $graph_url = $this->urlUtils->combineURL(self::GET_OPENID_URL, $keysArr);
        $response = $this->urlUtils->get_contents($graph_url);

        //--------检测错误是否发生
        if(strpos($response, "callback") !== false){

            $lpos = strpos($response, "(");
            $rpos = strrpos($response, ")");
            $response = substr($response, $lpos + 1, $rpos - $lpos -1);
        }

        $user = json_decode($response);
        if(isset($user->error)){
            $this->error->showError($user->error, $user->error_description);
        }

        //------记录openid
        $this->recorder->write("openid", $user->openid);
        return $user->openid;

    }
}
