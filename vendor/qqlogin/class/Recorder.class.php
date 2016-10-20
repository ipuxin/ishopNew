<?php
/**
 * 类的功能:
 * 配置,读写,session存取
 */
/* PHP SDK
 * @version 2.0.0
 * @author connect@qq.com
 * @copyright © 2013, Tencent Corporation. All rights reserved.
 */

require_once(QQ_CONNECT_SDK_CLASS_PATH . "ErrorCase.class.php");

class Recorder
{
    private static $data;
    private $inc;
    private $error;

    public function __construct()
    {
        $this->error = new ErrorCase();

        //配置参数
        $this->inc->appid = '101337109';
        $this->inc->appkey = '8e92e20bec8ca6e5f34b45b1a5ab1938';
        $this->inc->callback = 'http://ishop.ipuxin.com/index.php?r=member/qqcallback';
        $this->inc->scope = 'get_user_info,add_share,list_album,add_album,upload_pic,add_topic,add_one_blog,add_weibo,check_page_fans,add_t,add_pic_t,del_t,get_repost_list,get_info,get_other_info,get_fanslist,get_idolist,add_idol,del_idol,get_tenpay_addr';
        $this->inc->errorReport = 'true';
        $this->inc->storageType = 'file';

        if (empty($this->inc)) {
            $this->error->showError("20001");
        }

        if (empty($_SESSION['QC_userData'])) {
            self::$data = array();
        } else {
            self::$data = $_SESSION['QC_userData'];
        }
    }

    public function write($name, $value)
    {
        self::$data[$name] = $value;
    }

    public function read($name)
    {
        if (empty(self::$data[$name])) {
            return null;
        } else {
            return self::$data[$name];
        }
    }

    public function readInc($name)
    {
        if (empty($this->inc->$name)) {
            return null;
        } else {
            return $this->inc->$name;
        }
    }

    public function delete($name)
    {
        unset(self::$data[$name]);
    }

    function __destruct()
    {
        $_SESSION['QC_userData'] = self::$data;
    }
}
