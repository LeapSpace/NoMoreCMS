<?php
class SiteController extends NBase{
	public function IndexAction(){
		$this->loadModel('funcs');
		$abc = t();
		$b = 1;
		$b = test($b);
		$this->render('test',array('test'=>$abc, 'abc'=>$b));
	}

	public function TestAction(){
		$this->render('test',array('test'=>'dsahdjk', 'abc'=>'88878'));
	}
}