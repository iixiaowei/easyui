<?php
require_once dirname(dirname(__DIR__)). '/vendor/jpush/jpush/autoload.php';

use JPush\Client as JPush;

class NndealJpush{
    private $_appKey = '';
    private $_masterSecret = '';
    private $_logFile = '';
    private $_retryTimes = '';
    private $_client = '';
    
    public function __construct(){
        
        $config = Yaf\Registry::get('config');
        
        $this->_client = new JPush($config->jpush->appKey, $config->jpush->masterSecret, dirname( getcwd() ) . $config->jpush->logFile,$config->jpush->retryTimes);
        
    }
    
    /**
     * 广播
     * @param unknown $content
     * @param string $log
     */
    public function push_all($content,$log=true){
        $result = $this->_client->push()
            ->setPlatform('all')
            ->setAudience('all')
            //->setNotificationAlert('Hi, JPush')
            ->addAndroidNotification($content,'', 1, array("key1"=>"value1", "key2"=>"value2"))
            ->addIosNotification($content, 'iOS sound', JPush::DISABLE_BADGE, true, 'iOS category')
            ->setOptions(100000, 3600, null, false)
            ->send();
        return $result;
    }
    
    //检查别名账号是否登录App
    public function checkAlias($aliasName){
        $response = $this->_client->_request("https://device.jpush.cn/v3/aliases/".$aliasName."?platform=android,ios", JPush::HTTP_GET);
        $exists = false;
        if($response['http_code'] === 200) {
            $strBody = $response['body'];
            $strBody = json_decode($strBody);
            
            if(count($strBody->registration_ids)>0){
                $exists = true;
            }
            
            ///$registration_ids = $strBody->registration_ids;
        }
        return $exists;
    }
    
    /**
     * 发送给指定别名
     * @param unknown $platform String|Array 指定的平台，不填表示所有的平台
     *                   String参数示例: $platform='ios,android'
     *                   Array参数示例: $platform=array('ios', 'android')
     * @param unknown $alias  String 指定的别名
     * 				     array('uid_1465367121','uid_1465290444')
     * @param unknown $content 内容
     * @param array $extras  扩展字段，这里自定义 Key/value 信息，以供业务使用
     * @param string $log    是否开启log
     *
     * $push->setOptions($sendno=null, $time_to_live=null, $override_msg_id=null, $apns_production=null, $big_push_duration=null)
     * 参数说明:
     * apns\_production:`Bool` APNs是否生产环境，True 表示推送生产环境，False 表示要推送开发环境；
     * 如果不指定则为推送生产环境。JPush 官方 API LIbrary (SDK) 默认设置为推送 “开发环境”。
     * 如未指定或者指定为`null`, 则默认指定为`false`
     */
    public function push($platform,$alias,$content,$extras=array(),$log=true,$sound="iOS sound")
    {
        $result = $this->_client->push()
                        ->setPlatform($platform)
                        ->addAlias($alias)
                        ->addAndroidNotification($content,'', 1, $extras)
                        ->addIosNotification($content, $sound, 0x10000 , true, 'iOS category', $extras)
                        ->setOptions(100000, 3600, null, false)
                        //  ->setMessage()
                        ->send();
        return $result;
    }
    
    public function pushAndroid($platform,$alias,$title,$content,$extras=array(),$log=true){
        $result = $this->_client->push()
                        ->setPlatform($platform)
                        ->addAlias($alias)
                        //->setAudience("all")
                        //->addAndroidNotification($content,'', 1, $extras)
                        //->addIosNotification($content, 'iOS sound', JPush::DISABLE_BADGE, true, 'iOS category', $extras)
                        ->setOptions(100000, 3600, null, false)
                        ->setMessage($content, $title, null,$extras)
                        ->send();
        return $result;
    }    
}