<?php
class UserController extends AdminBaseController{
    
    protected $_http;
    
    public function init()
    {
        $this->_http = new Yaf\Request\Http();
        parent::init();
    }
    
    public function indexAction()
    {
        $s_phone = $this->_http->getQuery('s_phone');
        $s_userid = $this->_http->getQuery('s_userid');
        
        $sortField = $this->_http->getQuery('sortField');
        $sortValue = $this->_http->getQuery('sortValue');
        
        if (empty($sortField)){
            $sortField='id';
        }
        if (empty($sortValue)){
            $sortValue='desc';
        }
        
        $page = $this->_http->getQuery('page', 1);
        $limit = $this->_http->getQuery('limit', 10);
        $offset = ($page - 1) * $limit;
        
        $pages = [];
        $lists = [];
        $queryString = '';
        
        if($sortField=='id' || $sortField=='created_at'){
        
            $condition = ' where 1=1 ';
            if (!empty($s_phone)){
                $condition.=" AND phone like '%".$s_phone."%' ";
                $queryString.="&s_phone={$s_phone}";
            }
            
            if (!empty($s_userid)){
                $condition.=" AND id={$s_userid} ";
                $queryString.="&s_userid={$s_userid}";
            }
            
            if (!empty($sortField)){
                $queryString.="&sortField=$sortField";
            }
            if (!empty($sortValue)){
                $queryString.="&sortValue=$sortValue";
            }
            
            
            $sql = "select * from network_user $condition ";
            $cntUser = DB::select($sql);
            
            $totalRecords = count($cntUser);
            $pages = ceil($totalRecords / $limit);
            $users = DB::select("$sql order by $sortField $sortValue limit $offset,$limit");
            $pages = Page($page, $pages, 'index.php?m=admin&c=user&a=index', $totalRecords, $queryString);
            
        }elseif($sortField=="brower_today"){
            
            $condition = ' where 1=1 ';
            if (!empty($s_phone)){
                $condition.=" AND a.phone like '%".$s_phone."%' ";
                $queryString.="&s_phone={$s_phone}";
            }
            
            if (!empty($s_userid)){
                $condition.=" AND a.id={$s_userid} ";
                $queryString.="&s_userid={$s_userid}";
            }
            
            if (!empty($sortField)){
                $queryString.="&sortField=$sortField";
            }
            if (!empty($sortValue)){
                $queryString.="&sortValue=$sortValue";
            }
            
            
           // $sql = "select * from network_user $condition ";
            
            $sql = "select a.*,b.vNum from network_user a left join ( ";
            $sql.="select count(id) as vNum,user_id from network_visits where FROM_UNIXTIME(created_at,'%Y-%m-%d')=".date('Y-m-d')." group by user_id ";
            $sql.=") b ON a.id=b.user_id ";
            $cntUser = DB::select($sql);
            
            $totalRecords = count($cntUser);
            $pages = ceil($totalRecords / $limit);  //order by b.vNum DESC
            $users = DB::select("$sql order by b.vNum $sortValue limit $offset,$limit");
            $pages = Page($page, $pages, 'index.php?m=admin&c=user&a=index', $totalRecords, $queryString);
            
        }elseif($sortField=="brower_seven"){
            
            $condition = ' where 1=1 ';
            if (!empty($s_phone)){
                $condition.=" AND a.phone like '%".$s_phone."%' ";
                $queryString.="&s_phone={$s_phone}";
            }
            
            if (!empty($s_userid)){
                $condition.=" AND a.id={$s_userid} ";
                $queryString.="&s_userid={$s_userid}";
            }
            
            if (!empty($sortField)){
                $queryString.="&sortField=$sortField";
            }
            if (!empty($sortValue)){
                $queryString.="&sortValue=$sortValue";
            }
            
            
            // $sql = "select * from network_user $condition ";
            
            $sql = "select a.*,b.vNum from network_user a left join ( ";
            $sql.="select count(id) as vNum,user_id from network_visits where DATEDIFF(DATE_FORMAT(NOW(),'%Y-%m-%d'),FROM_UNIXTIME(created_at,'%Y-%m-%d'))<=7 group by user_id ";
            $sql.=") b ON a.id=b.user_id ";
            $cntUser = DB::select($sql);
            
            $totalRecords = count($cntUser);
            $pages = ceil($totalRecords / $limit);  //order by b.vNum DESC
            $users = DB::select("$sql order by b.vNum $sortValue limit $offset,$limit");
            $pages = Page($page, $pages, 'index.php?m=admin&c=user&a=index', $totalRecords, $queryString);
            
        }
        
        foreach($users as $user):
            
            $subscribe = getUserSubscribe($user->id);
            $brower_today = getUserTodayBrower($user->id);
            $brower_seven = getUserSevenBrower($user->id);
            $lists[] = [
                'id'=>$user->id,
                'phone'=>$user->phone,
                'created_at'=>date('Y-m-d H:i:s',$user->created_at),
                'subscribe'=>$subscribe,
                'brower_today'=>$brower_today,
                'brower_seven'=>$brower_seven
            ];
        
        endforeach;
        
        $data['lists'] = $lists;
        $data['pages'] = $pages;
        $data['s_phone'] = $s_phone;
        $data['s_userid'] = $s_userid;
        $data['sortField'] = $sortField;
        $data['sortValue'] = $sortValue;
        $data['totalRecords'] = $totalRecords;
        
        $this->getView()->assign($data);
        $this->getView()->display('admin/user/index.phtml');
        return false;
    }
    
    /**
    * detailAction
    * 
    * @author kevin
    * @access public
    * @param mixed $code    
    * @return json
    */
    public function detailAction() {
        $id = intval( $this->_http->getQuery('id') );
        
        $user = DB::table('user')->where('id',$id)->first();
        
        $remarks = DB::table('user_remark')->where('user_id',$id)->orderBy('created_at','desc')->get();
        
        $this->getView()->assign('remarks',$remarks);
        $this->getView()->assign('user',$user);
        $this->getView()->display('admin/user/detail.phtml');
        return false;
    }
    
    /**
    * doremark
    * 
    * @author kevin
    * @access public
    * @param mixed $code    
    * @return json
    */
    public function doremarkAction() {
        $userId = intval($this->_http->getPost('user_id'));
        $remark = nndealParam($this->_http->getPost('remark'));
        
        $res = DB::table('user_remark')->insert([
            'user_id'=>$userId,
            'remark'=>$remark,
            'created_at'=>time()
        ]);
        
        if ($res){
            echo json_encode(['status'=>1]);
        }else{
            echo json_encode(['status'=>0,'msg'=>'系统异常']);
        }
        return false;
    }
    
    /**
    * browers
    * 
    * @author kevin
    * @access public
    * @param mixed $code    
    * @return json
    */
    public function browersAction() {
        $userId = intval($this->_http->getQuery('id'));
        $page = $this->_http->getQuery('page', 1);
        $limit = $this->_http->getQuery('limit', 10);
        $offset = ($page - 1) * $limit;
        
        $pages = [];
        $lists = [];
        $queryString = '&id='.$userId;
        
        $sql = "select * from network_browers where user_id=? ";
        
        $cntRes = DB::select($sql,[$userId]);
        
        $totalRecords = count($cntRes);
        $pages = ceil($totalRecords / $limit);
        $results = DB::select("$sql order by id desc limit $offset,$limit",[$userId]);
        $pages = Page($page, $pages, 'index.php?m=admin&c=user&a=browers', $totalRecords, $queryString);
        
        foreach ($results as $row):
            if($row->type==1){ //全网铺源
               
                $rs =  DB::connection('net')->table('store')->where('id',$row->store_id)->first();
                $statusStr = '';
                switch ($rs->status){
                    case 0:
                        $statusStr = '转租中';
                        break;
                    case 1:
                        $statusStr = '已出租';
                        break;
                    case 2:
                        $statusStr = '已删除';
                        break;
                }
                
                $lists[] = [
                    'type'=>1,
                    'id'=>$rs->id,
                    'status'=>$rs->status,
                    'status_str'=>$statusStr,
                    'thumb'=>$rs->thumb,
                    'title'=>$rs->title,
                    'address'=>$rs->address,
                    'rental_value'=>$rs->rental,
                    'rental_unit'=>$rs->rental_type,
                    'transfer'=>$rs->transfer,
                    'build_area'=>$rs->build_area,
                    'username'=>$rs->username,
                    'identity'=>$rs->identity,
                    'phone'=>$rs->phone,
                    'updated_at'=>$rs->updated_at,
                    'source_website'=>getNetStoreSource($rs->source_website),
                    'site_url'=>$rs->site_url
                ];
             }elseif ($row->type==2){  //魔方铺源
                 
                 $rs = DB::connection('ant')->table('store')->where('id',$row->store_id)->first();
                 
                 // thumb
                 $res = DB::connection('ant')->select("select qiniu_key from ant_store_thumb where store_id=? and type=4 order by id asc limit 1", [
                     $row->store_id
                 ]);
                 $thumb = '/resources/img/store_thumb.png';
                 if (! empty($res)) {
                     $thumb = Yaf\Registry::get('config')->qiniu->url . $res[0]->qiniu_key . '?imageView2/1/w/250/h/250';
                 }
                 
                 // 基础配套
                 $basic_installation_ids = $rs->basic_installation_ids;
                 $basic_installations = '';
                 if (! empty($basic_installation_ids)) {
                     $basic_installation_arr = explode(',', $basic_installation_ids);
                     foreach ($basic_installation_arr as $installation_id) :
                     $basic_installations .= getInstallationNameById($installation_id) . "、";
                     endforeach
                     ;
                     $basic_installations = preg_replace('/、$/', '', $basic_installations);
                 }
                 
                 // 额外配套
                 $extra_installation_ids = $rs->extra_installation_ids;
                 $extra_installations = '';
                 if (! empty($extra_installation_ids)) {
                     $extra_installation_arr = explode(',', $extra_installation_ids);
                     foreach ($extra_installation_arr as $installation_id) :
                     $extra_installations .= getInstallationNameById($installation_id) . "、";
                     endforeach
                     ;
                     $extra_installations = preg_replace('/、$/', '', $extra_installations);
                 }
                 
                 $oam_str = '';
                 $oam_times = $rs->oam_times==0?30:$rs->oam_times;
                 $created_at = $rs->created_at;
                 $updated_at = $rs->updated_at;
                 $updated_at = $rs->updated_at==0?$created_at:$rs->updated_at;
                 $oam_diff_days = getDayTimeDiff(date('Y-m-d'),date('Y-m-d',$rs->oam_datetime),'day');
                 
                 $is_oam_expire = 0;
                 
                 if ($oam_diff_days<=0){
                     $is_oam_expire = 1;
                     $oam_str = "<font style=\"color:red;\">维护期{$oam_times}天,需要更新</font>";
                 }else{
                     $oam_str = "<font style=\"color:#000;\">维护期{$oam_times}天,剩余{$oam_diff_days}天</font>";
                 }
                 
                 $tags_str = [];
                 if (!empty(trim($rs->tags,','))){
                     $arr_tags = explode(',', $rs->tags);
                     
                     $i=1;
                     foreach ($arr_tags as $tag):
                     if($i<=3){
                         array_push($tags_str, "<span class=\"label label-warning\">{$tag}</span>");
                     }
                     $i++;
                     endforeach;
                     
                 }
                 
                 
                 $lists[] = [
                     'type'=>2,
                     'id'=>$rs->id,
                     'province' => $rs->province,
                     'city' => $rs->city,
                     'district' => $rs->district,
                     'street' => $rs->street,
                     'present_operation' => empty($rs->present_operation) ? '暂无' : $rs->present_operation,
                     'status_str' => storeStatus($rs->status),
                     'status' => $rs->status,
                     'street_sn' => $rs->street_sn,
                     'thumb' => $thumb,
                     'build_area' => empty($rs->build_area)?0:$rs->build_area,
                     'rental_year' => floatval($rs->rental_year),
                     'transfer_fee' => floatval($rs->transfer_fee),
                     'operator_name' => getAntOperatorNameById($rs->operator_id),
                     'created_at' => date('Y年m月d日', $rs->created_at),
                     'memo_address' => $rs->memo_address,
                     'conditional_remark' => empty($rs->conditional_remark) ? '暂无' : $rs->conditional_remark,
                     'basic_installations' => empty($basic_installations . $extra_installations) ? '暂无' : $basic_installations . "、" . $extra_installations,
                     'door_width' => floatval($rs->door_width),
                     'property'=>$rs->property,
                     'is_oam_expire'=>$is_oam_expire,
                     'oam_str'=>$oam_str,
                     'trade'=>$rs->trade,
                     'floor_str'=>'共'.$rs->floor_total.'层，'.'第'.$rs->floor_start.'~'.$rs->floor_end.'层',
                     'tags_str'=>count($tags_str)>0?implode(' ', $tags_str):"&nbsp;"
                 ];
             }
           
        endforeach;
        
        $data['lists'] = $lists;
        $data['pages'] = $pages;
        $data['totalRecords'] = $totalRecords;
        $this->getView()->assign($data);
        $this->getView()->display('admin/user/browers.phtml');
        return false;
    }
   
    
    
}