<?php
class NBase{
	public static $config;
	public static $nowModule;
	public function __construct($config){
		self::$config = $config;
	}

	public function __call($name, $arguments=null){
		throw new NException('method : '.$name.'does\'nt exist !');
	}
	
	public function render($view,array $param){
		//
		if(file_exists(NM::$modulePath.DIRECTORY_SEPARATOR.'view'.DIRECTORY_SEPARATOR.$view.'.php')){
			if(isset($param) && is_array($param)){
				foreach($param as $key=>$v){
					if(is_string($key)){
						$$key = $v;
					}else{
						$key = null;
						$v = null;
					}
				}
			}
			$param = null;
			include(NM::$modulePath.DIRECTORY_SEPARATOR.'view'.DIRECTORY_SEPARATOR.$view.'.php');
		}
	}

	public function loadModel($modelName){
		if(file_exists(NM::$modulePath.DIRECTORY_SEPARATOR.'model'.DIRECTORY_SEPARATOR.$modelName.'.php')){
			require(NM::$modulePath.DIRECTORY_SEPARATOR.'model'.DIRECTORY_SEPARATOR.$modelName.'.php');
		}
	}
}