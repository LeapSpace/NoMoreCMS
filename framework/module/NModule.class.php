<?php
class NModule{
	public static $config;
	public static $nowModule=array();
	public static $modulePath;
	public function __construct($config){
		self::$config = $config;
		$tmpModule = get_called_class();
		self::$nowModule[] = $tmpModule;
		self::$modulePath = ModulePath.implode(DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR,self::$nowModule);
		$tmp = count($nowModule);
		$modules = empty(NM::$router)?NM::$config['DefaultModule']:NM::$router;
		//优先路由至当前module下的controller，如果没有对应名称的controller则尝试调用当前module下子module，如果还没有，返回尝试调用当前模块下的默认controller
		//$next = 
		$now = $modules[$tmp];
		$next = isset($modules[$tmp+1])?$modules[$tmp+1]:'';
		//test controllers
		$c = ucwords($next).'Controller.php';
		if(file_exists(self::$modulePath.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.$c)){
			require();
		}else{
			self::loadModule($next);
		}
	}

	public static function loadModule($moduleName){
		require(self::$modulePath.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.ucwords($moduleName).'Module.php');
		//父模块子模块同名会有问题(类名相同)
		return new $moduleName(self::$config);
	}
}