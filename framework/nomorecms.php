<?php

require_once('core.php');

class NM{
	protected static $mysql;
	public static $log;
	public $router = array();
	public static $config = array();

	public static function runApp($config){
		require($config);
		self::$config = $config;

		//路由功能
		$request = urlParse($_SERVER['REQUEST_URI']);
		$modules = array();
		NMgetDirFile(ModulePath, $modules);
		$modules = array_intersect($modules, self::$config['modules']);
		if(empty($modules)){
			die('no modules found!');
		}
		if(in_array($request[0], $modules)){
			$nowModule = $request[0];
		}else{
			if(empty(self::$config['defaultModule'])){
				die('no module found');
			}else{
				$nowModule = self::$config['defaultModule'];
			}
		}
		$class = empty($request[1])?'default':$request[1];
		$class = ucwords($class).'Controller';
		if(file_exists(ModulePath.DIRECTORY_SEPARATOR.$nowModule.DIRECTORY_SEPARATOR.'controller'.DIRECTORY_SEPARATOR.$class.'.php')){
			require(ModulePath.DIRECTORY_SEPARATOR.$nowModule.DIRECTORY_SEPARATOR.'controller'.DIRECTORY_SEPARATOR.$class.'.php');
			return new $class(self::$config);
		}else{
			throw new NException('request error');
		}
	}

	public function __construct(){
		//
	}
	public static function autoload($class){
		if(file_exists(FramemPath.self::$_coreClasses[$class])){
			require(FramemPath.self::$_coreClasses[$class]);
		}
	}
	
	private static $_coreClasses = array(
		'NBase' => '/base/NBase.class.php',
		'NDb' => '/db/NDb.class.php',
		'NLog' => '/log/NLog.class.php',
		'NCache' => '/NCache/cache.class.php',
		'NException' => '/exception/NException.class.php'
	);
}

spl_autoload_register(array('NM','autoload'));