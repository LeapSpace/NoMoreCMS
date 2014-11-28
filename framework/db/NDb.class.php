<?php
/**
 *
 *	数据库操作类
 *
 **/


//static const $Config;

class NDb extends NBase{
	protected static $dbConfig;
	/*
	 *	@func __construct
	 *	@result	mysql link
	 *	@description	数据库连接,构造连接对象
	 */
	function __construct(){
		self::$dbConfig = NM::$config['db'];
	}

	abstract public function getData($sql, $arr=null);
	abstract public function getLine($sql, $arr=null);
	abstract public function runSql($sql, $arr=null);
}