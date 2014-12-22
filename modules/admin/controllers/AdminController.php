<?php
class AdminController extends NBase{
	public function IndexAction(){
		echo 'hehe';
	}
	public function TestAction(){
		echo 'haha';
		echo $_SERVER['DOCUMENT_ROOT'] ;
	}
}