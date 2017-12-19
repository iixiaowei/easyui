<?php
class AdminBaseController extends Yaf\Controller_Abstract{
    
    protected $_operatorId = 0;
    protected $_operatorName = '';
    protected $_thumb='/resources/img/avatars/male.png';
    
    public function init(){
        session_start();
        $this->initSite();
    }
    
    public function initSite(){
        
        if (empty($_SESSION['operator_id'])){
            $this->redirect("/index.php?m=admin&c=login&a=index");
            exit();
        }
        
//         if ($_SERVER['HTTP_HOST']!='tools.ddup.ren'){
//             die('no access.');
//         }
        
        $this->_operatorId = $_SESSION['operator_id'];
        $this->_operatorName = getOperatorNameById($this->_operatorId);
        $this->getView()->assign('operatorName',$this->_operatorName);
        
        $operator = DB::table('operator')->where('id',$this->_operatorId)->first();
        $thumbInfo = $operator->thumb;
        if (!empty($thumbInfo)){
            $this->_thumb = Yaf\Registry::get('config')->qiniu->url .$thumbInfo. '?imageView2/1/w/100/h/100';
        }
        $this->getView()->assign('operatorThumb',$this->_thumb);
        
    }
    
}