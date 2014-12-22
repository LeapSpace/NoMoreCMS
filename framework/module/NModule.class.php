<?php
class NModule{
	public static $config;
	public static $nowModule=array();
	public static $modulePath;
	public function __construct($config){
		self::$config = $config;
		$tmpModule = get_called_class();
		$tmpModule = NMescapeString($tmpModule,'Module');
		self::$nowModule[] = strtolower($tmpModule);
		self::$modulePath = ModulePath.DIRECTORY_SEPARATOR.implode('modules'.DIRECTORY_SEPARATOR,self::$nowModule);
		$tmp = count(self::$nowModule);
		//print_r(self::$nowModule);
		$modules = empty(NM::$router)?array(NM::$config['DefaultModule']):NM::$router;
		//优先路由至当前module下的controller，如果没有对应名称的controller则尝试调用当前module下子module，如果还没有，返回尝试调用当前模块下的默认controller
		$now = $modules[$tmp-1];
		$next = isset($modules[$tmp])?$modules[$tmp]:'';
		//test controllers if exists
		$c = ucwords($next).'Controller.php';

		if(file_exists(self::$modulePath.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.$c)){
			require(self::$modulePath.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.$c);
			$method = isset($modules[$tmp+1])?$modules[$tmp+1]:'IndexAction';
			if(is_callable(array(ucwords($next), $method))){
				$controller = ucwords($next);
				$c = new $controller(self::$config);
				call_user_func(array($c, $method));
			}else{
				throw new NException('no such method');
			}
		}else{
			//test modules if exists
			$tmpModules = array();
			NMgetDirFile(self::$modulePath.DIRECTORY_SEPARATOR.'modules',$tmpModules);
			if(in_array($next, $tmpModules)){
				self::loadModule($next);
			}else{
				if(file_exists(self::$modulePath.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.ucwords($tmpModule).'Controller.php')){
					require(self::$modulePath.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.ucwords($tmpModule).'Controller.php');
					if(is_callable(array(ucwords($tmpModule).'Controller', 'IndexAction'))){
						$controller = ucwords($tmpModule).'Controller';
						$c = new $controller(self::$config);
						call_user_func(array($c, 'IndexAction'));
					}else{
						throw new NException('no such method');
					}
					//call_user_func(array(ucwords($tmpModule).'Controller', 'IndexAction'));
				}else{
					throw new NException('no module found!');
				}
			}
		}
	}

	public static function loadModule($moduleName){
		require(self::$modulePath.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.ucwords($moduleName).'Module.php');
		//父模块子模块同名会有问题(类名相同)
		return new $moduleName(self::$config);
	}

	public static function import(){
		//
	}
}