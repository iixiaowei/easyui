<?php

class TaskController extends Yaf\Controller_Abstract
{

    /**
    * updateNetRental
    * 更新全网铺源租金信息
    * 
    * @author kevin
    * @access public
    * @param mixed $code    
    * @return json
    */
    public function updateNetRentalAction() {
        
        
        DB::connection('net')->table('store')->orderBy('id','desc')->chunk(100,function($stores){
               
               
               foreach ($stores as $store):
                   $rental_day = 0;
                   $rental_month = 0;
                   $rental_year = 0;
                   if($store->rental_type=="元/月" && intval($store->build_area)>0){
                       $rental_month = $store->rental/10000;
                       $rental_month = sprintf("%.2f", $rental_month);
                       $rental_day = getRentalDayByMonth($rental_month, $store->build_area);
                       $rental_year  =  getRentalYearByMonth($rental_month);                       
                       
                       DB::connection('net')->table('store')->where('id',$store->id)->update([
                           'rental_day'=>$rental_day,
                           'rental_month'=>$rental_month,
                           'rental_year'=>$rental_year
                       ]);
                       
                       
                   }elseif($store->rental_type==".元/月" && intval($store->build_area)>0){
                       $rental_month = $store->rental/10000;
                       $rental_month = sprintf("%.2f", $rental_month);
                       $rental_day = getRentalDayByMonth($rental_month, $store->build_area);
                       $rental_year  =  getRentalYearByMonth($rental_month);    
                       
                       DB::connection('net')->table('store')->where('id',$store->id)->update([
                           'rental_day'=>$rental_day,
                           'rental_month'=>$rental_month,
                           'rental_year'=>$rental_year
                       ]);
                       
                   }elseif ($store->rental_type=="万/年" && intval($store->build_area)>0){
                       
                       $rental_year = $store->rental;
                       $rental_day  = getRentalDayByYear($rental_year, $store->build_area);
                       $rental_month = getRentalMonthByYear($rental_year);
                       
                       DB::connection('net')->table('store')->where('id',$store->id)->update([
                           'rental_day'=>$rental_day,
                           'rental_month'=>$rental_month,
                           'rental_year'=>$rental_year
                       ]);
                       
                   }elseif ($store->rental_type=="元/天" && intval($store->build_area)>0){
                       
                       $rental_day = $store->rental;
                       $rental_month = getRentalMonthByDay($rental_day, $store->build_area);
                       $rental_year = getRentalYearByDay($rental_day, $store->build_area);
                       
                       DB::connection('net')->table('store')->where('id',$store->id)->update([
                           'rental_day'=>$rental_day,
                           'rental_month'=>$rental_month,
                           'rental_year'=>$rental_year
                       ]);
                       
                   }elseif ($store->rental_type=="元/平米.天" && intval($store->build_area)>0){
                       
                       $rental_day = $store->rental;
                       $rental_month = getRentalMonthByDay($rental_day, $store->build_area);
                       $rental_year = getRentalYearByDay($rental_day, $store->build_area);
                       
                       DB::connection('net')->table('store')->where('id',$store->id)->update([
                           'rental_day'=>$rental_day,
                           'rental_month'=>$rental_month,
                           'rental_year'=>$rental_year
                       ]);
                       
                   }elseif( $store->rental_type=="元/平米*月" && intval($store->build_area)>0 ){
                       
                       $rental_day = $store->rental/30;
                       $rental_month = getRentalMonthByDay($rental_day, $store->build_area);
                       $rental_year = getRentalYearByDay($rental_day, $store->build_area);
                       
                       DB::connection('net')->table('store')->where('id',$store->id)->update([
                           'rental_day'=>$rental_day,
                           'rental_month'=>$rental_month,
                           'rental_year'=>$rental_year
                       ]);
                       
                       
                   }   
               endforeach;
               
               //return false;
        });
        
        echo 'ok';
        return false;
    }
    
    
    /**
     * favorite
     * 收藏的店铺有更新
     *
     * @author kevin
     * @access public
     * @param mixed $code            
     * @return json
     */
    public function favoriteAction()
    {
        $storeIds = [];
        // modify_time>created_at and
        DB::connection('net')->table('store')
            ->whereRaw("modify_time>created_at and status=0")
            ->chunk(10, function ($stores) use (&$storeIds) {
            foreach ($stores as $store) {
                array_push($storeIds, $store->id);
            }
            // return false;
        });
        
        if (count($storeIds) > 0) {
            
            $store_ids = implode(',', $storeIds);
            $results = DB::select("select distinct(user_id),store_id from network_favorite where type=1 and store_id in ($store_ids)");
            
            if (count($results) > 0) {
                
                $res = jpushFavoriateStoreUpdated($results,count($storeIds));
            }
        }
        
        return false;
    }

    /**
     * subscribe
     * 订阅的店铺有更新
     *
     * @author kevin
     * @access public
     * @param mixed $code            
     * @return json
     */
    public function subscribeAction()
    {
        $storeIds = [];
        // modify_time>created_at and
        DB::connection('net')->table('store')
            ->whereRaw("modify_time>created_at and status=0")
            ->chunk(10, function ($stores) use (&$storeIds) {
            foreach ($stores as $store) {
                array_push($storeIds, $store->id);
            }
            // return false;
        });
        
        if (count($storeIds) > 0) {
            $store_ids = implode(',', $storeIds);
            //$message  = "您订阅的“订阅规则”有更新啦";
            
            $users= DB::select("select distinct(user_id),store_id from network_subscribe_store where type=1 and store_id in ($store_ids)");
            
            if (count($users) > 0) {
                
                $res = jpushUserSubcribeUpdated($users,count($storeIds));
                
            }            
            
            
        }
        
        return false;
    }
}