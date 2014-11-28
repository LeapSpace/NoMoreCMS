<?php
class NBase{
	public static $config;
	public function __construct($config){
		self::$config = $config;
	}

	public function __call($name, $arguments=null){
		throw new NException('method'.$name.'does\'nt exist !');
	}
}