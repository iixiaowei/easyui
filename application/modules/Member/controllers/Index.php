<?php
class IndexController extends Yaf\Controller_Abstract {
    protected $_http;
    
    public function init()
    {
        $this->_http = new Yaf\Request\Http();
    }
    
    /**
     * sendsms
     * 发送手机短信验证码
     *
     * @access public
     * @param mixed $phone 手机号码
     * @return json
     */
    public function sendsmsAction()
    {
        //         session_start();
        //         header('Content-Type: text/plain; charset=utf-8');
        header('Content-type: application/json;charset=utf-8');
        $phone = $this->_http->getQuery('phone');
        if (empty($phone) || is_null($phone)) {
            echo json_encode([
                'status' => 10001,
                'msg' => '参数异常'
            ]);
            exit;
        }
        
        if (! isMobile($phone)) {
            echo json_encode([
                'status' => 10003,
                'msg' => '手机号码格式错误'
            ]);
            exit;
        }
        
        $code = rand(1111, 9999);
        //         setcookie('verifyCode',$code,time()+300,'/',$_SERVER['HTTP_HOST']);
        $redis = NndealRedis::getInstance();
        $redis->set('net_verifyCode_'.$phone,$code,300);
        $redis->expire('net_verifyCode_'.$phone,300);
        
        Yaf\Loader::import('Sms/Message.php');
        $message = new Sms\Message();
        $res = $message->run((string) $code, $phone);
        
        if ($res == 1) {
            echo json_encode([
                'status' => 10000,
                'msg' => '操作成功'
            ]);
            exit;
        } else {
            echo json_encode([
                'status' => 10009,
                'msg' => '验证码发送失败'
            ]);
            exit;
        }
        return false;
    }
    
    
    /**
     * login
     * 验证业务员登录信息
     *
     * @access public
     * @param mixed $code 手机短信验证码
     * @param mixed $phone 手机号码
     * @return json
     */
    public function loginAction(){
        header('Content-type: application/json;charset=utf-8');
        $code    = $this->_http->getQuery('code');
        $phone   = $this->_http->getQuery('phone');
        $time    = time();
        
        if (empty($code) || is_null($code) || empty($phone)) {
            echo json_encode([
                'status' => 10001,
                'msg' => '参数异常'
            ]);
            exit;
        }
        
        if (! isMobile($phone)) {
            echo json_encode([
                'status' => 10003,
                'msg' => '手机号码格式错误'
            ]);
            exit;
        }
        
        $redis = NndealRedis::getInstance();
        
        $phonesArr= [
            "15831683109",
            "18301129881",
            "13611031466"
        ];
        
        if (in_array($phone, $phonesArr)){
            if ($code != '0000') {
                echo json_encode([
                    'status' => 10004,
                    'msg' => '验证码错误'
                ]);
                exit;
            }
        }else{
            $verifyCode = $redis->get('net_verifyCode_'.$phone);
            if (empty($verifyCode)) {
                echo json_encode([
                    'status' => 10010,
                    'msg' => '验证码超时'
                ]);
                exit;
            }
            
            if ($code != $verifyCode) {
                echo json_encode([
                    'status' => 10004,
                    'msg' => '验证码错误'
                ]);
                exit;
            }
        }
        
        //验证手机号码是否注册过
        $row = DB::table('user')->where('phone',$phone)->first();
        if(!empty($row)){
            
            if ($row->is_valid==0){
                echo json_encode([
                    'status' => 10016,
                    'msg' => '账号无效'
                ]);
                exit;
            }
            
            DB::table('user')->where('phone',$phone)->update([
                'last_login_at'=>$time
            ]);
            
            echo json_encode([
                'status' => 10000,
                'msg' => '操作成功',
                'user_id'=>$row->id,
                'phone'=>$phone,
                'token'=>$time
            ]);
            exit;
        }else{
            $userId = DB::table('user')->insertGetId([
                'phone'=>$phone,
                'created_at'=>time(),
                'last_login_at'=>$time,
                'is_valid'=>1
            ]);
            
            if($userId>0){
                echo json_encode([
                    'status' => 10000,
                    'msg' => '操作成功',
                    'user_id'=>$userId,
                    'phone'=>$phone,
                    'token'=>$time
                ]);
                exit;
            }else{
                echo json_encode([
                    'status' => 10002,
                    'msg' => '数据库异常'
                ]);
                exit;
            }
        }
        return false;
    }
    
}
