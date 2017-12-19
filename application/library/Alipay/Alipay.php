<?php
namespace Alipay;
header("Content-type: text/html; charset=utf-8");

require_once dirname(__FILE__).'/model/builder/AlipayTradePrecreateContentBuilder.php';
require_once dirname(__FILE__).'/service/AlipayTradeService.php';

class Alipay{
    
    
    public static function makeQrCode($outTradeNo,$subject,$totalAmount){
        $undiscountableAmount = "0.01";
        $body = "购买商品2件共15.00元";
        $operatorId = "test_operator_id";
        $storeId = "test_store_id";
        $alipayStoreId= "test_alipay_store_id";
        $providerId = "";   //系统商pid,作为系统商返佣数据提取的依据
        
        $extendParams = new ExtendParams();
        $extendParams->setSysServiceProviderId($providerId);
        $extendParamsArr = $extendParams->getExtendParams();
        
        $timeExpress = "5m";
        
        // 商品明细列表，需填写购买商品详细信息，
        $goodsDetailList = array();
        
        $goods1 = new GoodsDetail();
        
        $goods1->setGoodsId("apple-01");
        $goods1->setGoodsName("iphone");
        $goods1->setPrice(3000);
        $goods1->setQuantity(1);
        //得到商品1明细数组
        $goods1Arr = $goods1->getGoodsDetail();
        
        // 继续创建并添加第一条商品信息，用户购买的产品为“xx牙刷”，单价为5.05元，购买了两件
        $goods2 = new GoodsDetail();
        $goods2->setGoodsId("apple-02");
        $goods2->setGoodsName("ipad");
        $goods2->setPrice(1000);
        $goods2->setQuantity(1);
        //得到商品1明细数组
        $goods2Arr = $goods2->getGoodsDetail();
        
        $goodsDetailList = array($goods1Arr,$goods2Arr);
        
        dd( $goodsDetailList );
        
        
        //第三方应用授权令牌,商户授权系统商开发模式下使用
        $appAuthToken = "";//根据真实值填写
        
        // 创建请求builder，设置请求参数
        $qrPayRequestBuilder = new AlipayTradePrecreateContentBuilder();
        $qrPayRequestBuilder->setOutTradeNo($outTradeNo);
        $qrPayRequestBuilder->setTotalAmount($totalAmount);
        $qrPayRequestBuilder->setTimeExpress($timeExpress);
        $qrPayRequestBuilder->setSubject($subject);
        $qrPayRequestBuilder->setBody($body);
        $qrPayRequestBuilder->setUndiscountableAmount($undiscountableAmount);
        $qrPayRequestBuilder->setExtendParams($extendParamsArr);
        $qrPayRequestBuilder->setGoodsDetailList($goodsDetailList);
        $qrPayRequestBuilder->setStoreId($storeId);
        $qrPayRequestBuilder->setOperatorId($operatorId);
        $qrPayRequestBuilder->setAlipayStoreId($alipayStoreId);
        
        $qrPayRequestBuilder->setAppAuthToken($appAuthToken);
        
        dd( $qrPayRequestBuilder);
        // 调用qrPay方法获取当面付应答
        $qrPay = new AlipayTradeService($config);
        $qrPayResult = $qrPay->qrPay($qrPayRequestBuilder);
        
        //	根据状态值进行业务处理
        switch ($qrPayResult->getTradeStatus()){
            case "SUCCESS":
                echo "支付宝创建订单二维码成功:"."<br>---------------------------------------<br>";
                $response = $qrPayResult->getResponse();
                $qrcode = $qrPay->create_erweima($response->qr_code);
                echo $qrcode;
                print_r($response);
                
                break;
            case "FAILED":
                echo "支付宝创建订单二维码失败!!!"."<br>--------------------------<br>";
                if(!empty($qrPayResult->getResponse())){
                    print_r($qrPayResult->getResponse());
                }
                break;
            case "UNKNOWN":
                echo "系统异常，状态未知!!!"."<br>--------------------------<br>";
                if(!empty($qrPayResult->getResponse())){
                    print_r($qrPayResult->getResponse());
                }
                break;
            default:
                echo "不支持的返回状态，创建订单二维码返回异常!!!";
                break;
        }
        
        
    }
    
}