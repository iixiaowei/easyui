<?php
require_once dirname(getcwd()) . '/vendor/autoload.php';

class IndexController extends Yaf\Controller_Abstract
{
    
    protected $_http;
    
    public function init()
    {
        $this->_http = new Yaf\Request\Http();
    }
    
    
    public function indexAction()
    {
        
        $this->getView()->display('admin/index/index.phtml');
        return false;
    }   
}