<?php
require_once dirname(getcwd()) . '/vendor/autoload.php';

use Qiniu\Auth;
use Qiniu\Storage\UploadManager;
use Alipay\Alipay;

class OperationController extends AdminBaseController{
    
    protected $_http;
    
    public function init()
    {
        $this->_http = new Yaf\Request\Http();
        parent::init();
    }
    
    /**
    * storePosition
    * 
    * @author kevin
    * @access public
    * @param mixed $code    
    * @return json
    */
    public function storePositionAction() {
        $page = $this->_http->getQuery('page', 1);
        $limit = $this->_http->getQuery('limit', 10);
        $offset = ($page - 1) * $limit;
        
        $pages = [];
        $lists = [];
        $queryString = '';
        
        
        $sql = "select * from network_store_position  ";
         
        $cntRes = DB::select($sql);
        
        $totalRecords = count($cntRes);
        $pages = ceil($totalRecords / $limit);
        $results = DB::select("$sql order by id desc limit $offset,$limit");
        $pages = Page($page, $pages, 'index.php?m=admin&c=operation&a=storePosition', $totalRecords, $queryString);
        
        foreach ($results as $row):
            $storeId = $row->store_id;
            $store = DB::connection('ant')->table('store')->where('id',$storeId)->first();
            $thumb = '';    
            
            $thumbs = DB::connection('ant')->table('store_thumb')->orderBy('id','asc')->first();
            if (!empty($thumbs)){
                $thumb = Yaf\Registry::get('config')->qiniu->url . $thumbs->qiniu_key;//'?imageView2/1/w/250/h/250'
            }
            
            $statusStr = '';
            //0转租中 1营业中
            if ($store->status==0){
                $statusStr = '转租中';
            }elseif ($store->status==1){
                $statusStr = '营业中';
            }
            
            $title = $store->province.$store->city.$store->district.$store->street.$store->build_area."平方米".$store->present_operation.$statusStr;
            $trade = $store->trade;
            $valid_date = date('Y-m-d',$row->start_date)."至".date('Y-m-d',$row->end_date);
            
            $lists[] = [
                'id'=>$row->id,
                'store_id'=>$storeId,
                'thumb'=>$thumb,
                'title'=>$title,
                'trade'=>$trade,
                'valid_date'=>$valid_date,
                'position'=>$row->position                
            ];
        endforeach;
        $data['lists'] = $lists;
        $data['pages'] = $pages;
        $data['totalRecords'] = $totalRecords;
        $this->getView()->assign($data);
        $this->getView()->display('admin/operation/storePosition.phtml');
        return false;
    }
    
    /**
    * create
    * 
    * @author kevin
    * @access public
    * @param mixed $code    
    * @return json
    */
    public function createAction() {
        
        
        $this->getView()->display('admin/operation/createPosition.phtml');
        return false;
    }
    
    /**
    * doCreatePostion
    * 
    * @author kevin
    * @access public
    * @param mixed $code    
    * @return json
    */
    public function doCreatePostionAction() {
        $storeId = intval($this->_http->getPost('store_id'));
        $position = intval($this->_http->getPost('position'));
        $start_date = $this->_http->getPost('start_date');
        $end_date   = $this->_http->getPost('end_date');
        
        if (strtotime($end_date)<strtotime($start_date)){
            echo json_encode(array('status'=>0,'msg'=>'生效结束时间不能小于生效开始时间'));
            exit;
        }

        $cntStore = DB::connection('ant')->table('store')->where('id',$storeId)->count();
        if ($cntStore<=0){
            echo json_encode(array('status'=>0,'msg'=>'店铺不存在'));
            exit;
        }
        
        $res = DB::table('store_position')->insert([
            'store_id'=>$storeId,
            'start_date'=>strtotime($start_date),
            'end_date'=>strtotime($end_date),
            'created_at'=>time(),
            'position'=>$position
        ]);
        
        if ($res){
            echo json_encode(array('status'=>1,'msg'=>'添加成功'));
            exit;
        }else{
            echo json_encode(array('status'=>0,'msg'=>'系统异常'));
            exit;
        }
        return false;
    }
    
    /**
    * editstorepostionAction
    * 
    * @author kevin
    * @access public
    * @param mixed $code    
    * @return json
    */
    public function editStorePostionAction() {
        $id = intval($this->_http->getQuery('id'));
        if ($id<=0){
            die('no access');
        }
        $row = DB::table('store_position')->where('id',$id)->first();
        
        $this->getView()->assign('row',$row);
        $this->getView()->display('admin/operation/editPosition.phtml');
        return false;
    }
    
    /**
    * doEditPostion
    * 
    * @author kevin
    * @access public
    * @param mixed $code    
    * @return json
    */
    public function doEditPostionAction() {
        $editId  = intval($this->_http->getPost('editId'));
        $storeId = intval($this->_http->getPost('store_id'));
        $position = intval($this->_http->getPost('position'));
        $start_date = $this->_http->getPost('start_date');
        $end_date   = $this->_http->getPost('end_date');
        
        if (strtotime($end_date)<strtotime($start_date)){
            echo json_encode(array('status'=>0,'msg'=>'生效结束时间不能小于生效开始时间'));
            exit;
        }
        
        $cntStore = DB::connection('ant')->table('store')->where('id',$storeId)->count();
        if ($cntStore<=0){
            echo json_encode(array('status'=>0,'msg'=>'店铺不存在'));
            exit;
        }
        
         
        $res = DB::table('store_position')->where('id',$editId)->update([
            'store_id'=>$storeId,
            'start_date'=>strtotime($start_date),
            'end_date'=>strtotime($end_date),
            'updated_at'=>time(),
            'position'=>$position
        ]);
        
        if ($res){
            echo json_encode(array('status'=>1,'msg'=>'操作成功'));
            exit;
        }else{
            echo json_encode(array('status'=>0,'msg'=>'系统异常'));
            exit;
        }        
        return false;
    }
    
    /**
    * deletePos
    * 
    * @author kevin
    * @access public
    * @param mixed $code    
    * @return json
    */
    public function deletePosAction() {
        $id = intval($this->_http->getPost('id'));
        if ($id<=0){
            echo json_encode(array('status'=>0,'msg'=>'未知错误'));
            exit;
        }
        
        $res = DB::table('store_position')->where('id',$id)->delete();
        if ($res){
            echo json_encode(array('status'=>1,'msg'=>'操作成功'));
            exit;
        }else{
            echo json_encode(array('status'=>0,'msg'=>'系统异常'));
            exit;
        }        
        return false;
    }
    
    /**
    * feedback
    * 
    * @author kevin
    * @access public
    * @param mixed $code    
    * @return json
    */
    public function feedbackAction() {
        $page = $this->_http->getQuery('page', 1);
        $limit = $this->_http->getQuery('limit', 10);
        $offset = ($page - 1) * $limit;
        
        $pages = [];
        $lists = [];
        $queryString = '';
        
        
        $sql = "select * from network_feedback  ";
        
        $cntRes = DB::select($sql);
        
        $totalRecords = count($cntRes);
        $pages = ceil($totalRecords / $limit);
        $results = DB::select("$sql order by id desc limit $offset,$limit");
        $pages = Page($page, $pages, 'index.php?m=admin&c=operation&a=feedback', $totalRecords, $queryString);
        
        foreach ($results as $row):
            
            $phone = '';
            $user = DB::table('user')->where('id',$row->user_id)->first();
            if (!empty($user)){
                $phone = $user->phone;
            }
         
            $lists[] = [
                'id'=>$row->id,
                'user_id'=>$row->user_id,
                'phone'=>$phone,
                'created_at'=>$row->created_at,
                'message'=>$row->message
            ];
        endforeach;
        $data['lists'] = $lists;
        $data['pages'] = $pages;
        $data['totalRecords'] = $totalRecords;
        $this->getView()->assign($data);
        $this->getView()->display('admin/operation/feedback.phtml');
        return false;
    }
    
    /**
    * showFeedBack
    * 
    * @author kevin
    * @access public
    * @param mixed $code    
    * @return json
    */
    public function showFeedBackAction() {
        $id = intval($this->_http->getQuery('id'));
        $row = DB::table('feedback')->where('id',$id)->first();
        
        $phone = '';
        $user = DB::table('user')->where('id',$row->user_id)->first();
        if (!empty($user)){
            $phone = $user->phone;
        }
        
        $data['row'] = $row;
        $data['phone'] = $phone;
        $this->getView()->assign($data);
        $this->getView()->display('admin/operation/showFeedBack.phtml');
        return false;
    }
    
    /**
    * upgradeAction
    * 
    * @author kevin
    * @access public
    * @param mixed $code    
    * @return json
    */
    public function upgradeAction() {
        $page = $this->_http->getQuery('page', 1);
        $limit = $this->_http->getQuery('limit', 10);
        $offset = ($page - 1) * $limit;
        
        $pages = [];
        $lists = [];
        $queryString = '';
        
        
        $sql = "select * from network_upgrade_version ";
        
        $cntRes = DB::select($sql);
        
        $totalRecords = count($cntRes);
        $pages = ceil($totalRecords / $limit);
        $results = DB::select("$sql order by id desc limit $offset,$limit");
        $pages = Page($page, $pages, 'index.php?m=admin&c=operation&a=upgrade', $totalRecords, $queryString);
        
        foreach ($results as $row):
            
            $upSystem = '';
            $up_system = $row->up_system;
            $up_system_arr = explode(',', $up_system);
            if (in_array(1, $up_system_arr)){
                $upSystem.="ios,";
            }
            if (in_array(2, $up_system_arr)){
                $upSystem.="android";
            }
            
            $upSystem=trim($upSystem,',');
            
            $upMethod = '';
            if ($row->up_method==1){
                $upMethod = '强制升级';
            }else{
                $upMethod = '提示升级';
            }
            
            $lists[] = [
                'id'=>$row->id,
                'version_sn'=>$row->version_sn,
                'up_system'=>$upSystem,
                'up_method'=>$upMethod,
                'message'=>$row->message,
                'remark'=>$row->remark,
                'created_at'=>$row->created_at
            ];
        endforeach;
        $data['lists'] = $lists;
        $data['pages'] = $pages;
        $data['totalRecords'] = $totalRecords;
        $this->getView()->assign($data);
        $this->getView()->display('admin/operation/upgrade.phtml');
        return false;
    }
    
    /**
    * createupgrade
    * 
    * @author kevin
    * @access public
    * @param mixed $code    
    * @return json
    */
    public function createUpgradeAction() {
        
        $this->getView()->display('admin/operation/createUpgrade.phtml');
        return false;
    }
    
    /**
    * doUpgrade
    * 
    * @author kevin
    * @access public
    * @param mixed $code    
    * @return json
    */
    public function doUpgradeAction() {
        $version_sn = abs($this->_http->getPost('version_sn'));
        $up_system  = $this->_http->getPost('up_system');
        $up_method  = $this->_http->getPost('up_method');
        $message    = nndealParam($this->_http->getPost('message'));
        $remark     = nndealParam($this->_http->getPost('remark'));
        
        $up_system = implode(',', $up_system);
        
        $res = DB::table('upgrade_version')->insert([
            'version_sn'=>$version_sn,
            'up_system'=>$up_system,
            'up_method'=>$up_method,
            'message'=>$message,
            'remark'=>$remark,
            'created_at'=>time()
        ]);
        
        if($res){
            echo json_encode([
                'status'=>1,
                'msg'=>'操作成功'
            ]);
        }else{
            echo json_encode([
                'status'=>0,
                'msg'=>'系统异常'
            ]);
        }
        return false;
    }
    
    /**
    * editUpgrade
    * 
    * @author kevin
    * @access public
    * @param mixed $code    
    * @return json
    */
    public function editUpgradeAction() {
        $id = intval($this->_http->getQuery('id'));
        if ($id<=0){
            die('no access');
        }
        
        $row = DB::table('upgrade_version')->where('id',$id)->first();
        
        $up_system_arr = explode(',', $row->up_system);
        
        $this->getView()->assign('up_system_arr',$up_system_arr);
        $this->getView()->assign('row',$row);
        $this->getView()->display('admin/operation/editUpgrade.phtml');
        return false;
    }
    
    /**
    * doEditUpgrade
    * 
    * @author kevin
    * @access public
    * @param mixed $code    
    * @return json
    */
    public function doEditUpgradeAction() {
        $editId     = intval($this->_http->getPost('editId'));
        $version_sn = abs($this->_http->getPost('version_sn'));
        $up_system  = $this->_http->getPost('up_system');
        $up_method  = $this->_http->getPost('up_method');
        $message    = nndealParam($this->_http->getPost('message'));
        $remark     = nndealParam($this->_http->getPost('remark'));
        
        $up_system = implode(',', $up_system);
        
        $res = DB::table('upgrade_version')->where('id',$editId)->update([
            'version_sn'=>$version_sn,
            'up_system'=>$up_system,
            'up_method'=>$up_method,
            'message'=>$message,
            'remark'=>$remark,
            'updated_at'=>time()
        ]);
        
        if($res){
            echo json_encode([
                'status'=>1,
                'msg'=>'操作成功'
            ]);
        }else{
            echo json_encode([
                'status'=>0,
                'msg'=>'系统异常'
            ]);
        }
        
        return false;
    }
    
    /**
    * deleteUpgrade
    * 
    * @author kevin
    * @access public
    * @param mixed $code    
    * @return json
    */
    public function deleteUpgradeAction() {
        $id = intval($this->_http->getPost('id'));
        if ($id<=0){
            die('no access.');
        }
        
        $res = DB::table('upgrade_version')->where('id',$id)->delete();
        if ($res){
            echo json_encode(array('status'=>1,'msg'=>'操作成功'));
            exit;
        }else{
            echo json_encode(array('status'=>0,'msg'=>'系统异常'));
            exit;
        }        
        return false;
    }
    
    
}