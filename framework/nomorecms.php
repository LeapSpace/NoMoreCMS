<?php

require_once('core.php');

class NM{
	protected static $mysql;
	public static $log;
	public $router = array();
	protected $config = array();
	protected $module;

	public static runApp($config){
		$this->config = $config;

		//路由功能
		$request = urlParse($_SERVER['REQUEST_URI']);
		$modules = array();
		NMgetDirFile(ModulePath, $modules);
		if(empty($modules)){
			die('no modules found!');
		}
		return ;
	}

	public function __construct(){
		//
	}
	public static function autoload($class){
	}
}