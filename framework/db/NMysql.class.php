<?php
/**
 *
 *	数据库操作类
 *
 **/


//static const $Config;

class NMysql extends NDb{
	public $_pdo;
	public $_errno=0;
	public $_errmsg='';
	public static $gpc;
	/*
	 *	@func __construct
	 *	@result	mysql link
	 *	@description	数据库连接,构造连接对象
	 */
	function __construct($dbName = null){
		if(!get_magic_quotes_gpc()){
			self::$gpc = false;
		}else{
			self::$gpc = true;
		}
		//链接数据库
		$dbName = empty($dbName)?NM::$config['db']['dbname']:$dbName;
		if(empty($dbName)){
			throw new NException('no database');
		}
		$port = empty(NM::$config['db']['port'])?'3306':NM::$config['db']['port'];
		$charset = empty(NM::$config['db']['charset'])?'utf8':NM::$config['db']['charset'];
		$dbstr = 'mysql:host='.NM::$config['db']['host'].';port='.$port.';dbname='.$dbName;
		try{
			$pdo = new PDO($dbstr,NM::$config['db']['user'],NM::$config['db']['passwd'], array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES '.$charset,PDO::ATTR_PERSISTENT => true,PDO::ATTR_EMULATE_PREPARES=>false));
		}catch(PDOException $e){
			throw new NException($e->getMessage());
		}
		$this->_pdo = $pdo;
	}

	/*获取查询信息*/
	public function getData($sql, $arr=null){
		$query = $this->_pdo->prepare($sql);
		//echo $sql;
		if(!$query){
			$this->_errno = $this->_pdo->errorCode();
			$this->_errmsg = $this->_pdo->errorInfo()[2];
			return false;
		}else{
			$this->_errno = 0;
			$this->_errmsg = '';
		}
		$exeres = $query->execute($arr);
		if(!$exeres){
			$this->_errno = $this->_pdo->errorCode();
			$this->_errmsg = $this->_pdo->errorInfo()[2];
		}else{
			$this->_errno = 0;
			$this->_errmsg = '';
		}
		$resultArr = array();
		if($exeres){
			while($row = $query->fetch(PDO::FETCH_ASSOC)){
				$resultArr[] = $row;
			}
		}else{
			$resultArr = false;
		}
		return $resultArr;
	}

	public function getLine($sql, $arr=null){
		$query = $this->_pdo->prepare($sql);
		if(!$query){
			$this->_errno = $this->_pdo->errorCode();
			$this->_errmsg = $this->_pdo->errorInfo()[2];
			return false;
		}else{
			$this->_errno = 0;
			$this->_errmsg = '';
		}
		$exeres = $query->execute($arr);
		if(!$exeres){
			$this->_errno = $this->_pdo->errorCode();
			$this->_errmsg = $this->_pdo->errorInfo()[2];
		}else{
			$this->_errno = 0;
			$this->_errmsg = '';
		}
		$resultArr = array();
		if($exeres){
			$row = $query->fetch(PDO::FETCH_ASSOC);
			$resultArr = $row;
		}else{
			$resultArr = false;
		}
		return $resultArr;
	}
	
	/*执行SQL语句*/
	public function runSql($sql, $arr=null){
		$query = $this->_pdo->prepare($sql);
		if(!$query){
			$this->_errno = $this->_pdo->errorCode();
			$this->_errmsg = $this->_pdo->errorInfo()[2];
			return false;
		}else{
			$this->_errno = 0;
			$this->_errmsg = '';
		}
		$exeres = $query->execute($arr);
		if(!$exeres){
			$this->_errno = $this->_pdo->errorCode();
			$this->_errmsg = $this->_pdo->errorInfo()[2];
			return false;
		}else{
			$this->_errno = 0;
			$this->_errmsg = '';
		}
		return true;
	}
	
	/*返回当前连接最后一次插入数据库返回的id*/
	public function lastId(){
		return $this->_pdo->lastInsertId();
	}
	/*返回mysql错误代码*/
	public function errno(){
		return $this->_errno;
	}
	/*返回mysql错误信息*/
	public function errmsg(){
		return $this->_errmsg;
	}
	/*过滤SQL语句*/
	public function escape($str){
		return self::$gpc?$str:mysql_real_escape_string($str);
	}
	/*关闭当前连接*/
	function closeDb(){
		$this->_pdo = null;
	}
}