<?php
require_once dirname(getcwd()) . '/vendor/autoload.php';

use Qiniu\Auth;
use Alipay\Alipay;
use Twig\Node\ImportNode;

class IndexController extends BaseController
{

    protected $_http;

    public function init()
    {
        $this->_http = new Yaf\Request\Http();
        parent::init();
    }

    /**
     * netStoreList
     * 全网铺源列表
     *
     * @author kevin
     * @access public
     * @param mixed $code            
     * @return json
     */
    public function netStoreListAction()
    {
        $keyword = nndealParam($this->_http->getQuery('keyword', '')); // 搜索关键字
        $page = intval($this->_http->getQuery('page', 1));
        $pagesize = intval($this->_http->getQuery('limit', 10));
        
        $province = nndealParam($this->_http->getQuery('province'));
        $city = nndealParam($this->_http->getQuery('city'));
        $district = nndealParam($this->_http->getQuery('district'));
        $area = $this->_http->getQuery('area');
        $rental = $this->_http->getQuery('rental');
        $transfer = $this->_http->getQuery('transfer');
        $updated_at = $this->_http->getQuery('updated_at');
        $source_website = $this->_http->getQuery('source_website');        
        
        $userId = intval($this->_http->getQuery('user_id'));
        
        if (empty($province) || empty($city)) {
            parent::writeJson([
                'status' => parent::STATUS_PARAMS_EXCEPTION,
                'msg' => '参数异常'
            ]);
        }
        
        //更新订阅信息
        if ($userId>0){
            
            $flag = updateUserSubscribe($userId,$district,$area,$rental,$transfer,$updated_at);
            
        }
        
        $condition = " 1=1 AND status=0 ";
        if (! empty($keyword)) {
            
            try {
                $sphinx = new SphinxClient();
                $sphinx->setServer("localhost", 9313);
                $sphinx->setMatchMode(SPH_MATCH_PHRASE); // SPH_MATCH_ALL
                $result = $sphinx->query($keyword, "*");
                 
                if (array_key_exists('matches', $result)) {
                    $ids = array_keys($result['matches']);
                    $storeSearchIds = implode(',', $ids);
                    $condition .= " AND id in($storeSearchIds) ";
                }else{
                    $condition.=" AND id in(0) ";
                }
            } catch (Exception $e) {}
            
             
        }
        
        if (! empty($district) && $district!="全区域") {
            $condition .= " AND province='" . $province . "' AND city='" . $city . "' AND district like '%" . $district . "%' ";
        }elseif (! empty($district) && $district=="全区域"){
            $condition .= " AND province='" . $province . "' AND city='" . $city . "' ";
        }
        
        
        if (! empty($area)) {
            switch ($area) {
                case 1: // 1-50平米
                    $condition .= "  AND ( build_area>1 and build_area<=50 )  ";
                    break;
                case 2: // 50-100平米
                    $condition .= " AND ( build_area>50 and build_area<=100 )  ";
                    break;
                case 3: // 100-200平米
                    $condition .= " AND ( build_area>100 and build_area<=200 ) ";
                    break;
                case 4: // 200-300平米
                    $condition .= " AND ( build_area>200 and build_area<=300 ) ";
                    break;
                case 5: // 300-500平米
                    $condition .= "  AND ( build_area>300 and build_area<=500 ) ";
                    break;
                case 6: // >500平米
                    $condition .= " AND build_area>500  ";
                    break;
            }
        }
        if (! empty($rental)) {
            switch ($rental) {
                case 1: // 1-10万
                    $condition .= " AND ( rental_year>1 and rental_year<=10 )  ";
                    break;
                case 2: // 10-20万
                    $condition .= " AND ( rental_year>10 and rental_year<=20 )  ";
                    break;
                case 3: // 20-40万
                    $condition .= " AND ( rental_year>20 and rental_year<=40 )  ";
                    break;
                case 4: // 40-60万
                    $condition .= " AND ( rental_year>40 and rental_year<=60 )  ";
                    break;
                case 5: // 60-100万
                    $condition .= "  AND  ( rental_year>60 and rental_year<=100 )  ";
                    break;
                case 6: // >100万
                    $condition .= " AND rental_year>100  ";
                    break;
            }
        }
        
        if ($transfer == 1) {
            $condition .= " AND ( transfer=0 or transfer='' )  ";
        }else{
            $condition .= " AND ( transfer>0 or transfer!='' )  ";
        }
        
        if (! empty($updated_at)) {
            switch ($updated_at) {
                case 1: // 3天内 DATE_FORMAT(NOW(),'%Y-%m-%d')
                    $condition .= " AND DATEDIFF(DATE_FORMAT(NOW(),'%Y-%m-%d'),updated_at)<=3  ";
                    break;
                case 2: // 7天内
                    $condition .= " AND DATEDIFF(DATE_FORMAT(NOW(),'%Y-%m-%d'),updated_at)<=7  ";
                    break;
                case 3: // 30天内
                    $condition .= " AND DATEDIFF(DATE_FORMAT(NOW(),'%Y-%m-%d'),updated_at)<=30  ";
                    break;
            }
        }
        
        if (!empty($source_website)){
            $source_website_str = trim($source_website,',');
            $condition.=" AND source_website in ($source_website_str) ";
        }
        
        $page = $page == 0 ? 1 : $page;
        $offset = ($page - 1) * $pagesize;
        $sql = "SELECT id,district,title,thumb,rental_year,rental,rental_type,build_area,address,username,identity,source_website,site_url,updated_at,status FROM `network_store` WHERE {$condition} ORDER BY UNIX_TIMESTAMP(updated_at) desc, id desc LIMIT $offset,$pagesize";
        $results = DB::connection('net')->select($sql);
        $data = array();
      
        $resultsPos = [];
        
        if (empty($keyword)) {
            
            $datetime = date('Y-m-d');
            $sql1 = "select * from network_store_position  where ";
            $sql1 .= " FROM_UNIXTIME(start_date,'%Y-%m-%d')<='{$datetime}' and FROM_UNIXTIME(end_date,'%Y-%m-%d')>='{$datetime}' and position>{$offset}";
            $resultsPos= DB::select($sql1);
            
        }
        
        foreach ($results as $rs) :
            
            $statusStr = '';
            switch ($rs->status) {
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
            
            if (count($resultsPos) > 0) {
                
                foreach ($resultsPos as $ps) :
                    
                if (($ps->position-1)== $offset) {
                        
                        $storeId = $ps->store_id;
                        
                        $row = DB::connection('ant')->table('store')
                            ->where('id', $storeId)
                            ->first();
                        
//                         $title = $row->present_operation;

                        $district = $row->district;
                        
                        // 状态
                        $statusStr = "";
                        switch ($row->status) {
                            case 0:
                                $statusStr = '转租中';
                                break;
                            case 1:
                                $statusStr = '营业中';
                                break;
                        }
                        
                        $thumb_door = '';
                        $rowThumb = DB::connection('ant')->table('store_thumb')
                            ->select('type', 'qiniu_key')
                            ->where([
                            'type' => 4,
                            'store_id' => $storeId
                        ])
                            ->orderBy('id', 'asc')
                            ->first();
                        if (! empty($rowThumb)) {
                            $thumb_door = Yaf\Registry::get('config')->qiniu->url . $rowThumb->qiniu_key . '?imageView2/1/w/225/h/225';
                        }
                        
                        $rental_year = $row->rental_year;
                        $updated_at = date('Y-m-d', $row->updated_at);
                        $build_area = $row->build_area;
                        $address = $row->trade . ' ' . $row->street_sn . "号";
                        $title = $row->district . $row->street . "·" . $row->build_area . "㎡" . $row->present_operation . $statusStr;
                        // 联系人信息
                        $contacts = DB::connection('ant')->table('store_call')
                            ->select('id', 'phone', 'wechat', 'role_name', 'contacts')
                            ->where('store_id', $storeId)
                            ->orderBy('id', 'asc')
                            ->first();
                        $username = '';
                        $identity = '';
                        if (! empty($contacts)) {
                            $username = $contacts->contacts;
                            $identity = $contacts->role_name;
                        }
                        
                        $data[] = array(
                            'type' => 2,
                            'store_id' => $storeId,
                            'district' => $district,
                            'title' => $title,
                            'thumb' => $thumb_door,
                            'rental_year' => $rental_year."万/年",
                            'updated_at' => $updated_at,
                            'build_area' => $build_area,
                            'address' => $address,
                            'username' => $username,
                            'identity' => $identity,
                            'source_website' => '',
                            'site_url' => '',
                            'status_str' => $statusStr
                        );
                    }
                endforeach
                ;
            }
            
            $data[] = array(
                'type' => 1,
                'store_id' => $rs->id,
                'district' => $rs->district,
                'title' => $rs->title,
                'thumb' => $rs->thumb,
                'rental_year' => $rs->rental.$rs->rental_type,
                'updated_at' => $rs->updated_at,
                'build_area' => $rs->build_area,
                'address' => $rs->address,
                'username' => $rs->username,
                'identity' => $rs->identity,
                'source_website' => getNetStoreSource($rs->source_website),
                'site_url' => $rs->site_url,
                'status_str' => $statusStr
            );
            $offset ++;
        endforeach
        ;
        
        // 魔方铺源
        $mData = [];
        if (! empty($keyword) && $page == 1) {
            $datetime = date('Y-m-d');
            $sql = "select store_id from network_store_position  where ";
            $sql .= " FROM_UNIXTIME(start_date,'%Y-%m-%d')<='{$datetime}' and FROM_UNIXTIME(end_date,'%Y-%m-%d')>='{$datetime}'";
            $results = DB::select($sql);
            
            $pos_ids = '';
            if (! empty($results)) {
                
                foreach ($results as $r) :
                    
                    $pos_ids .= $r->store_id . ",";
                endforeach
                ;
                $pos_ids = trim($pos_ids, ',');
                
                if (! empty($pos_ids)) {
                    
                    try {
                        $sphinx = new SphinxClient();
                        $sphinx->setServer("localhost", 9312);
                        $sphinx->setMatchMode(SPH_MATCH_PHRASE); // SPH_MATCH_ALL
                        $result = $sphinx->query($keyword, "*");
                        
                        if (array_key_exists('matches', $result)) {
                            $ant_ids = array_keys($result['matches']);
                            
                            if (count($ant_ids) > 0) {
                                $arr_pos_ids = explode(',', $pos_ids);
                                $new_arr = array_intersect($arr_pos_ids, $ant_ids);
                                
                                if (count($new_arr) > 0) {
                                    
                                    foreach ($new_arr as $storeId) :
                                       
                                        $row = DB::connection('ant')->table('store')
                                            ->where('id', $storeId)
                                            ->first();
                                        $title = $row->present_operation;
                                        $district = $row->district;
                                        
                                        // 状态
                                        $statusStr = "";
                                        switch ($row->status) {
                                            case 0:
                                                $statusStr = '转租中';
                                                break;
                                            case 1:
                                                $statusStr = '营业中';
                                                break;
                                        }
                                        
                                        $thumb_door = '';
                                        $rowThumb = DB::connection('ant')->table('store_thumb')
                                            ->select('type', 'qiniu_key')
                                            ->where([
                                            'type' => 4,
                                            'store_id' => $storeId
                                        ])
                                            ->orderBy('id', 'asc')
                                            ->first();
                                        if (! empty($rowThumb)) {
                                            $thumb_door = Yaf\Registry::get('config')->qiniu->url . $rowThumb->qiniu_key . '?imageView2/1/w/225/h/225';
                                        }
                                        
                                        $rental_year = $row->rental_year;
                                        $updated_at = date('Y-m-d', $row->updated_at);
                                        $build_area = $row->build_area;
                                        $address = $row->trade . ' ' . $row->street_sn . "号";
                                        
                                        // 联系人信息
                                        $contacts = DB::connection('ant')->table('store_call')
                                            ->select('id', 'phone', 'wechat', 'role_name', 'contacts')
                                            ->where('store_id', $storeId)
                                            ->orderBy('id', 'asc')
                                            ->first();
                                        $username = '';
                                        $identity = '';
                                        if (! empty($contacts)) {
                                            $username = $contacts->contacts;
                                            $identity = $contacts->role_name;
                                        }
                                        
                                        $mData[] = array(
                                            'type' => 2,
                                            'store_id' => $storeId,
                                            'district' => $district,
                                            'title' => $title,
                                            'thumb' => $thumb_door,
                                            'rental_year' => $rental_year,
                                            'updated_at' => $updated_at,
                                            'build_area' => $build_area,
                                            'address' => $address,
                                            'username' => $username,
                                            'identity' => $identity,
                                            'source_website' => '',
                                            'site_url' => '',
                                            'status_str' => $statusStr
                                        );
                                    endforeach
                                    ;
                                }
                            }
                        }
                    } catch (Exception $e) {}
                }
            }
        }
        
        
        
        if (count($mData) > 0) {
            $data = array_merge($mData, $data);
        }
        
        if ($userId>0){
            $subData = [];
            foreach ($data as $ss):
                
                $subData[] = [
                    'user_id'=>$userId,
                    'store_id'=>$ss['store_id'],
                    'type'=>$ss['type'],
                    'created_at'=>time()
                ];
            endforeach;
            if (count($subData)>0){
                DB::table('subscribe_store')->where('user_id',$userId)->delete();
                DB::table('subscribe_store')->insert($subData);
            }
        }
        
        parent::writeJson([
            'status' => parent::STATUS_OK,
            'data' => $data
        ]);
        return false;
    }

    /**
     * storePosition
     * 魔方铺源（蚂蚁）显示在全网铺源列表位置
     *
     * @author kevin
     * @access public
     * @param mixed $code            
     * @return json
     */
    public function storePositionAction()
    {
        $datetime = date('Y-m-d');
        $sql = "select * from network_store_position  where ";
        $sql .= " FROM_UNIXTIME(start_date,'%Y-%m-%d')>={$datetime} and FROM_UNIXTIME(start_date,'%Y-%m-%d')<={$datetime}";
        $results = DB::table('store_position')->select($sql);
        
        $data = [];
        foreach ($results as $store) :
            $storeId = $store->store_id;
            $row = DB::connection('ant')->table('store')
                ->where('id', $storeId)
                ->first();
            $title = $row->present_operation;
            $district = $row->district;
            
            // 状态
            $statusStr = "";
            switch ($row->status) {
                case 0:
                    $statusStr = '转租中';
                    break;
                case 1:
                    $statusStr = '营业中';
                    break;
            }
            
            $thumb_door = '';
            $rowThumb = DB::connection('ant')->table('store_thumb')
                ->select('type', 'qiniu_key')
                ->where([
                'type' => 4,
                'store_id' => $storeId
            ])
                ->orderBy('id', 'asc')
                ->first();
            if (! empty($rowThumb)) {
                $thumb_door = Yaf\Registry::get('config')->qiniu->url . $rowThumb->qiniu_key . '?imageView2/1/w/225/h/225';
            }
            
            $rental_year = $row->rental_year;
            $updated_at = date('Y-m-d', $row->updated_at);
            $build_area = $row->build_area;
            $address = $row->trade . ' ' . $row->street_sn . "号";
            
            // 联系人信息
            $contacts = DB::connection('ant')->table('store_call')
                ->select('id', 'phone', 'wechat', 'role_name', 'contacts')
                ->where('store_id', $storeId)
                ->orderBy('id', 'asc')
                ->first();
            $username = '';
            $identity = '';
            if (! empty($contacts)) {
                $username = $contacts->contacts;
                $identity = $contacts->role_name;
            }
            
            $data[] = array(
                'store_id' => $store->store_id,
                'position' => $store->position,
                'status_str' => $statusStr,
                'title' => $title,
                'district' => $district,
                'thumb' => $thumb_door,
                'rental_year' => $rental_year,
                'updated_at' => $updated_at,
                'build_area' => $build_area,
                'address' => $address,
                'username' => $username,
                'identity' => $identity
            );
        endforeach
        ;
        
        parent::writeJson([
            'status' => parent::STATUS_OK,
            'data' => $data
        ]);
        return false;
    }

    /**
     * storeDetails
     * 全网店铺详情页面
     *
     * @author kevin
     * @access public
     * @param mixed $code            
     * @return json
     */
    public function storeDetailsAction()
    {
        $storeId = intval($this->_http->getQuery('store_id'));
        $userId = intval($this->_http->getQuery('user_id'));
        
        if (intval($storeId) <= 0) {
            parent::writeJson([
                'status' => parent::STATUS_PARAMS_EXCEPTION,
                'msg' => '参数异常'
            ]);
        }
        
        $row = DB::connection('net')->table('store')
            ->where('id', $storeId)
            ->first();
        
        if (empty($row)) {
            parent::writeJson([
                'status' => parent::STATUS_RECORD_NOT_FOUND,
                'msg' => '店铺信息不存在'
            ]);
        }
        
        // if ($row->is_valid==0){
        // parent::writeJson([
        // 'status' => parent::STATUS_STORE_NOT_VALID,
        // 'msg' => '店铺信息无效'
        // ]);
        // }
        
        $transfer = empty($row->transfer)?'无':$row->transfer;
        if (floatval($transfer)==0){
            $transfer = "无";
        }
        
        $data = array();
        $data['store_id'] = $row->id;
        $data['thumb'] = $row->thumb;
        $data['title'] = $row->title;
        $data['source_website'] = getNetStoreSource($row->source_website);
        $data['updated_at'] = $row->updated_at;
        $data['build_area'] = $row->build_area;
        $data['rental_day'] = $row->rental_day;
        $data['rental_month'] = $row->rental_month;
        $data['rental_year'] = $row->rental.$row->rental_type;
        $data['transfer'] = $transfer;
        $data['address'] = $row->address;
        $data['site_url'] = $row->site_url;
        $data['type'] = 1;
        $data['status'] = $row->status;
        
        $is_favorite = 0;
        $opt_logs = [];
        
        if ($userId > 0) {
            
            $existsRes = DB::select("select id from network_visits where FROM_UNIXTIME(created_at,'%Y-%m-%d')=? and type=1 and user_id={$userId} and store_id={$storeId}", [
                date('Y-m-d')
            ]);
            
            if (count($existsRes) <= 0) {
                DB::table('visits')->insert([
                    'user_id' => $userId,
                    'store_id' => $storeId,
                    'type' => 1,
                    'created_at' => time()
                ]);
            }
            
            // 收藏
            $fs = DB::table('favorite')->where([
                'user_id' => $userId,
                'store_id' => $storeId,
                'type' => 1
            ])->count();
            if ($fs > 0) {
                $is_favorite = 1;
            }
            
            // 操作记录
            $ops = DB::select("select * from network_operate_log where type=1 and user_id=? and store_id=? order by id desc", [
                $userId,
                $storeId
            ]);
            if (! empty($ops)) {
                foreach ($ops as $ps) :
                    
                    $note_msg = '';
                    if (intval($ps->note_id) > 0) {
                        $msg = DB::table('message')->where([
                            'type' => 1,
                            'user_id' => $userId,
                            'store_id' => $storeId
                        ])->first();
                        if (! empty($msg)) {
                            $note_msg = $msg->message;
                        }
                    }
                    
                    $opt_logs[] = array(
                        'opt_time' => date('Y-m-d H:i:s', $ps->created_at),
                        'msg' => $ps->message,
                        'note_id' => $ps->note_id,
                        'note_msg' => $note_msg,
                        'stype' => $ps->stype
                    );
                endforeach
                ;
            }
        }
        $data['is_favorite'] = $is_favorite;
        $data['operates'] = $opt_logs;
        $data['username'] = $row->username;
        $data['phone'] = $row->phone;
        
        parent::writeJson([
            'status' => parent::STATUS_OK,
            'data' => $data
        ]);
        return false;
    }

    /**
     * mStoreDetail
     * 魔方店铺详情
     *
     * @author kevin
     * @access public
     * @param mixed $code            
     * @return json
     */
    public function mStoreDetailAction()
    {
        $storeId = intval($this->_http->getQuery('store_id'));
        $userId = intval($this->_http->getQuery('user_id'));
        
        if (intval($storeId) <= 0) {
            parent::writeJson([
                'status' => parent::STATUS_PARAMS_EXCEPTION,
                'msg' => '参数异常'
            ]);
        }
        
        $row = DB::connection('ant')->table('store')
            ->where('id', $storeId)
            ->first();
        
        if (empty($row)) {
            parent::writeJson([
                'status' => parent::STATUS_RECORD_NOT_FOUND,
                'msg' => '店铺信息不存在'
            ]);
        }
        
        // if ($row->is_valid==0){
        // parent::writeJson([
        // 'status' => parent::STATUS_STORE_NOT_VALID,
        // 'msg' => '店铺信息无效'
        // ]);
        // }
        
        // 状态
        $statusStr = "";
        switch ($row->status) {
            case 0:
                $statusStr = '转租中';
                break;
            case 1:
                $statusStr = '营业中';
                break;
        }
        
        $data = array();
        $data['store_id'] = $storeId;
        $data['title'] = $row->district . $row->street . "·" . $row->build_area . "㎡" . $row->present_operation . $statusStr;
        $data['province'] = $row->province;
        $data['city'] = $row->city;
        $data['district'] = $row->district;
        $data['street'] = $row->street;
        $data['street_sn'] = $row->street_sn;
        $data['memo_address'] = $row->memo_address;
        $data['present_operation'] = $row->present_operation;
        $data['trade'] = $row->trade;
        $data['status'] = $row->status;
        $data['status_str'] = $statusStr;
        $data['property'] = $row->property;
        $data['store_sn'] = $row->store_sn;
        
        // 基础配套
        $basic_installation_ids = $row->basic_installation_ids;
        $basic_installations = array();
        if (! empty($basic_installation_ids)) {
            $basic_installation_arr = explode(',', $basic_installation_ids);
            foreach ($basic_installation_arr as $installation_id) :
                $basic_installations[] = array(
                    'installation_id' => $installation_id,
                    'name' => getInstallationNameById($installation_id)
                );
            endforeach
            ;
        }
        $data['basic_installation'] = $basic_installations;
        // 额外配套
        $extra_installation_ids = $row->extra_installation_ids;
        $extra_installations = array();
        if (! empty($extra_installation_ids)) {
            $extra_installation_arr = explode(',', $extra_installation_ids);
            foreach ($extra_installation_arr as $installation_id) :
                $extra_installations[] = array(
                    'installation_id' => $installation_id,
                    'name' => getInstallationNameById($installation_id)
                );
            endforeach
            ;
        }
        
        $transfer = empty($row->transfer_fee)?'无':$row->transfer_fee;
        if (floatval($transfer)==0){
            $transfer = '无';
        }
        $data['extra_installation'] = $extra_installations;
        
        $data['door_width'] = $row->door_width;
        $data['build_area'] = $row->build_area;
        $data['use_area'] = $row->use_area;
        $data['floor_total'] = $row->floor_total;
        $data['floor_start'] = $row->floor_start;
        $data['floor_end'] = $row->floor_end;
        $data['conditional_remark'] = $row->conditional_remark;
        $data['rental_day'] = $row->rental_day."元/平方米/天";
        $data['rental_month'] = $row->rental_month."万/月";
        $data['rental_year'] = $row->rental_year."万/年";
        $data['rental_type'] = $row->rental_type;
        $data['transfer_fee'] = $transfer;
        $data['residual_lease'] = $row->residual_lease;
        $data['payment_deposit'] = $row->payment_deposit;
        $data['payment_pay'] = $row->payment_pay;
        $data['min_lease'] = $row->min_lease;
        $data['charge_remark'] = $row->charge_remark;
        $data['location_lat'] = $row->location_lat;
        $data['location_lng'] = $row->location_lng;
        $data['type'] = 2;
        
        $tags_str = [];
        if (! empty(trim($row->tags, ','))) {
            $arr_tags = explode(',', $row->tags);
            
            $i = 1;
            foreach ($arr_tags as $tag) :
                if ($i <= 3) {
                    array_push($tags_str, "{$tag}");
                }
                $i ++;
            endforeach
            ;
        }
        
        $data['tags'] = $tags_str;
        
        // 联系人信息
        $contacts = DB::connection('ant')->table('store_call')
            ->select('id', 'phone', 'wechat', 'role_name', 'contacts')
            ->where('store_id', $storeId)
            ->orderBy('id', 'desc')
            ->get();
        $contact_arr = array();
        foreach ($contacts as $cs) :
            $contact_arr[] = array(
                'contacts_id' => $cs->id,
                'phone' => $cs->phone,
                'wechat' => $cs->wechat,
                'role_name' => $cs->role_name,
                'contacts' => $cs->contacts
            );
        endforeach
        ;
        $data['contacts'] = $contact_arr;
        
        // 图片信息
        $thumbs = DB::connection('ant')->table('store_thumb')
            ->select('id', 'type', 'qiniu_key')
            ->where('store_id', $storeId)
            ->get();
        $thumb_arr = array();
        foreach ($thumbs as $thumb) {
            
            $thumb_arr[] = array(
                'thumb_id' => $thumb->id,
                'type' => $thumb->type,
                // 'qiniu_key' => Yaf\Registry::get('config')->qiniu->url . $thumb->qiniu_key . '?imageView2/1/w/750/h/530',
                'qiniu_key' => Yaf\Registry::get('config')->qiniu->url . $thumb->qiniu_key,
                'qiniu_uploaded_key' => $thumb->qiniu_key
            );
        }
        $data['thumbs'] = $thumb_arr;
        $data['thumb'] = count($thumb_arr)>0?$thumb_arr[0]:'';
        $data['source'] = $row->source;
        $data['is_valid'] = $row->is_valid;
        $is_favorite = 0;
        if ($userId > 0) {
            $existsRes = DB::select("select id from network_visits where FROM_UNIXTIME(created_at,'%Y-%m-%d')=? and type=2 and user_id={$userId} and store_id={$storeId}", [
                date('Y-m-d')
            ]);
            
            if (count($existsRes) <= 0) {
                DB::table('visits')->insert([
                    'user_id' => $userId,
                    'store_id' => $storeId,
                    'type' => 2,
                    'created_at' => time()
                ]);
            }
            
            // 收藏
            $fs = DB::table('favorite')->where([
                'user_id' => $userId,
                'store_id' => $storeId,
                'type' => 2
            ])->count();
            if ($fs > 0) {
                $is_favorite = 1;
            }
        }
        $data['is_favorite'] = $is_favorite;
        
        // 附近 小区、写字楼
        $districtInfo = [];
        $officeBuilding = [];
        $squares = returnSquarePoint($row->location_lng, $row->location_lat);
        
        $sql = "select * from `network_district_info` where province=? and city=? and district=? and location_lng<>0 and location_lat>{$squares['right-bottom']['lat']} and location_lat<{$squares['left-top']['lat']} and location_lng>{$squares['left-top']['lng']} and location_lng<{$squares['right-bottom']['lng']} ";
        $results = DB::connection('net')->select($sql, [
            $row->province,
            $row->city,
            $row->district
        ]);
        foreach ($results as $rs) :
            $districtInfo[] = [
                'name' => $rs->name,
                'build_total' => $rs->build_total,
                'house_total' => $rs->house_total,
                'build_year' => $rs->build_year,
                'location_lat' => $rs->location_lat,
                'location_lng' => $rs->location_lng
            ];
        endforeach
        ;
        $data['district_info'] = $districtInfo;
        
        $sql = "select * from `network_office_building` where province=? and city=? and district=? and location_lng<>0 and location_lat>{$squares['right-bottom']['lat']} and location_lat<{$squares['left-top']['lat']} and location_lng>{$squares['left-top']['lng']} and location_lng<{$squares['right-bottom']['lng']} ";
        $results = DB::connection('net')->select($sql, [
            $row->province,
            $row->city,
            $row->district
        ]);
        foreach ($results as $rs) :
            
            $officeBuilding[] = [
                'location_lat' => $rs->location_lat,
                'location_lng' => $rs->location_lng,
                'name' => $rs->name
            ];
        endforeach
        ;
        //$data['office_building'] = $officeBuilding;
        $data['updated_at'] = date('Y-m-d H:i',$row->updated_at);
        
        //附近 公交站、学校、写字楼
        $bus_station_count = 0;  //公交站名称、线路数量
        $bus_stations = [];
        $school_count = 0;   //学校名称
        $schools = [];
        $schools = getNearlySchool($row->location_lng, $row->location_lat);
        $school_count = count($schools);
        $bus_stations = getNearlyBus($row->location_lng, $row->location_lat);
        $bus_station_count = count($bus_stations);
        
        $offices_count = 0;
        $offices = [];
        $offices = getNearlyOffices($row->location_lng, $row->location_lat);
        $offices_count = count($offices);
        
        $data['bus_stations'] = $bus_stations;
        $data['schools'] = $schools;
        $data['offices'] = $offices;
        
        parent::writeJson([
            'status' => parent::STATUS_OK,
            'data' => $data
        ]);
        return false;
    }

    /**
     * doOperate
     * 保存收藏、取消收藏、拨打电话、记录笔记信息
     *
     * @author kevin
     * @access public
     * @param int $type
     *            店铺类型 1全网铺源 2魔方铺源
     * @param int $store_id
     *            店铺ID
     * @param int $opt_type
     *            操作类型 1跳转原网站 2收藏店铺 3取消收藏店铺 4记录笔记 5拨打电话
     * @param varchar $message
     *            笔记内容
     * @param int $user_id
     *            用户ID
     * @return json
     */
    public function doOperateAction()
    {
        $type = intval($this->_http->getQuery('type'));
        $storeId = intval($this->_http->getQuery('store_id'));
        $optType = intval($this->_http->getQuery('opt_type'));
        $message = nndealParam($this->_http->getQuery('message'));
        $userId = intval($this->_http->getQuery('user_id'));
        
        if ($type <= 0 || $storeId <= 0 || $optType <= 0 || $userId <= 0) {
            parent::writeJson([
                'status' => parent::STATUS_PARAMS_EXCEPTION,
                'msg' => '参数异常'
            ]);
        }
        
        $res = false;
        switch ($optType) {
            case 1: // 跳转原网站
                $res = DB::table('operate_log')->insert([
                    'type' => $type,
                    'user_id' => $userId,
                    'store_id' => $storeId,
                    'stype' => $optType,
                    'message' => '跳转原网站',
                    'created_at' => time()
                ]);
                
                if ($res) {
                    parent::writeJson([
                        'status' => parent::STATUS_OK,
                        'msg' => '操作成功'
                    ]);
                } else {
                    parent::writeJson([
                        'status' => parent::STATUS_DB_EXCEPTION,
                        'msg' => '系统异常' . $ex->getMessage() . "-" . $ex->getLine() . "-" . $ex->getTraceAsString()
                    ]);
                }
                return false;
                break;
            case 5: // 拨打电话
                $res = DB::table('operate_log')->insert([
                    'type' => $type,
                    'user_id' => $userId,
                    'store_id' => $storeId,
                    'stype' => $optType,
                    'message' => '拨打电话',
                    'created_at' => time()
                ]);
                
                if ($res) {
                    parent::writeJson([
                        'status' => parent::STATUS_OK,
                        'msg' => '操作成功'
                    ]);
                } else {
                    parent::writeJson([
                        'status' => parent::STATUS_DB_EXCEPTION,
                        'msg' => '系统异常' . $ex->getMessage() . "-" . $ex->getLine() . "-" . $ex->getTraceAsString()
                    ]);
                }
                return false;
                break;
            case 2:
                
                DB::beginTransaction();
                try {
                    
                    $count = DB::table('favorite')->where([
                        'type' => $type,
                        'store_id' => $storeId,
                        'user_id' => $userId
                    ])->count();
                    if ($count <= 0) {
                        DB::table('favorite')->insert([
                            'user_id' => $userId,
                            'store_id' => $storeId,
                            'type' => $type,
                            'created_at' => time()
                        ]);
                    }
                    
                    DB::table('operate_log')->insert([
                        'type' => $type,
                        'user_id' => $userId,
                        'store_id' => $storeId,
                        'stype' => $optType,
                        'message' => '收藏店铺',
                        'created_at' => time()
                    ]);
                    
                    DB::commit();
                    parent::writeJson([
                        'status' => parent::STATUS_OK,
                        'msg' => '操作成功'
                    ]);
                } catch (Exception $ex) {
                    DB::rollback();
                    parent::writeJson([
                        'status' => parent::STATUS_DB_EXCEPTION,
                        'msg' => '系统异常' . $ex->getMessage() . "-" . $ex->getLine() . "-" . $ex->getTraceAsString()
                    ]);
                }
                
                break;
            case 3:
                
                DB::beginTransaction();
                try {
                    
                    $count = DB::table('favorite')->where([
                        'type' => $type,
                        'store_id' => $storeId,
                        'user_id' => $userId
                    ])->delete();
                    
                    DB::table('operate_log')->insert([
                        'type' => $type,
                        'user_id' => $userId,
                        'store_id' => $storeId,
                        'stype' => $optType,
                        'message' => '取消收藏店铺',
                        'created_at' => time()
                    ]);
                    
                    DB::commit();
                    parent::writeJson([
                        'status' => parent::STATUS_OK,
                        'msg' => '操作成功'
                    ]);
                } catch (Exception $ex) {
                    DB::rollback();
                    parent::writeJson([
                        'status' => parent::STATUS_DB_EXCEPTION,
                        'msg' => '系统异常' . $ex->getMessage() . "-" . $ex->getLine() . "-" . $ex->getTraceAsString()
                    ]);
                }
                break;
            case 4: // 记录笔记
                
                DB::beginTransaction();
                try {
                    $title = '';
                    if ($type == 1) {
                        $store = DB::connection('net')->table('store')
                            ->where('id', $storeId)
                            ->first();
                        $title = "{$store->district}·{$store->title}";
                    }elseif ($type==2){
                        $store = DB::connection('ant')->table('store')
                        ->where('id', $storeId)
                        ->first();
                        //$title = "{$store->district}·{$store->present_operation}";
                        
                        // 状态
                        $statusStr = "";
                        switch ($store->status) {
                            case 0:
                                $statusStr = '转租中';
                                break;
                            case 1:
                                $statusStr = '营业中';
                                break;
                        }
                        
                        $title = $store->district . $store->street . "·" . $store->build_area . "㎡" . $store->present_operation . $statusStr;
                    }
                    $noteId = DB::table('note')->insertGetId([
                        'type' => $type,
                        'store_id' => $storeId,
                        'user_id' => $userId,
                        'title' => $title,
                        'message' => $message,
                        'created_at' => time()
                    ]);
                    
                    DB::table('operate_log')->insert([
                        'type' => $type,
                        'user_id' => $userId,
                        'store_id' => $storeId,
                        'stype' => $optType,
                        'message' => '记录笔记',
                        'created_at' => time(),
                        'note_id' => $noteId
                    ]);
                    
                    DB::commit();
                    parent::writeJson([
                        'status' => parent::STATUS_OK,
                        'msg' => '操作成功'
                    ]);
                } catch (Exception $ex) {
                    DB::rollback();
                    parent::writeJson([
                        'status' => parent::STATUS_DB_EXCEPTION,
                        'msg' => '系统异常' . $ex->getMessage() . "-" . $ex->getLine() . "-" . $ex->getTraceAsString()
                    ]);
                }
                
                break;
        }
        return false;
    }

    /**
     * msgList
     * 消息中心列表
     *
     * @author kevin
     * @access public
     * @param mixed $code            
     * @return json
     */
    public function msgListAction()
    {
        $userId = intval($this->_http->getQuery('user_id'));
        $page = intval($this->_http->getQuery('page', 1));
        $pagesize = intval($this->_http->getQuery('limit', 10));
        
        if ($userId <= 0) {
            parent::writeJson([
                'status' => parent::STATUS_PARAMS_EXCEPTION,
                'msg' => '参数异常'
            ]);
        }
        
        $page = $page == 0 ? 1 : $page;
        $offset = ($page - 1) * $pagesize;
        $sql = "SELECT * FROM `network_message` WHERE user_id={$userId} ORDER BY created_at desc, id desc LIMIT $offset,$pagesize";
        $results = DB::select($sql);
        $data = array();
        
        foreach ($results as $row) :
            
            $data[] = [
                'message_id' => $row->id,
                'title' => $row->title,
                'message' => $row->message,
                'created_at' => date('Y.m.d', $row->created_at),
                'type'=>$row->type,
                'stype'=>$row->stype,
                'store_id'=>$row->store_id
            ];
        endforeach;
        
        parent::writeJson([
            'status' => parent::STATUS_OK,
            'data' => $data
        ]);
        return false;
    }

    /**
     * favoriteList
     * 我的收藏列表
     *
     * @author kevin
     * @access public
     * @param mixed $code            
     * @return json
     */
    public function favoriteListAction()
    {
        $userId = intval($this->_http->getQuery('user_id'));
        $page = intval($this->_http->getQuery('page', 1));
        $pagesize = intval($this->_http->getQuery('limit', 10));
        
        if ($userId <= 0) {
            parent::writeJson([
                'status' => parent::STATUS_PARAMS_EXCEPTION,
                'msg' => '参数异常'
            ]);
        }
        
        $page = $page == 0 ? 1 : $page;
        $offset = ($page - 1) * $pagesize;
        $sql = "SELECT * FROM `network_favorite` WHERE user_id={$userId} ORDER BY created_at desc, id desc LIMIT $offset,$pagesize";
        $results = DB::select($sql);
        $data = array();
        
        foreach ($results as $row) :
            
            $type = $row->type; // 店铺类型 1全网铺源 2魔方铺源
                                // 状态
            $statusStr = "";
            $storeId = $row->store_id;
            $updated_at = '';
            $thumb = '';
            $rental_year = '';
            $title = '';
            $trade = '';
            $tags = [];
            $site_url = '';
            $status = 0;
            
            if ($type == 1) {
                $store = DB::connection('net')->table('store')
                    ->where('id', $storeId)
                    ->first();
                
                switch ($store->status) {
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
                $status = $store->status;                    
                    
                if (!empty($store)){                    
                $title = $store->district."·".$store->title;
                $thumb = $store->thumb;
                $updated_at = $store->updated_at;
                $rental_year = $store->rental.$store->rental_type;
                $site_url = $store->site_url;
                }
            }
            
            if ($type == 2) {
                $store = DB::connection('ant')->table('store')
                    ->where('id', $storeId)
                    ->first();
                switch ($store->status) {
                    case 0:
                        $statusStr = '转租中';
                        break;
                    case 1:
                        $statusStr = '营业中';
                        break;
                }
                $status = $store->status;
                
                $thumb_door = '';
                $rowThumb = DB::connection('ant')->table('store_thumb')
                    ->select('type', 'qiniu_key')
                    ->where([
                    'type' => 4,
                    'store_id' => $storeId
                ])
                    ->orderBy('id', 'asc')
                    ->first();
                if (! empty($rowThumb)) {
                    $thumb = Yaf\Registry::get('config')->qiniu->url . $rowThumb->qiniu_key . '?imageView2/1/w/225/h/225';
                }
                $rental_year = $store->rental_year;
                $updated_at = date('Y.m.d', $store->updated_at);
                $trade = $store->trade;
                //$title = "{$store->district}·{$store->present_operation}";
                
                $title = $store->district . $store->street . "·" . $store->build_area . "㎡" . $store ->present_operation . $statusStr;
                
                $tags_str = [];
                if (! empty(trim($store->tags, ','))) {
                    $arr_tags = explode(',', $store->tags);
                    
                    $i = 1;
                    foreach ($arr_tags as $tag) :
                        if ($i <= 3) {
                            array_push($tags_str, "{$tag}");
                        } else {
                            break;
                        }
                        $i ++;
                    endforeach
                    ;
                    $tags = $tags_str;
                }
            }
            
            $data[] = [
                'store_id' => $storeId,
                'type' => $type,
                'status_str' => $statusStr,
                'status' => $status,
                'updated_at' => $updated_at,
                'thumb' => $thumb,
                'rental_year' => $rental_year,
                'title' => $title,
                'trade' => $trade,
                'tags' => $tags,
                'site_url' => $site_url
            ];
        endforeach
        ;
        parent::writeJson([
            'status' => parent::STATUS_OK,
            'data' => $data
        ]);
        return false;
    }

    /**
     * noteList
     * 记事本列表
     *
     * @author kevin
     * @access public
     * @param mixed $code            
     * @return json
     */
    public function noteListAction()
    {
        $userId = intval($this->_http->getQuery('user_id'));
        $page = intval($this->_http->getQuery('page', 1));
        $pagesize = intval($this->_http->getQuery('limit', 10));
        
        if ($userId <= 0) {
            parent::writeJson([
                'status' => parent::STATUS_PARAMS_EXCEPTION,
                'msg' => '参数异常'
            ]);
        }
        
        $page = $page == 0 ? 1 : $page;
        $offset = ($page - 1) * $pagesize;
        $sql = "SELECT * FROM `network_note` WHERE user_id={$userId} ORDER BY created_at desc, id desc LIMIT $offset,$pagesize";
        $results = DB::select($sql);
        $data = array();
        
        foreach ($results as $row) :
            
            $type = $row->type;
            $noteId = $row->id;
            $created_at = date('Y-m-d H:i:s', $row->created_at);
            $title = $row->title;
            $message = $row->message;
            
            $data[] = [
                'note_id' => $noteId,
                'store_id' => $row->store_id,
                'type' => $type,
                'created_at' => $created_at,
                'title' => $title,
                'message' => $message
            ];
        endforeach
        ;
        
        parent::writeJson([
            'status' => parent::STATUS_OK,
            'data' => $data
        ]);
        return false;
    }

    /**
     * noteDetail
     * 记事本详情
     *
     * @author kevin
     * @access public
     * @param mixed $code            
     * @return json
     */
    public function noteDetailAction()
    {
        $userId = intval($this->_http->getQuery('user_id'));
        $noteId = intval($this->_http->getQuery('note_id'));
        
        if ($userId <= 0 || $noteId <= 0) {
            parent::writeJson([
                'status' => parent::STATUS_PARAMS_EXCEPTION,
                'msg' => '参数异常'
            ]);
        }
        
        $note = DB::table('note')->where('id', $noteId)->first();
        $data = array();
        if (empty($note)) {
            parent::writeJson([
                'status' => parent::STATUS_RECORD_NOT_FOUND,
                'msg' => '信息不存在'
            ]);
        }
        
        $data['title'] = $note->title;
        $data['message'] = $note->message;
        
        parent::writeJson([
            'status' => parent::STATUS_OK,
            'data' => $data
        ]);
        return false;
    }

    /**
     * feedback
     * 意见反馈
     *
     * @author kevin
     * @access public
     * @param mixed $code            
     * @return json
     */
    public function feedbackAction()
    {
        $user_id = intval($this->_http->getQuery('user_id'));
        $message = nndealParam($this->_http->getQuery('message'));
        
        if (empty($message)) {
            parent::writeJson([
                'status' => parent::STATUS_PARAMS_EXCEPTION,
                'msg' => '参数异常'
            ]);
        }
        
        $res = DB::table('feedback')->insert([
            'user_id' => $user_id,
            'message' => $message,
            'created_at' => time()
        ]);
        
        if ($res) {
            parent::writeJson([
                'status' => parent::STATUS_OK,
                'msg' => '操作成功'
            ]);
        } else {
            parent::writeJson([
                'status' => parent::STATUS_DB_EXCEPTION,
                'msg' => '系统错误'
            ]);
        }
        return false;
    }

    /**
     * visitDetail
     * 注册用户访问店铺详情统计
     *
     * @author kevin
     * @access public
     * @param mixed $code            
     * @return json
     */
    public function visitDetailAction()
    {
        $userId = intval($this->_http->getQuery('user_id'));
        $type = intval($this->_http->getQuery('type')); // 1全网铺源 2魔方铺源
        $storeId = intval($this->_http->getQuery('store_id'));
        
        if ($userId <= 0 || $type <= 0 || $storeId <= 0) {
            parent::writeJson([
                'status' => parent::STATUS_PARAMS_EXCEPTION,
                'msg' => '参数异常'
            ]);
        }
        
        $existsRes = DB::select("select id from network_visits where FROM_UNIXTIME(created_at,'%Y-%m-%d')=? and type={$type} and user_id={$userId} and store_id={$storeId}", [
            date('Y-m-d')
        ]);
        
        if (count($existsRes) <= 0) {
            DB::table('visits')->insert([
                'user_id' => $userId,
                'store_id' => $storeId,
                'type' => $type,
                'created_at' => time()
            ]);
        }
        parent::writeJson([
            'status' => parent::STATUS_OK,
            'msg' => '操作成功'
        ]);
        return false;
    }

    /**
     * doBrowers
     * 保存登陆用户浏览记录
     *
     * @author kevin
     * @access public
     * @param mixed $code            
     * @return json
     */
    public function doBrowersAction()
    {
        $userId = intval($this->_http->getQuery('user_id'));
        $browerIds = $this->_http->getQuery('ids');
        
        if ($userId <= 0 || empty($browerIds)) {
            parent::writeJson([
                'status' => parent::STATUS_PARAMS_EXCEPTION,
                'msg' => '参数异常'
            ]);
        }
        
        $insertData = [];
        $updateData = [];
        $arr = explode(',', $browerIds);
        foreach ($arr as $info) :
            $stores = explode('_', $info);
            $storeId = $stores[0];
            $type = $stores[1];
            $datetime = $stores[2];
            
            $existsRes = DB::select("select id from network_browers where type={$type} and user_id={$userId} and store_id={$storeId}");
            if (count($existsRes) <= 0) {
                $insertData[] = array(
                    'user_id' => $userId,
                    'store_id' => $storeId,
                    'type' => $type,
                    'created_at' => strtotime($datetime)
                );
            }else{
                DB::table('browers')->where([
                    'type'=>$type,
                    'user_id'=>$userId,
                    'store_id'=>$storeId
                ])->update(['created_at'=>strtotime($datetime)]);                
            }
            
        endforeach
        ;
        $res = DB::table('browers')->insert($insertData);
        
        if ($res) {
            parent::writeJson([
                'status' => parent::STATUS_OK,
                'msg' => '操作成功'
            ]);
        } else {
            parent::writeJson([
                'status' => parent::STATUS_DB_EXCEPTION,
                'msg' => '系统错误'
            ]);
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
        $userId = intval($this->_http->getQuery('user_id'));
        $page = intval($this->_http->getQuery('page', 1));
        $pagesize = intval($this->_http->getQuery('limit', 10));
        if ($userId <= 0) {
            parent::writeJson([
                'status' => parent::STATUS_PARAMS_EXCEPTION,
                'msg' => '参数异常'
            ]);
        }
        
        $page = $page == 0 ? 1 : $page;
        $offset = ($page - 1) * $pagesize;
        $sql = "SELECT * FROM `network_browers` WHERE user_id={$userId} order by FROM_UNIXTIME(created_at,'%Y-%m-%d %H:%i') desc LIMIT $offset,$pagesize";
        $results = DB::select($sql);
        $data = array();
        
        foreach ($results as $row) :
            
            $datetime = date('Y-m-d',$row->created_at);
            $type = $row->type;
            
            switch ($type){
                case 1:
                    $statusStr = '';
                    $rs = DB::connection('net')->table('store')->where('id',$row->store_id)->first();
                    switch ($rs->status) {
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
                    
                    $data[$datetime][] = array(
                        'type' => 1,
                        'store_id' => $rs->id,
                        'district' => $rs->district,
                        'title' => $rs->title,
                        'thumb' => $rs->thumb,
                        'rental_year' => $rs->rental_year,
                        'updated_at' => $rs->updated_at,
                        'build_area' => $rs->build_area,
                        'address' => $rs->address,
                        'username' => $rs->username,
                        'identity' => $rs->identity,
                        'source_website' => getNetStoreSource($rs->source_website),
                        'site_url' => $rs->site_url,
                        'status_str' => $statusStr,
                        'status' => $rs->status,
                        'tags'=>'',
                        'trade'=>''
                    );                   
                    break;
                case 2:
                    $r = DB::connection('ant')->table('store')
                    ->where('id', $row->store_id)
                    ->first();
                    
                    
                    $district = $r->district;
                    
                    // 状态
                    $statusStr = "";
                    switch ($r->status) {
                        case 0:
                            $statusStr = '转租中';
                            break;
                        case 1:
                            $statusStr = '营业中';
                            break;
                    }
                    
                    $thumb_door = '';
                    $rowThumb = DB::connection('ant')->table('store_thumb')
                    ->select('type', 'qiniu_key')
                    ->where([
                        'type' => 4,
                        'store_id' => $row->store_id
                    ])
                    ->orderBy('id', 'asc')
                    ->first();
                    if (! empty($rowThumb)) {
                        $thumb_door = Yaf\Registry::get('config')->qiniu->url . $rowThumb->qiniu_key . '?imageView2/1/w/225/h/225';
                    }
                    
                    $rental_year = $r->rental_year;
                    $updated_at = date('Y-m-d', $r->updated_at);
                    $build_area = $r->build_area;
                    $address = $r->trade . ' ' . $r->street_sn . "号";
                    
                    // 联系人信息
                    $contacts = DB::connection('ant')->table('store_call')
                    ->select('id', 'phone', 'wechat', 'role_name', 'contacts')
                    ->where('store_id', $row->store_id)
                    ->orderBy('id', 'asc')
                    ->first();
                    $username = '';
                    $identity = '';
                    if (! empty($contacts)) {
                        $username = $contacts->contacts;
                        $identity = $contacts->role_name;
                    }
                    
                    // $title = $r->present_operation;
                    $title = $r->district . $r->street . "·" . $r->build_area . "㎡" . $r->present_operation . $statusStr;
                    
                    $data[$datetime][] = array(
                        'type' => 2,
                        'store_id' => $row->store_id,
                        'district' => $district,
                        'title' => $title,
                        'thumb' => $thumb_door,
                        'rental_year' => $rental_year,
                        'updated_at' => $updated_at,
                        'build_area' => $build_area,
                        'address' => $address,
                        'username' => $username,
                        'identity' => $identity,
                        'source_website' => '',
                        'site_url' => '',
                        'status_str' => $statusStr,
                        'tags'=> $r->tags,
                        'trade'=> $r->trade,
                        'status' => $r->status
                        
                    );
                    break;                
            }
            
        endforeach;
        
        
        $newData = [];
        
        $dateStr = [];
        
        foreach ($data as $k=>$v):
           
             if (!in_array($k, $dateStr)){
                    array_push($dateStr, $k);
                    $newData['date'][] = $k;
                    $newData['items'][]= $v;
             }else{
                    $newData['items'][] = $v;
             }
                
        endforeach;
        
        
        $res = [];
        $dateArr = [];
        $dataArr = [];
        if (count($newData)>0){
            $dateArr =  $newData['date'];
            $dataArr = $newData['items'];
        }
        for($i=0;$i<count($dataArr);$i++){
            
            $res[$i]['date'] = $dateArr[$i];
            $res[$i]['items'] = $dataArr[$i];
            
        }
        
        parent::writeJson([
            'status' => parent::STATUS_OK,
            'data' => $res
        ]);
        
        
        return false;
    }
    
    /**
    * bus
    * 
    * @author kevin
    * @access public
    * @param mixed $code    
    * @return json
    */
    public function busAction() {
        
        $url = "http://restapi.amap.com/v3/place/around?key=050e4206e58b0c01617716357b3a05f6&location=120.057595,30.281288&keywords=公交站&types=150700&radius=1000&offset=300&page=1&extensions=all";
        
        $contents = http_get($url);
        
        $res = json_decode($contents);
        
        if($res->status==1 && is_object($res)){
            
            //echo $res->count;
            foreach ($res->pois as $row):
            
            //echo $row->name."<Br>";
            //echo $row->location."<br>";
            echo $row->address."<br>";
            endforeach;
            
        }
        
        //var_dump($res);
        
        return false;
    }
    
    public function buildingAction() {
        
        $url = "http://restapi.amap.com/v3/place/around?key=050e4206e58b0c01617716357b3a05f6&location=120.057595,30.281288&keywords=写字楼&types=120200&radius=1000&offset=300&page=1&extensions=all";
        
        $contents = http_get($url);
        
        $res = json_decode($contents);
        
        if($res->status==1 && is_object($res)){
            
            echo $res->count;
            foreach ($res->pois as $row):
            
            echo $row->name."<Br>";
            echo $row->location."<br>";
            endforeach;
            
        }
        
        //var_dump($res);
        
        return false;
    }
    
    
    /**
     * test
     *
     * @author kevin
     * @access public
     * @param mixed $code            
     * @return json
     */
    public function testAction()
    {
        
        $url = "http://restapi.amap.com/v3/place/around?key=050e4206e58b0c01617716357b3a05f6&location=120.057595,30.281288&keywords=学校&types=141200&radius=1000&offset=300&page=1&extensions=all";
        
        $contents = http_get($url);
        
        $res = json_decode($contents);
        
        if($res->status==1 && is_object($res)){
            
            //echo $res->count;
            foreach ($res->pois as $row):
                
                //echo $row->name."<Br>";
                  echo $row->location."<br>";
            
            endforeach;
            
        } 
        
        //var_dump($res);
        
        return false;
        try {
            $sphinx = new SphinxClient();
            $sphinx->setServer("localhost", 9313);
            $sphinx->setMatchMode(SPH_MATCH_PHRASE); // SPH_MATCH_ALL
            $result = $sphinx->query('西湖', "*");
            
            // if (array_key_exists('matches', $result)){
            // $ids = array_keys($result['matches']);
            // $storeSearchIds = implode(',', $ids);
            // $condition.=" AND id in($storeSearchIds) ";
            // }
            
            dd($result);
        } catch (Exception $e) {}
        return false;
    }

    public function test1Action()
    {
        try {
            $sphinx = new SphinxClient();
            $sphinx->setServer("101.200.87.137", 9312);
            $sphinx->setMatchMode(SPH_MATCH_PHRASE); // SPH_MATCH_ALL
            $result = $sphinx->query('西湖', "*");
            
            // if (array_key_exists('matches', $result)){
            // $ids = array_keys($result['matches']);
            // $storeSearchIds = implode(',', $ids);
            // $condition.=" AND id in($storeSearchIds) ";
            // }
            
            dd($result);
        } catch (Exception $e) {}
        return false;
    }
}