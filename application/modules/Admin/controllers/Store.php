<?php
require_once dirname(getcwd()) . '/vendor/autoload.php';

use Qiniu\Auth;
use Qiniu\Storage\UploadManager;
use Alipay\Alipay;

class StoreController extends AdminBaseController{
    protected $_http;
    protected $_view;
    
    public function init()
    {
        $this->_http = new Yaf\Request\Http();
        $this->_view = $this->getView();
        parent::init();
    }
    
    /**
    * netStore
    * 
    * @author kevin
    * @access public
    * @param mixed $code    
    * @return json
    */
    public function netStoreAction() {
        $s_keyword    = $this->_http->getQuery('s_keyword', '');
        $s_district   = $this->_http->getQuery('s_district', '');
        $s_build_area = $this->_http->getQuery('s_build_area', '');
        $s_rental     = $this->_http->getQuery('s_rental','');
        $s_oam_times  = intval($this->_http->getQuery('s_oam_times'));
        $s_transfers  = $this->_http->getQuery('s_transfers');
        
        $data['s_keyword']    = $s_keyword;
        $data['s_district']   = $s_district;
        $data['s_build_area'] = $s_build_area;
        $data['s_rental']     = $s_rental;
        $data['s_oam_times']  = $s_oam_times;
        $data['s_transfers']  = $s_transfers;        
        
        $province   = '浙江省';
        $city       = '杭州市';
        // 选址区域
        $districtRes1[] = array(
            'nnid' => '',
            'name' => '全' . str_replace('市', '', $city)
        );
        $sql = "select c.name as nnid,c.name as name from network_province a,network_city b,network_area c ";
        $sql .= " where c.province_id=a.id and c.city_id=b.id and b.province_id=a.id and a.`name`=? and b.`name`=?";
        $districtRes = DB::select($sql, [
            $province,
            $city
        ]);
        foreach ($districtRes as $rs) :
        $districtRes1[] = [
            'nnid' => $rs->nnid,
            'name' => $rs->name
        ];
        endforeach
        ;
        
        // 租金范围
        $rentals[] = array(
            'nnid' => '',
            'name' => '不限'
        );
        $res = DB::table('rent')->select('id', 'name')
        ->where('is_valid', 1)
        ->orderBy('sort_by', 'desc')
        ->get();
        foreach ($res as $row) :
        $rentals[] = array(
            'nnid' => $row->id,
            'name' => $row->name
        );
        endforeach
        ;
        
        // 建筑面积
        $buildAreas = DB::table('build_area')->select('id', 'name')
        ->where('is_valid', 1)
        ->orderBy('sort_by', 'desc')
        ->get();
        $build_areas[] = array(
            'nnid' => '',
            'name' => '不限'
        );
        
        foreach ($buildAreas as $row) :
        $build_areas[] = array(
            'nnid' => $row->id,
            'name' => $row->name
        );
        endforeach
        ;
        
        //转让费
        $transfers = [
            ['id'=>1,'name'=>'0'],
            ['id'=>2,'name'=>'1万--5万'],
            ['id'=>3,'name'=>'5万--10万'],
            ['id'=>4,'name'=>'10万--20万'],
            ['id'=>5,'name'=>'20万--50万']
        ];
        

        $data['districts'] = $districtRes1;
        $data['rentals']   = $rentals;
        $data['build_areas'] = $build_areas;
        $data['transfers'] = $transfers;
        
        $lists = [];
        $pages = '';
        $queryString = '';
        $sql = "select * from network_store where 1=1 ";  
        
        
        
        if (!empty($s_district)){
            $ssDistrict = str_replace('区', '', $s_district);
            //$ssDistrict = str_replace('市', '', $s_district);
            //$ssDistrict = str_replace('县', '', $s_district);
            
            $sql.=" AND FIND_IN_SET(district,'{$ssDistrict}') ";
            $queryString .= "&s_district=$s_district";
        }
        
        if (intval($s_rental)>0){
            switch (intval($s_rental)){
                case 1:
                    $sql.=" AND (rental_year>1 and rental_year<=10) ";
                    $queryString.="&s_rental=$s_rental";
                    break;
                case 2:
                    $sql.=" AND (rental_year>10 and rental_year<=20) ";
                    $queryString.="&s_rental=$s_rental";
                    break;
                case 3:
                    $sql.=" AND (rental_year>20 and rental_year<=40) ";
                    $queryString.="&s_rental=$s_rental";
                    break;
                case 4:
                    $sql.=" AND (rental_year>40 and rental_year<=60) ";
                    $queryString.="&s_rental=$s_rental";
                    break;
                case 5:
                    $sql.=" AND (rental_year>60 and rental_year<=100) ";
                    $queryString.="&s_rental=$s_rental";
                    break;
                case 6:
                    $sql.=" AND rental_year>100 ";
                    $queryString.="&s_rental=$s_rental";
                    break;
                    
            }
        }
        
        if (! empty($s_build_area)) {
            switch ($s_build_area) {
                case 1: // 1-50平米
                    $sql .= " AND (build_area >1 and build_area<=50) ";
                    break;
                case 2: // 50-100平米
                    $sql .= " AND (build_area >50 and build_area<=100) ";
                    break;
                case 3: // 100-200平米
                    $sql .= " AND (build_area >100 and build_area<=200) ";
                    break;
                case 4: // 200-300平米
                    $sql .= " AND (build_area >200 and build_area<=300) ";
                    break;
                case 5: // 300-500平米
                    $sql .= " AND (build_area >300 and build_area<=500) ";
                    break;
                case 6:
                    $sql .= " AND build_area >500 ";
                    break;
            }
            $queryString.="&s_build_area=$s_build_area";
        }
        
        if (!empty($s_transfers)){
            
            switch ($s_transfers){
                case 1:  //0
                    $sql.=" AND transfer_fee = 0 ";
                    break;
                case 2:  //1万--5万 
                    $sql .= " AND (transfer_fee >1 and transfer_fee<=5) ";
                    break;
                case 3:  //5万--10万 
                    $sql .= " AND (transfer_fee >5 and transfer_fee<=10) ";
                    break;
                case 4:  //10万--20万 
                    $sql .= " AND (transfer_fee >10 and transfer_fee<=20) ";
                    break;
                case 5: //20万--50万
                    $sql .= " AND (transfer_fee >20 and transfer_fee<=50) ";
                    break;
            }
            $queryString.="&s_transfer=$s_transfers";            
        }
        
        if (!empty($s_oam_times)){
            if ($s_oam_times=="3天内"){
                $sql.=" AND DATEDIFF(FROM_UNIXTIME(UNIX_TIMESTAMP(),'%Y-%m-%d %H:%i:%s'),updated_at)<=3 ";
            }elseif ($s_oam_times=="7天内"){
                $sql.=" AND DATEDIFF(FROM_UNIXTIME(UNIX_TIMESTAMP(),'%Y-%m-%d %H:%i:%s'),updated_at)<=7 ";
            }
            $queryString.="&s_oam_times=$s_oam_times";
        }        
        
        
        $storeSearchIds = '';
        if (!empty($s_keyword)){
            
            try{
                $sphinx = new SphinxClient();
                $sphinx->setServer("localhost",9313);
                $sphinx->setMatchMode(SPH_MATCH_PHRASE);
                $result = $sphinx->query($s_keyword,"*");
                
                if (array_key_exists('matches', $result)){
                    $ids = array_keys($result['matches']);
                    $storeSearchIds = implode(',', $ids);
                    $sql.=" AND id in($storeSearchIds)   ";
                }
                
            }catch(Exception $e){
            }
            
        }
        
        $page = $this->_http->getQuery('page', 1);
        $limit = $this->_http->getQuery('limit', 10);
        $offset = ($page - 1) * $limit;
        $cntStore = DB::connection('net')->select($sql);
        
        $totalRecords = count($cntStore);
        $pages = ceil($totalRecords / $limit);
        $stores = DB::connection('net')->select("$sql ORDER BY UNIX_TIMESTAMP(updated_at) desc, id desc limit $offset,$limit");
        $pages = Page($page, $pages, 'index.php?m=admin&c=store&a=netStore', $totalRecords, $queryString);
        
        foreach($stores as $row):
            
            $statusStr = '';
            switch ($row->status){
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
            
        
            $lists[] = array(
                'id'=>$row->id,
                'status'=>$row->status,
                'status_str'=>$statusStr,
                'thumb'=>$row->thumb,
                'title'=>$row->title,
                'address'=>$row->address,
                'rental_value'=>$row->rental,
                'rental_unit'=>$row->rental_type,
                'transfer'=>$row->transfer,
                'build_area'=>$row->build_area,
                'username'=>$row->username,
                'identity'=>$row->identity,
                'phone'=>$row->phone,
                'updated_at'=>$row->updated_at,
                'source_website'=>getNetStoreSource($row->source_website),
                'site_url'=>$row->site_url
            );    
        endforeach;
        
        $data['lists'] = $lists;
        $data['pages'] = $pages;
        
        $this->_view->assign($data);
        $this->_view->display('admin/store/netStore.phtml');
        return false;
    }
    
    /**
    * changeStatus
    * 
    * @author kevin
    * @access public
    * @param mixed $code    
    * @return json
    */
    public function changeStatusAction() {
        $storeId = intval($this->_http->getQuery('id'));
        $store = DB::connection('net')->table('store')->select('id','status')->where('id',$storeId)->first();
        
        $this->_view->assign('store',$store);
        $this->_view->display('admin/store/changeStatus.phtml');
        return false;
    }
    
    /**
    * doStoreStatus
    * 
    * @author kevin
    * @access public
    * @param mixed $code    
    * @return json
    */
    public function doStoreStatusAction() {
        $storeId = intval($this->_http->getPost('store_id'));
        $status  = intval($this->_http->getPost('status'));
        
        $res = DB::connection('net')->table('store')->where('id',$storeId)->update([
            'status'=>$status
        ]);
        
        if ($res){
            echo json_encode(array('status'=>1,'msg'=>'操作成功'));
        }else{
            echo json_encode(array('status'=>0,'msg'=>'系统异常'));
        }
        return false;
    }
    
    /**
    * store
    * 
    * @author kevin
    * @access public
    * @param mixed $code    
    * @return json
    */
    public function storeAction() {
        $province = '浙江省';
        $city = '杭州市';
        // 选址区域
        $districtRes1[] = array(
            'nnid' => '',
            'name' => '全' . str_replace('市', '', $city)
        );
        $sql = "select c.name as nnid,c.name as name from ant_province a,ant_city b,ant_area c ";
        $sql .= " where c.province_id=a.id and c.city_id=b.id and b.province_id=a.id and a.`name`=? and b.`name`=?";
        $districtRes = DB::connection('ant')->select($sql, [
            $province,
            $city
        ]);
        foreach ($districtRes as $rs) :
        $districtRes1[] = [
            'nnid' => $rs->nnid,
            'name' => $rs->name
        ];
        endforeach
        ;
        
        // 建筑面积
        $buildAreas = DB::connection('ant')->table('build_area')->select('id', 'name')
        ->where('is_valid', 1)
        ->orderBy('sort_by', 'desc')
        ->get();
        $build_areas[] = array(
            'nnid' => '',
            'name' => '不限'
        );
        
        foreach ($buildAreas as $row) :
        $build_areas[] = array(
            'nnid' => $row->id,
            'name' => $row->name
        );
        endforeach
        ;
        
        // 租金范围
        $rentals[] = array(
            'nnid' => '',
            'name' => '不限'
        );
        $res = DB::connection('ant')->table('rent')->select('id', 'name')
        ->where('is_valid', 1)
        ->orderBy('sort_by', 'desc')
        ->get();
        foreach ($res as $row) :
        $rentals[] = array(
            'nnid' => $row->id,
            'name' => $row->name
        );
        endforeach
        ;
        
        $queryString = '';
        $s_keyword    = $this->_http->getQuery('s_keyword', '');
        $s_district   = $this->_http->getQuery('s_district', '');
        $s_build_area = $this->_http->getQuery('s_build_area', '');
        $s_property   = $this->_http->getQuery('s_property','');
        $s_trade      = $this->_http->getQuery('s_trade','');
        $s_rental     = $this->_http->getQuery('s_rental','');
        $s_tags       = $this->_http->getQuery('s_tags','');
        $s_oam_times  = $this->_http->getQuery('s_oam_times','');
        $s_is_valid   = $this->_http->getQuery('s_is_valid','');
        $s_source     = $this->_http->getQuery('s_source','');
        
        
        $sql = "select * from ant_store where 1=1 ";  //is_valid=1
        
        if (!empty($s_district)){
            $sql.=" AND district='".$s_district."' ";
            $queryString .= "&s_district=$s_district";
        }
        
        if (!empty($s_property)){
            $sql.=" AND property='".$s_property."' ";
            $queryString.="&s_property=$s_property";
        }
        
        if (!empty($s_trade)){
            $sql.=" AND trade='".$s_trade."' ";
            $queryString.="&s_trade=$s_trade";
        }
        
        if (intval($s_rental)>0){
            switch (intval($s_rental)){
                case 1:
                    $sql.=" AND (rental_year>1 and rental_year<=10) ";
                    $queryString.="&s_rental=$s_rental";
                    break;
                case 2:
                    $sql.=" AND (rental_year>10 and rental_year<=20) ";
                    $queryString.="&s_rental=$s_rental";
                    break;
                case 3:
                    $sql.=" AND (rental_year>20 and rental_year<=40) ";
                    $queryString.="&s_rental=$s_rental";
                    break;
                case 4:
                    $sql.=" AND (rental_year>40 and rental_year<=60) ";
                    $queryString.="&s_rental=$s_rental";
                    break;
                case 5:
                    $sql.=" AND (rental_year>60 and rental_year<=100) ";
                    $queryString.="&s_rental=$s_rental";
                    break;
                case 6:
                    $sql.=" AND rental_year>100 ";
                    $queryString.="&s_rental=$s_rental";
                    break;
                    
            }
        }
        
        if (! empty($s_build_area)) {
            switch ($s_build_area) {
                case 1: // 1-50平米
                    $sql .= " AND (build_area >1 and build_area<=50) ";
                    break;
                case 2: // 50-100平米
                    $sql .= " AND (build_area >50 and build_area<=100) ";
                    break;
                case 3: // 100-200平米
                    $sql .= " AND (build_area >100 and build_area<=200) ";
                    break;
                case 4: // 200-300平米
                    $sql .= " AND (build_area >200 and build_area<=300) ";
                    break;
                case 5: // 300-500平米
                    $sql .= " AND (build_area >300 and build_area<=500) ";
                    break;
                case 6:
                    $sql .= " AND build_area >500 ";
                    break;
            }
            $queryString.="&s_build_area=$s_build_area";
        }
        
        if (!empty($s_tags)){
            $sql.=" AND FIND_IN_SET('".$s_tags."',tags)>0 ";
        }
        
        if (!empty($s_is_valid)){
            if ($s_is_valid=="待审核"){
                $sql.=" AND is_valid=0 ";
            }elseif ($s_is_valid=="已发布"){
                $sql.=" ANd is_valid=1 ";
            }
            $queryString.="&s_is_valid=$s_is_valid";
        }
        if (!empty($s_source)){
            if ($s_source=="正常扫铺"){
                $sql.=" AND source='".$s_source."' ";
            }elseif($s_source=="挖铺"){
                $sql.=" AND source='".$s_source."' ";
            }
            $queryString.="&s_source=$s_source";
        }
        
        if (!empty($s_oam_times)){
            if ($s_oam_times=="已过期"){
                $sql.=" AND DATEDIFF(FROM_UNIXTIME(UNIX_TIMESTAMP(),'%Y-%m-%d %H:%i:%s'),FROM_UNIXTIME(updated_at,'%Y-%m-%d %H:%i:%s'))>=oam_times ";
            }elseif ($s_oam_times=="3天内"){
                $sql.=" AND DATEDIFF(FROM_UNIXTIME(UNIX_TIMESTAMP(),'%Y-%m-%d %H:%i:%s'),FROM_UNIXTIME(updated_at,'%Y-%m-%d %H:%i:%s'))>=(oam_times-3) ";
            }elseif ($s_oam_times=="7天内"){
                $sql.=" AND DATEDIFF(FROM_UNIXTIME(UNIX_TIMESTAMP(),'%Y-%m-%d %H:%i:%s'),FROM_UNIXTIME(updated_at,'%Y-%m-%d %H:%i:%s'))>=(oam_times-7) ";
            }
            $queryString.="&s_oam_times=$s_oam_times";
        }
        
        $storeSearchIds = '';
        if (!empty($s_keyword)){
            
            try{
                $sphinx = new SphinxClient();
                $sphinx->setServer("localhost",9312);
                $sphinx->setMatchMode(SPH_MATCH_ANY);
                $result = $sphinx->query($s_keyword,"*");
                
                if (array_key_exists('matches', $result)){
                    $ids = array_keys($result['matches']);
                    $storeSearchIds = implode(',', $ids);
                    $sql.=" AND id in($storeSearchIds)   ";
                }
                
            }catch(Exception $e){
            }
        }
        
        $page = $this->_http->getQuery('page', 1);
        $limit = $this->_http->getQuery('limit', 10);
        $offset = ($page - 1) * $limit;
        
        $cntStore = DB::connection('ant')->select($sql);
        
        $totalRecords = count($cntStore);
        $pages = ceil($totalRecords / $limit);
        $stores = DB::connection('ant')->select("$sql order by status asc,id desc limit $offset,$limit");
        $pages = Page($page, $pages, 'index.php?m=admin&c=store&a=store', $totalRecords, $queryString);
        
        $lists = [];
        foreach ($stores as $row):
        // thumb
        $res = DB::connection('ant')->select("select qiniu_key from ant_store_thumb where store_id=? and type=4 order by id asc limit 1", [
            $row->id
        ]);
        $thumb = '/resources/img/store_thumb.png';
        if (! empty($res)) {
            $thumb = Yaf\Registry::get('config')->qiniu->url . $res[0]->qiniu_key . '?imageView2/1/w/250/h/250';
        }
        
        // 基础配套
        $basic_installation_ids = $row->basic_installation_ids;
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
        $extra_installation_ids = $row->extra_installation_ids;
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
        $oam_times = $row->oam_times==0?30:$row->oam_times;
        $created_at = $row->created_at;
        $updated_at = $row->updated_at;
        $updated_at = $row->updated_at==0?$created_at:$row->updated_at;
        $oam_diff_days = getDayTimeDiff(date('Y-m-d'),date('Y-m-d',$row->oam_datetime),'day');
        
        $is_oam_expire = 0;
        
        if ($oam_diff_days<=0){
            $is_oam_expire = 1;
            $oam_str = "<font style=\"color:red;\">维护期{$oam_times}天,需要更新</font>";
        }else{
            $oam_str = "<font style=\"color:#000;\">维护期{$oam_times}天,剩余{$oam_diff_days}天</font>";
        }
        
        $tags_str = [];
        if (!empty(trim($row->tags,','))){
            $arr_tags = explode(',', $row->tags);
            
            $i=1;
            foreach ($arr_tags as $tag):
            if($i<=3){
                array_push($tags_str, "<span class=\"label label-warning\">{$tag}</span>");
            }
            $i++;
            endforeach;
            
        }
        
        $lists[] = array(
            'id'=>$row->id,
            'province' => $row->province,
            'city' => $row->city,
            'district' => $row->district,
            'street' => $row->street,
            'present_operation' => empty($row->present_operation) ? '暂无' : $row->present_operation,
            'status_str' => storeStatus($row->status),
            'status' => $row->status,
            'street_sn' => $row->street_sn,
            'thumb' => $thumb,
            'build_area' => empty($row->build_area)?0:$row->build_area,
            'rental_year' => floatval($row->rental_year),
            'transfer_fee' => floatval($row->transfer_fee),
            'operator_name' => getAntOperatorNameById($row->operator_id),
            'created_at' => date('Y年m月d日', $row->created_at),
            'memo_address' => $row->memo_address,
            'conditional_remark' => empty($row->conditional_remark) ? '暂无' : $row->conditional_remark,
            'basic_installations' => empty($basic_installations . $extra_installations) ? '暂无' : $basic_installations . "、" . $extra_installations,
            'door_width' => floatval($row->door_width),
            'property'=>$row->property,
            'is_oam_expire'=>$is_oam_expire,
            'oam_str'=>$oam_str,
            'trade'=>$row->trade,
            'floor_str'=>'共'.$row->floor_total.'层，'.'第'.$row->floor_start.'~'.$row->floor_end.'层',
            'tags_str'=>count($tags_str)>0?implode(' ', $tags_str):"&nbsp;"
        );
        endforeach;
        
        
        $this->getView()->assign('s_keyword', $s_keyword);
        $this->getView()->assign('s_district', $s_district);
        $this->getView()->assign('s_build_area', $s_build_area);
        $this->getView()->assign('s_rental', $s_rental);
        $this->getView()->assign('s_property',$s_property);
        $this->getView()->assign('s_trade',$s_trade);
        $this->getView()->assign('s_tags',$s_tags);
        $this->getView()->assign('s_oam_times',$s_oam_times);
        $this->getView()->assign('s_is_valid',$s_is_valid);
        $this->getView()->assign('s_source',$s_source);
        $this->getView()->assign('build_areas', $build_areas);
        $this->getView()->assign('districts', $districtRes1);
        $this->getView()->assign('rentals', $rentals);
        $this->getView()->assign('lists', $lists);
        $this->getView()->assign('pages', $pages);
        $this->getView()->assign('keyword', '');
        $this->getView()->display('admin/store/store.phtml');
        return false;
    }
    
    /**
     * details
     *
     * @author kevin
     * @access public
     * @param mixed $code
     * @return json
     */
    public function detailsAction() {
        $storeId = intval($this->_http->getQuery('id'));
        if ($storeId<=0){
            die;
        }
        
        $store = DB::connection("ant")->table('store')->where('id',$storeId)->first();
        
        $status_str = '';
        switch ($store->status) {
            case 0:
                $status_str = "转租中";
                break;
            case 1:
                $status_str = "营业中";
                break;
        }
        
        $contacts = '';
        
        $cs = DB::connection('ant')->table('store_call')->where('store_id',$storeId)->first();
        if (!empty($cs)){
            $contacts.=$cs->contacts." ".$cs->phone." "."({$cs->role_name})";
        }
        
        $basic_installation_ids = trim($store->basic_installation_ids, ',');
        $extra_installation_ids = trim($store->extra_installation_ids, ',');
        $installation_str = '';
        $basic_installation_arr = explode(',', $basic_installation_ids);
        $extra_installation_arr = explode(',', $extra_installation_ids);
        foreach ($basic_installation_arr as $installation_id) :
        $installation_str .= getInstallationNameById($installation_id) . "、";
        endforeach
        ;
        $installation_str = preg_replace('/、$/', '', $installation_str);
        foreach ($extra_installation_arr as $installation_id) :
        $installation_str .= getInstallationNameById($installation_id) . "、";
        endforeach
        ;
        $installation_str = preg_replace('/、$/', '', $installation_str);
        
        $tags = [];
        if (!empty($store->tags)){
            $tagArr = explode(',', trim($store->tags,','));
            foreach ($tagArr as $tag):
            array_push($tags, "<span class=\"label label-warning\">{$tag}</span>");
            endforeach;
        }
        
        $rowThumb = DB::connection('ant')->table('store_thumb')->select('id', 'qiniu_key')
        ->where([
            'type' => 4,
            'store_id' => $storeId
        ])
        ->orderBy('id', 'asc')
        ->get();
        $thumbs = array();
        if (! empty($rowThumb)) {
            foreach ($rowThumb as $thumb) :
            $thumbs[] = array(
                'qiniu_key' => $thumb->qiniu_key,
                'original_url' => Yaf\Registry::get('config')->qiniu->url . $thumb->qiniu_key,
                'thumb_url' => Yaf\Registry::get('config')->qiniu->url . $thumb->qiniu_key . '?imageView2/1/w/225/h/225'
            );
            endforeach
            ;
        }
        
        $data['thumbs'] = $thumbs;
        $data['tags'] = $tags;
        $data['contacts'] = $contacts;
        $data['status_str'] = $status_str;
        $data['installation_str'] = $installation_str;
        $data['store'] = $store;
        $this->getView()->assign($data);
        $this->getView()->display('admin/store/details.phtml');
        return false;
    }
    
    /**
     * updateStore
     * 修改店铺
     *
     * @author kevin
     * @access public
     * @param mixed $code
     * @return json
     */
    public function updateStoreAction() {
        $storeId = $this->_http->getQuery('id');
        $store = DB::connection('ant')->table('store')->where('id', $storeId)->first();
        
        // 基础配套
        $basicInfo = DB::connection('ant')->select("select * from ant_installation where type=1 and is_valid=1 order by sort_by desc,id desc");
        // 额外配套
        $extraInfo = DB::connection('ant')->select("select * from ant_installation where type=2 and is_valid=1 order by sort_by desc,id desc");
        
        $checkedBasic = trim($store->basic_installation_ids, ',');
        $checkedBasic = explode(',', $checkedBasic);
        $checkedExtra = trim($store->extra_installation_ids, ',');
        $checkedExtra = explode(',', $checkedExtra);
        
        //标签
        $tagsInfo = [
            "店铺",
            "小区门口",
            "公交站",
            "地铁站",
            "十字路口",
            "金角",
            "超市入口",
            "景区",
            "正对斑马线",
            "有烟草证",
            "有餐饮证",
            "展示面好"
        ];
        
        // 已选标签
        $storeTagsIds = trim($store->tags, ',');
        $storeTags = array();
        if (! empty($storeTagsIds)) {
            $storeTagsIds = explode(',', $storeTagsIds);
            foreach ($storeTagsIds as $tag_id) :
            array_push($storeTags, $tag_id);
            endforeach
            ;
        }
        
        // 联系人
        $contacts = DB::connection('ant')->table('store_call')->where('store_id', $storeId)->get();
        
        $rental_value = '';
        switch ($store->rental_type) {
            case 1:
                $rental_value = $store->rental_day;
                break;
            case 2:
                $rental_value = $store->rental_month;
                break;
            case 3:
                $rental_value = $store->rental_year;
                break;
        }
        
        // 缩略图
        $rowThumb = DB::connection('ant')->table('store_thumb')->select('id', 'qiniu_key')
        ->where([
            'type' => 4,
            'store_id' => $storeId
        ])
        ->orderBy('id', 'asc')
        ->get();
        $thumbs = array();
        if (! empty($rowThumb)) {
            foreach ($rowThumb as $thumb) :
            $thumbs[] = array(
                'qiniu_key' => $thumb->qiniu_key,
                'original_url' => Yaf\Registry::get('config')->qiniu->url . $thumb->qiniu_key,
                'thumb_url' => Yaf\Registry::get('config')->qiniu->url . $thumb->qiniu_key . '?imageView2/1/w/225/h/225'
            );
            endforeach
            ;
        }
        
        //物业性质
        $property_attrs = [
            "商业街商铺",
            "写字楼配套",
            "社区底商",
            "档口摊位",
            "临街门面",
            "购物百货中心"
        ];
        
        //当前行业
        $trade_attrs = [
            "酒楼餐饮",
            "生活服务",
            "零售百货",
            "旅馆宾馆",
            "教育培训",
            "汽车美容",
            "其他",
            "空铺"
        ];
        
        
        $payments_attrs = [
            1,2,3,4,5,6,7,8,9,10,11,12
        ];
        
        $this->getView()->assign('payments_attrs',$payments_attrs);
        $this->getView()->assign('trade_attrs',$trade_attrs);
        $this->getView()->assign('property_attrs',$property_attrs);
        $this->getView()->assign('thumbs', $thumbs);
        $this->getView()->assign('rental_value',$rental_value);
        $this->getView()->assign('contacts',$contacts);
        $this->getView()->assign('storeTags',$storeTags);
        $this->getView()->assign('tagsInfo',$tagsInfo);
        $this->getView()->assign('basicInfo',$basicInfo);
        $this->getView()->assign('checkedBasic',$checkedBasic);
        $this->getView()->assign('checkedExtra', $checkedExtra);
        $this->getView()->assign('extraInfo', $extraInfo);
        $this->getView()->assign('store',$store);
        $this->getView()->display('admin/store/update.phtml');
        return false;
    }
    
    
    
    /**
     * doUpdateStore
     *
     * @author kevin
     * @access public
     * @param mixed $code
     * @return json
     */
    public function doUpdateStoreAction() {
        $storeId = intval( $this->_http->getPost('store_id') );
        $operatorId = intval( $this->_operatorId );
        $street_sn = nndealParam( $this->_http->getPost('street_sn') );
        $memo_address = nndealParam( $this->_http->getPost('memo_address') );
        $present_operation = nndealParam( $this->_http->getPost('present_operation') );
        $trade = nndealParam($this->_http->getPost('trade'));
        $property = nndealParam($this->_http->getPost('property'));
        $basic_installation_ids = $this->_http->getPost('basic_installation_ids'); // array
        $extra_installation_ids = $this->_http->getPost('extra_installation_ids'); // array
        $door_width = $this->_http->getPost('door_width');
        $floor_total = $this->_http->getPost('floor_total');
        $floor_start = $this->_http->getPost('floor_start');
        $floor_end = $this->_http->getPost('floor_end');
        $build_area = $this->_http->getPost('build_area');
        $use_area = $this->_http->getPost('use_area');
        $conditional_remark = nndealParam($this->_http->getPost('conditional_remark'));
        $number = $this->_http->getPost('number'); // role_name_ array
        $contacts = $this->_http->getPost('contacts'); // array
        $phone = $this->_http->getPost('phone'); // array
        $wechat = $this->_http->getPost('wechat'); // array
        
        $rental_value = floatval($this->_http->getPost('rental_value'));
        $rental_type = intval($this->_http->getPost('rental_type'));
        $transfer_fee = floatval($this->_http->getPost('transfer_fee'));
        $charge_remark = nndealParam($this->_http->getPost('charge_remark'));
        $payment_deposit = $this->_http->getPost('payment_deposit');
        $payment_pay     = $this->_http->getPost('payment_pay');
        $residual_lease  = $this->_http->getPost('residual_lease');
        $min_lease       =  $this->_http->getPost('min_lease');
        
        $tag_ids = $this->_http->getPost('tag_ids');  //array
        $source = $this->_http->getPost('source');
        $status = $this->_http->getPost('status');
        $store_thumbs = trim($this->_http->getPost('store_thumbs'),',');
        
        $oam_times   = intval($this->_http->getPost('oam_times'));
        $time = time();
        
        $province = nndealParam( $this->_http->getPost('province') );
        $city     = nndealParam( $this->_http->getPost('city') );
        $district = nndealParam( $this->_http->getPost('district') );
        $street   = nndealParam( $this->_http->getPost('street') );
        $location_lat = $this->_http->getPost('location_lat');
        $location_lng = $this->_http->getPost('location_lng');
        
        if ( $operatorId <= 0 || $storeId <= 0 || empty($province) || empty($district) || empty($street) || empty($location_lat) || empty($location_lng)) {
            echo json_encode([
                'status' => 0,
                'msg' => '参数异常'
            ]);
            exit();
        }
        
        if ( empty($memoAddress) && empty($street_sn)){
            echo json_encode([
                'status' => 0,
                'msg' => '街道编号或备注地址必须任填一项'
            ]);
            exit();
        }
        
        // 这里只容许输入数字、和特殊符号“-”，文字‘号’是写完数字后，程序自动增加的，
        // 其他字符都不让容许，街铺号组合街铺是杭州商铺的唯一身份证ID。例如 129-1号
        if (! preg_match("/^[0-9]*(\-)*[0-9]*(\-)*[0-9]*$/", $street_sn)) {
            echo json_encode([
                'status' => 0,
                'msg' => '街铺编号格式错误'
            ]);
            exit();
        }
        
        if (empty($build_area)) {
            echo json_encode([
                'status' => 0,
                'msg' => '建筑面积必填'
            ]);
            exit();
        }
        
        if (! empty($rental_value)) {
            if (empty($rental_type)) {
                echo json_encode([
                    'status' => 0,
                    'msg' => '请选择租金方式'
                ]);
                exit();
            }
        }
        
        if (! empty($floor_total) && ! preg_match("/^[-+]?[0-9]*\.?[0-9]+$/", $floor_total)) {
            echo json_encode([
                'status' => 0,
                'msg' => '楼层只能输入数字或小数'
            ]);
            exit();
        }
        
        if ( !empty($residual_lease) && ! preg_match("/^[-+]?[0-9]*\.?[0-9]+$/", $floor_total) ){
            echo json_encode([
                'status' => 0,
                'msg' => '剩余租约只能输入数字或小数'
            ]);
            exit();
        }
        
        if ( !empty($min_lease) && ! preg_match("/^[-+]?[0-9]*\.?[0-9]+$/", $min_lease) ){
            echo json_encode([
                'status' => 0,
                'msg' => '最短租期只能输入数字或小数'
            ]);
            exit();
        }
        
        
        if (! empty($floor_start) && ! preg_match("/^[-+]?[0-9]*\.?[0-9]+$/", $floor_start)) {
            echo json_encode([
                'status' => 0,
                'msg' => '楼层第几层只能输入数字或小数'
            ]);
            exit();
        }
        
        if (! empty($floor_end) && ! preg_match("/^[-+]?[0-9]*\.?[0-9]+$/", $floor_end)) {
            echo json_encode([
                'status' => 0,
                'msg' => '楼层到几层只能输入数字或小数'
            ]);
            exit();
        }
        
        if (empty($phone)) {
            echo json_encode([
                'status' => 0,
                'msg' => '联系信息必填'
            ]);
            exit();
        }
        
        /**
         $sql = "select id from ant_store where location=? and street_sn=? and memo_address=? and id!=? ";
         $rows = DB::connection('slave')->select($sql, [
         $location,
         $street_sn,
         $memoAddress,
         $storeId
         ]);
         
         if (count($rows) > 0) {
         parent::writeJson([
         'status' => parent::STATUS_RECORD_EXISTS,
         'msg' => '店铺信息已存在',
         'id'=>$rows[0]->id
         ]);
         }
         */
        
        
        $store = DB::connection('ant')->table('store')->where('id',$storeId)->first();
        //押金 转让费 支付方式 剩余租约 最短租期  //只发收藏的店铺
        $updateJpush = false;
        switch ($rental_type){
            case 1:
                if($rental_value!=$store->rental_day){
                    $updateJpush = true;
                }
                break;
            case 2:
                if ($rental_value!=$store->rental_month){
                    $updateJpush = true;
                }
                break;
            case 3:
                if ($rental_value!=$store->rental_year){
                    $updateJpush = true;
                }
                break;
        }
        if ($transfer_fee!=$store->transfer_fee){
            $updateJpush = true;
        }
        if ($payment_deposit!=$store->payment_deposit){
            $updateJpush = true;
        }
        if ($payment_pay!=$store->payment_pay){
            $updateJpush = true;
        }
        if ($residual_lease!=$store->residual_lease){
            $updateJpush = true;
        }
        if ($min_lease!=$store->min_lease){
            $updateJpush = true;
        }
        
        DB::beginTransaction();
        try{
            $data['province'] = $province;
            $data['city'] = empty($city)?$province:$city;
            $data['district'] = $district;
            $data['street'] = $street;
            $data['location_lat'] = $location_lat;
            $data['location_lng'] = $location_lng;
            $data['street_sn']         = $street_sn;
            $data['memo_address']      = $memo_address;
            $data['present_operation'] = $present_operation;
            $data['trade']             = $trade;
            $data['status']            = $status;
            $data['property']          = $property;
            $data['basic_installation_ids'] = ! empty($basic_installation_ids) ? implode(',', $basic_installation_ids) : '';
            $data['extra_installation_ids'] = ! empty($extra_installation_ids) ? implode(',', $extra_installation_ids) : '';
            $data['door_width'] = $door_width;
            $data['build_area'] = $build_area;
            $data['use_area']   = $use_area;
            $data['floor_total'] = $floor_total;
            $data['floor_start'] = $floor_start;
            $data['floor_end']   = $floor_end;
            $data['conditional_remark'] = $conditional_remark;
            $data['rental_type'] = $rental_type;
            if (! empty($rental_type)) {
                switch ($rental_type) {
                    case 1: // 天 元
                        $data['rental_day'] = $rental_value;
                        $data['rental_month'] = getRentalMonthByDay($rental_value, $build_area);
                        $data['rental_year'] = getRentalYearByDay($rental_value, $build_area);
                        break;
                    case 2: // 月 万
                        $data['rental_day'] = getRentalDayByMonth($rental_value, $build_area);
                        $data['rental_month'] = $rental_value;
                        $data['rental_year'] = getRentalYearByMonth($rental_value);
                        break;
                    case 3: // 年 万
                        $data['rental_day'] = getRentalDayByYear($rental_value, $build_area);
                        $data['rental_month'] = getRentalMonthByYear($rental_value);
                        $data['rental_year'] = $rental_value;
                        break;
                }
            }
            
            $data['transfer_fee'] = $transfer_fee;
            $data['residual_lease'] = $residual_lease;
            $data['payment_deposit'] = intval($payment_deposit);
            $data['payment_pay']     = intval($payment_pay);
            $data['min_lease']       = intval($min_lease);
            $data['charge_remark']   = $charge_remark;
            $data['updated_at']      = $time;
            $data['update_operator_id'] = $operatorId;
            
            $tags = '';
            if (is_array($tag_ids)){
                $tags = implode(',', $tag_ids);
            }
            $data['tags'] = $tags;
            $data['source'] = $source;
            $data['oam_times'] = intval($oam_times);
            
            $oam_times = $data['oam_times']==0?30:$data['oam_times'];
            
            $data['oam_datetime'] = ($oam_times*3600*24) + $time;
            
            DB::connection('ant')->table('store')->where('id', $storeId)->update($data);
            
            // 更新thumb
            $store_thumbs = trim($store_thumbs, ',');
            DB::connection('ant')->table('store_thumb')->where([
                'store_id' => $storeId,
                'type' => 4
            ])->delete();
            if (! empty($store_thumbs)) {
                $thumb_other_arr = explode(',', $store_thumbs);
                $thumb_otherArr = array();
                for ($i = 0; $i < count($thumb_other_arr); $i ++) {
                    $thumb_otherArr[$i]['store_id'] = $storeId;
                    $thumb_otherArr[$i]['type'] = 4;
                    $thumb_otherArr[$i]['qiniu_key'] = $thumb_other_arr[$i];
                    $thumb_otherArr[$i]['created_at'] = $time;
                }
                DB::connection('ant')->table('store_thumb')->insert($thumb_otherArr);
            }
            
            // 更新联系方式
            DB::connection('ant')->table('store_call')->where('store_id', $storeId)->delete();
            if (! empty($phone)) {
                $arr_phone = $phone;
                $arr_wechat = $wechat;
                $arr_contacts = $contacts;
                $arr_number = $number;
                $callArr = array();
                for ($i = 0; $i < count($arr_phone); $i ++) {
                    $callArr[$i]['store_id'] = $storeId;
                    $callArr[$i]['phone'] = $arr_phone[$i];
                    $callArr[$i]['wechat'] = $arr_wechat[$i];
                    $callArr[$i]['role_name'] =  $this->_http->getPost('role_name_' . $arr_number[$i])[0];
                    $callArr[$i]['contacts'] = $arr_contacts[$i];
                    $callArr[$i]['created_at'] = $time;
                }
                DB::connection('ant')->table('store_call')->insert($callArr);
            }
            
            DB::commit();
            
            if ($updateJpush==true){
                $results = DB::select("select distinct(user_id),store_id from network_favorite where type=2 and store_id=$storeId");
                
                if (count($results) > 0) {
                    
                    $res = jpushSingleFavoriateStoreUpdated($results);
                }
            }
            
            echo json_encode([
                'status' => 1,
                'msg' => '操作店铺成功'
            ]);
            
        }catch (Exception $ex){
            
            DB::rollback();
            echo json_encode([
                'status' => 0,
                'msg' => '系统异常'.$ex->getMessage()."-".$ex->getLine()
            ]);
            
        }
        return false;
    }
    
    /**
     * uploadPic
     *
     * @author kevin
     * @access public
     * @param mixed $code
     * @return json
     */
    public function uploadPicAction()
    {
        $picname = $_FILES['mypic']['name'];
        $picsize = $_FILES['mypic']['size'];
        if ($picname != "") {
            if ($picsize > 1024000) {
                echo '图片大小不能超过1M';
                exit();
            }
            $postfix = [
                '.jpg',
                '.jpeg',
                '.png',
                '.gif',
                '.bmp'
            ];
            
            $type = strstr($picname, '.');
            if (! in_array(strtolower($type), $postfix)) {
                echo '图片格式不对！';
                exit();
            }
            
            $rand = rand(100, 999);
            $pics = date("YmdHis") . $rand . $type;
            // 上传路径
            $pic_path = getcwd() . "/uploads/" . $pics;
            move_uploaded_file($_FILES['mypic']['tmp_name'], $pic_path);
            
            $accessKey = '-T86qWAeWBs6Ra8EARyEJQ1ewLDK87oh4jOfM6yo';
            $secretKey = 'hOKpzOa5gNjQyI4Z0fS2_Ss6zSykquRfcmTPmAT8';
            $auth = new Auth($accessKey, $secretKey);
            $bucket = 'ant-store'; // 你的七牛空间名
            // 设置put policy的其他参数
            $opts = array(
                // 'callbackBody' => 'name=$(fname)&hash=$(etag)'
            );
            $token = $auth->uploadToken($bucket, null, 3600, $opts);
            $key = 'web' . time() . rand(111, 999);
            $uploadMgr = new UploadManager();
            list ($ret, $err) = $uploadMgr->putFile($token, $key, $pic_path);
            
            @unlink($pic_path);
            
            // dd($err);
            
            $original_url = Yaf\Registry::get('config')->qiniu->url . $key;
            $thumb_url = Yaf\Registry::get('config')->qiniu->url . $key . '?imageView2/1/w/225/h/225';
            $this->getView()->assign('thumb_url', $thumb_url);
            $this->getView()->assign('original_url', $original_url);
            $this->getView()->assign('key', $key);
            $thumbStr = $this->getView()->render('admin/store/thumb.phtml');
            
            echo json_encode(array(
                'key' => $key,
                'str' => $thumbStr,
                'type' => $type
            ));
        }
        
        return false;
    }
    
    /**
     * 获取店铺添加联系人模板
     *
     * @return html
     */
    public function getContactTmpAction()
    {
        $number = $this->_http->getPost('number');
        $this->getView()->assign('number', $number);
        $content = $this->getView()->display('admin/store/contact_tmp.phtml');
        echo $content;
        return false;
    }
    
    /**
     * addStoreTag
     *
     * @author kevin
     * @access public
     * @param mixed $code
     * @return json
     */
    public function addStoreTagAction() {
        
        $this->getView()->display('admin/store/insertTags.phtml');
        return false;
    }
    
    
}