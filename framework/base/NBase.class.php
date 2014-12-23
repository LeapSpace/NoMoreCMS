<?php
class NBase{
	public static $config;
	public static $nowModule;
	public $layout;
	public function __construct($config){
		self::$config = $config;
		$this->layout = null;
	}

	public function __call($name, $arguments=null){
		throw new NException('method : '.$name.'does\'nt exist !');
	}
	
	public function render($view,array $param){
		//
		if(file_exists(NM::$modulePath.DIRECTORY_SEPARATOR.'view'.DIRECTORY_SEPARATOR.$view.'.php')){
			if(isset($param) && is_array($param)){
				extract($param);
			}
			$param = null;
			ob_start();
			include(NM::$modulePath.DIRECTORY_SEPARATOR.'view'.DIRECTORY_SEPARATOR.$view.'.php');
			$content = ob_get_contents();

			if($this->layout===false){
				echo $content;
			}else{
				$this->layout = empty($this->layout)?'layout':$this->layout;
				if(file_exists(NM::$modulePath.DIRECTORY_SEPARATOR.'view'.DIRECTORY_SEPARATOR.$this->layout.'.php')){
					include(NM::$modulePath.DIRECTORY_SEPARATOR.'view'.DIRECTORY_SEPARATOR.$this->layout.'.php');
				}else{
					echo $content;
				}
			}
		}
	}

	public function loadModel($modelName){
		if(file_exists(NM::$modulePath.DIRECTORY_SEPARATOR.'model'.DIRECTORY_SEPARATOR.$modelName.'.php')){
			require(NM::$modulePath.DIRECTORY_SEPARATOR.'model'.DIRECTORY_SEPARATOR.$modelName.'.php');
		}
	}
}