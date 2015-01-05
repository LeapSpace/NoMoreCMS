<?php
class NException extends Exception{
	public function __construct($msg=null){
		//print_r( $this->getTrace());
		echo $msg;
	}
}