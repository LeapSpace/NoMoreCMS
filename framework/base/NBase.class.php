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
	
	public function __get($name){
		return '';
	}

	public function __set($property_name, $value){
		$this->$property_name = $value;
	}

	public function render($view, $param=null){
		//
		$className = get_class($this);
		$class = NMescapeString($className,'Controller');
		$class = strtolower($class);
		if(file_exists(NM::$modulePath.DIRECTORY_SEPARATOR.'view'.DIRECTORY_SEPARATOR.$class.DIRECTORY_SEPARATOR.$view.'.php')){
			if(isset($param) && is_array($param)){
				extract($param);
			}
			$param = null;
			ob_start();
			include(NM::$modulePath.DIRECTORY_SEPARATOR.'view'.DIRECTORY_SEPARATOR.$class.DIRECTORY_SEPARATOR.$view.'.php');
			$content = ob_get_contents();
			ob_end_clean();
			
			if($this->layout===false){
				echo $content;
			}else{
				$this->layout = empty($this->layout)?'layout':$this->layout;
				if(file_exists(NM::$modulePath.DIRECTORY_SEPARATOR.'view'.DIRECTORY_SEPARATOR.'layout'.DIRECTORY_SEPARATOR.$this->layout.'.php')){
					include(NM::$modulePath.DIRECTORY_SEPARATOR.'view'.DIRECTORY_SEPARATOR.'layout'.DIRECTORY_SEPARATOR.$this->layout.'.php');
				}else{
					echo $content;
				}
			}
		}
	}

	public function loadModel($modelName){
		$arr = explode('.', $modelName);;
		if(count($arr)==1){
			if(file_exists(NM::$modulePath.DIRECTORY_SEPARATOR.'model'.DIRECTORY_SEPARATOR.$modelName.'.php')){
				require_once(NM::$modulePath.DIRECTORY_SEPARATOR.'model'.DIRECTORY_SEPARATOR.$modelName.'.php');
			}else{
				throw new NException('no such model');
			}
		}else{
			$modelName = array_pop($arr);
			$modulePath = ModulePath.DIRECTORY_SEPARATOR.implode(DIRECTORY_SEPARATOR.'modules',$arr);
			if(file_exists($modulePath.DIRECTORY_SEPARATOR.'model'.DIRECTORY_SEPARATOR.$modelName.'.php')){
				require_once($modulePath.DIRECTORY_SEPARATOR.'model'.DIRECTORY_SEPARATOR.$modelName.'.php');
			}else{
				throw new NException('no such model');
			}
		}
	}

	public function redirect($url){
		header('Location:'.$url);
		exit;
	}
}