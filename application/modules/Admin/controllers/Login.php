<?php
class LoginController extends Yaf\Controller_Abstract{
    
    protected $_http;
    public function init(){
        $this->_http = new Yaf\Request\Http();
    }
    
    public function indexAction() {
//         if ($_SERVER['HTTP_HOST']!='tools.ddup.ren'){
//             die('no access.');
//         }
        
        $this->getView()->display('admin/login/index.phtml');
        return false;
    }
    
    
    public function sendSmsAction() {
        header('Content-Type: text/plain; charset=utf-8');
        $phone = $this->_http->getPost('phone');
        if (empty($phone) || is_null($phone)) {
            echo json_encode([
                'status' => 0,
                'msg' => '参数异常'
            ]);
            exit;
        }
        
        if (! isMobile($phone)) {
            echo json_encode([
                'status' => 0,
                'msg' => '手机号码格式错误'
            ]);
            exit;
        }
        
        $code = rand(1111, 9999);
        //         setcookie('verifyCode',$code,time()+300,'/',$_SERVER['HTTP_HOST']);
        $redis = NndealRedis::getInstance();
        $redis->set('ant_verifyCode_'.$phone,$code,300);
        $redis->expire('ant_verifyCode_'.$phone,300);
        
        Yaf\Loader::import('Sms/Message.php');
        $message = new Sms\Message();
        $res = $message->run((string) $code, $phone);
        
        if ($res == 1) {
            echo json_encode([
                'status' => 1,
                'msg' => '操作成功'
            ]);
            exit;
        } else {
            echo json_encode([
                'status' => 0,
                'msg' => '验证码发送失败'
            ]);
            exit;
        }
        
        return false;
    }
    
    public function checkLoginAction() {
        session_start();
        $phone = $this->_http->getPost('phone');
        $password = $this->_http->getPost('password');
        
        if (empty($phone) || empty($password)){
            echo json_encode([
                'status' => 0,
                'msg' => '参数异常'
            ]);
            exit;
        }
        
        if (! isMobile($phone)) {
            echo json_encode([
                'status' => 0,
                'msg' => '手机号码格式错误'
            ]);
            exit;
        }
        
        $redis = NndealRedis::getInstance();
        $verifyCode = $redis->get('ant_verifyCode_'.$phone);
        
        if (empty($verifyCode)) {
            echo json_encode([
                'status' => 0,
                'msg' => '短信验证码超时'
            ]);
            exit;
        }
        
        if ($password != $verifyCode) {
            echo json_encode([
                'status' => 0,
                'msg' => '短信验证码错误'
            ]);
            exit;
        }
        
        $row = DB::table('operator')->where('phone',$phone)->first();
        if(!empty($row)){
            
            if ($row->is_manager!=1){
                echo json_encode([
                    'status' => 0,
                    'msg' => '非法操作is_manager'
                ]);
                exit;
            }
            
            $loginIp = get_client_ip();
            
            $_SESSION['operator_id'] = $row->id;
            echo json_encode([
                'status' => 1,
                'msg' => '操作成功'
            ]);
            
            
        }else{
            echo json_encode([
                'status' => 0,
                'msg' => '非法操作'
            ]);
        }
        return false;
    }
    
    /**
     * logout
     *
     * @author kevin
     * @access public
     * @param mixed $code
     * @return json
     */
    public function logoutAction() {
        session_start();
        
        $_SESSION['operator_id'] = '';
        unset($_SESSION['operator_id']);
        $this->redirect("/index.php?m=admin&c=login&a=index");
        exit();
        return false;
    }
    
}