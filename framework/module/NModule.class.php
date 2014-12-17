<?php
class NModule{
	public static $config;
	public static $nowModule=array();
	public static $modulePath;
	public function __construct($config){
		self::$config = $config;
		self::$nowModule[] = get_called_class();
		self::$modulePath = ModulePath.implode(DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR,self::$nowModule);
		$tmp = count($nowModule);
		//优先路由至当前module下的controller，如果没有对应名称的controller则尝试调用当前module下子module，如果还没有，返回尝试调用当前模块下的默认controller
		//$next = 
	}

	public function __call($name, $arguments=null){
		throw new NException('method : '.$name.'does\'nt exist !');
	}

	public static function loadModule($moduleName){
		require(self::$modulePath.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.ucwords($moduleName).'Module.php');
	}
}