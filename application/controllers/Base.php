<?php
class BaseController extends Yaf\Controller_Abstract{
    
    const STATUS_OK                     = 10000;                  //操作成功
    const STATUS_PARAMS_EXCEPTION       = 10001;                  //参数异常
    const STATUS_DB_EXCEPTION           = 10002;                  //数据库异常
    const STATUS_PHONE_FORMAT_ERR       = 10003;                  //手机号码格式错误
    const STATUS_VERIFY_CODE_ERR        = 10004;                  //验证码输入错误
    const STATUS_RECORD_NOT_FOUND       = 10005;                  //记录不存在
    const STATUS_PHONE_EXISTS           = 10006;                  //手机号码已被绑定
    const STATUS_INVITECODE_ERR         = 10007;                  //邀请码错误
    const STATUS_INVITECODE_TIMEOUT     = 10008;                  //邀请码超时  15分钟
    const STATUS_VERIFY_CODESEND_RRR    = 10009;                  //验证码发送失败
    const STATUS_VERIFY_CODE_TIMEOUT    = 10010;                  //验证码超时
    const STATUS_RECORD_EXISTS          = 10011;                  //记录已存在
    const STATUS_RECORD_NOTSELF         = 10012;                  //不是自己发布的信息
    const STATUS_SIGN_ERROR             = 10013;                  //签名错误
    const STATUS_STORE_NOT_VALID        = 10014;                  //房屋无效
    const STATUS_STORE_SOURCE_ERROR     = 10015;                  //房屋性质错误
    const STATUS_NOT_VALID              = 10016;                  //无效
    
    public function init(){
        //$this->authCheck();
    }
    
    public function authCheck(){
        $ant_key = Yaf\Registry::get('config')->net_key;
        $http   = new Yaf\Request\Http();
        $params = $http->getQuery();
        $sign = $params['building_color'];
        $token = $params['token'];
        
        $userId = $params['user_id'];
        
        $row = DB::table('user')->where('id',$userId)->first();
        if (empty($row)){
            $this->writeJson([
                'status' => $this::STATUS_RECORD_NOT_FOUND,
                'msg' => '账号不存在'
            ]);
        }
        
        if($token!=$row->last_login_at){
            $this->writeJson(['status'=>$this::STATUS_SIGN_ERROR,'msg'=>'未知错误,请重新登录']);
        }
        
        if ($row->is_valid==0){
            $this->writeJson([
                'status' => $this::STATUS_NOT_VALID,
                'msg' => '无效账号'
            ]);
        }
        
        $redis = NndealRedis::getInstance();
        $keySignNndeal = 'net_'.$sign;
        if($redis->exists($keySignNndeal)){
            $this->writeJson(['status'=>$this::STATUS_SIGN_ERROR,'msg'=>'未知错误,请刷新']);
        }
        $redis->set($keySignNndeal,$keySignNndeal);
        $redis->expire($keySignNndeal,3600);
        
        $sign = substr_replace($sign,'',6,15);  //替换第6个字符开始后5位 为空      1231288888312312  ==》12312312312
        if (empty($sign)){
            $this->writeJson(['status'=>$this::STATUS_SIGN_ERROR,'msg'=>'未知错误,请刷新']);
        }
        
        unset($params['m']);
        unset($params['c']);
        unset($params['a']);
        unset($params['building_color']);
        
        ksort($params);  //按照键名从低到高进行排序
        $params_str = '&';
        foreach ($params as $key=>$val):
        $params_str.= $key."=".$val."&";
        endforeach;
        $params_str.="key=".$ant_key;
        $signStr = md5($params_str);
        
        if ($signStr!=$sign){
            $this->writeJson(['status'=>$this::STATUS_SIGN_ERROR,'msg'=>'未知错误,请刷新']);
        }
    }
    
    public static function writeJson($data=array()){
        header('Content-type: application/json');
        echo json_encode($data);
        exit;
    }
}