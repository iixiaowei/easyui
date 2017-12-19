<?php
/**
 * @name ErrorController
 * @desc 错误控制器, 在发生未捕获的异常时刻被调用
 * @see http://www.php.net/manual/en/yaf-dispatcher.catchexception.php
 * @author root
 */
class ErrorController extends Yaf\Controller_Abstract
{

    public function errorAction($exception)
    {
        assert($exception);
        $this->getView()->assign("code", $exception->getCode());
        $this->getView()->assign("message", $exception->getMessage());
        $this->getView()->assign("line", $exception->getLine());
		$this->getView()->display('error/error.phtml');
		return false;
    }
}