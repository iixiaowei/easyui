<?php

/**
 * @name Bootstrap
 * @author maydo
 * @desc 所有在Bootstrap类中, 以_init开头的方法, 都会被Yaf调用,
 * @see http://www.php.net/manual/en/class.yaf-bootstrap-abstract.php
 * 这些方法, 都接受一个参数:Yaf_Dispatcher $dispatcher
 * 调用的次序, 和申明的次序相同
 */
class Bootstrap extends Yaf\Bootstrap_Abstract {
    
    public function _initError() {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
    }
    
    public function _initLoader() {
        Yaf\Loader::import(APPLICATION_PATH . '/vendor/autoload.php');
    }
    
    public function _initConfig() {
        //把配置保存起来
        $arrConfig = Yaf\Application::app()->getConfig();
        Yaf\Registry::set('config', $arrConfig);
    }
    
    public function _initPlugin(Yaf\Dispatcher $dispatcher) {
        //注册一个插件
        $objSamplePlugin = new SamplePlugin();
        $dispatcher->registerPlugin($objSamplePlugin);
    }
    
    public function _initRoute(Yaf\Dispatcher $dispatcher) {
        $router = $dispatcher->getInstance()->getRouter();
        // 	    //$router->addConfig(Yaf\Registry::get('config')->routes);
        $simpleRoute = new Yaf\Route\Simple('m', 'c', 'a');
        $router->addRoute('simple_route', $simpleRoute);
    }
    
    public function _initView(Yaf\Dispatcher $dispatcher) {
        // 	    $view_engine = Yaf\Registry::get('config')->application->view->engine;
        // 	    $twig = new Twig\Adapter(APPLICATION_PATH. "/application/views/", Yaf\Registry::get('config')->toArray());
        // 	    $dispatcher->setView($twig);
        
        $smarty = new \Smarty\Adapter(APPLICATION_PATH . "/application/views/", Yaf\Registry::get("config")->smarty);
        $dispatcher->setView($smarty);
    }
    
    public function _initDefaultDbAdapter() {
        // 初始化 illuminate/database
        $capsule = new Illuminate\Database\Capsule\Manager();
        $capsule->addConnection(Yaf\Registry::get('config')->database->toArray());
        $capsule->addConnection(Yaf\Registry::get('config')->dbslave->toArray(),'slave');
        $capsule->setEventDispatcher(new Illuminate\Events\Dispatcher(new Illuminate\Container\Container()));
        $capsule->setAsGlobal();
        // 开启Eloquent ORM
        $capsule->bootEloquent();
        
        class_alias('Illuminate\Database\Capsule\Manager', 'DB');
    }
    
    public function _initFunction() {
        Yaf\Loader::import('Common/functions.php');
    }
    
    public function _initRedis() {
        // 	    Yaf\Loader::import('DdRedis/DdRedis.php');
        // 	    $redis = new \DdRedis\DdRedis("192.168.169.130","6379","nnup2017");
        // 	    Yaf\Registry::set('redis', $redis);
    }
    
    public static function error_handler($errno, $errstr, $errfile, $errline) {
        if (error_reporting() === 0)
            return;
            throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    }
    
    public function _initErrorHandler(Yaf\Dispatcher $dispatcher) {
        $dispatcher->setErrorHandler(array(get_class($this), 'error_handler'));
    }
    
}
