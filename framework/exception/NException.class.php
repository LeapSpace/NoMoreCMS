<?php
class NException extends Exception{
	public function __construct($msg=null){
		echo $msg;
	}
}