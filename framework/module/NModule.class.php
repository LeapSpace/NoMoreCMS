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
		$modules = empty(NM::$router)?array(NM::$config['defaultModule']):NM::$router;

		/*	根据请求决定路由走向：
		*	优先路由至当前module下的controller；
		*	如果没有对应名称的controller则尝试调用当前module下子module；
		*	如果还没有，返回尝试调用当前模块下的默认controller
		*/
		$now = $modules[$tmp-1];
		$next = isset($modules[$tmp])?$modules[$tmp]:'';
		//test controllers if exists
		$c = ucwords($next).'Controller.php';
		//当前module下是否存在相应的controller，存在则调用此controller
		if(file_exists(self::$modulePath.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.$c)){
			require(self::$modulePath.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.$c);
			NM::$modulePath = self::$modulePath;
			$controller = ucwords($next).'Controller';

			/*	决定调用的方法：
			 *	若controller中存在路由解析出的方法并且该方法可被调用，则调用此方法；
			 *	否则调用该controller默认方法IndexAction；
			 *	若此controller中两种action均不存在，抛出异常
			 */
			$method = (isset($modules[$tmp+1]) && is_callable(array($controller, ucwords($modules[$tmp+1].'Action'))))?ucwords($modules[$tmp+1].'Action'):'IndexAction';
			if(is_callable(array($controller, $method))){
				NM::$request .= '/'.strtolower(NMescapeString($controller,'Controller')).'/'.strtolower(NMescapeString($method,'Action'));
				$c = new $controller(self::$config);
				call_user_func(array($c, $method));
			}else{
				throw new NException('no such method');
			}
		}else{
			/*	遍历该module下所有子module；
			*	如果存在相应的子module
			*	则路由至此子module
			*/
			$tmpModules = array();
			NMgetDirFile(self::$modulePath.DIRECTORY_SEPARATOR.'modules',$tmpModules);
			if(!empty($next) && in_array($next, $tmpModules)){
				NM::$request .= '/'.strtolower($next);
				self::loadModule($next);
			}else{
				/*	路由查找，调用的module或controller都不存在，
				*	则尝试调用此module下默认(与module同名)的controller
				*/
				if(file_exists(self::$modulePath.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.ucwords($tmpModule).'Controller.php')){
					require(self::$modulePath.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.ucwords($tmpModule).'Controller.php');
					NM::$modulePath = self::$modulePath;
					/*	决定调用的方法：
					*	若路由得到的action存在且可调用，则调用相应action；
					*	否则调用默认IndexAtion
					*	否则抛出异常
					*/
					$method = (empty($next) || !is_callable(array(ucwords($tmpModule).'Controller', ucwords($next.'Action'))))?'IndexAction':ucwords($next.'Action');
					if(is_callable(array(ucwords($tmpModule).'Controller', $method))){
						$controller = ucwords($tmpModule).'Controller';
						NM::$request .= '/'.strtolower(NMescapeString($controller,'Controller')).'/'.strtolower(NMescapeString($method,'Action'));
						$c = new $controller(self::$config);
						call_user_func(array($c, $method));
					}else{
						throw new NException('no such method');
					}
				}else{
					throw new NException('no module found!');
				}
			}
		}
	}
	/*
	*	加载模块
	*/
	public static function loadModule($moduleName){
		require(self::$modulePath.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.ucwords($moduleName).'Module.php');
		//父模块子模块同名会有问题(类名相同)
		return new $moduleName(self::$config);
	}

	public static function import(){
		//
	}
}