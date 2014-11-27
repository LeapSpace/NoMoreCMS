<?php
/**
 *
 *	数据库操作类
 *
 **/


//static const $Config;

class NMMysql extends NBase{
	public $_LINK;
	
	/*
	 *	@func __construct
	 *	@result	mysql link
	 *	@description	数据库连接,构造连接对象
	 */
	function __construct($dbName = null){
		//链接数据库
		$dbName = empty($dbName)?NM::$config['db']['dbname']:$dbName;
		if(empty($dbName)){
			throw new NException('no database');
		}
		$port = empty(self::$config['db']['port'])?'3306':NM::$config['db']['port'];
		$_LINK = @mysql_connect(NM::$config['db']['host'] . ":" . $port, NM::$config['db']['user'], NM::$config['db']['passwd']);
		if(!$_LINK){
			die("db_connect failed with error:" . mysql_error());
		}else{
			$dbSelect = mysql_select_db(NM::$dbName, $_LINK);
			@mysql_query('set names utf8');
			if(!$dbSelect){
				die("db_select failed with error:" . mysql_error($_LINK));
			}
			$this->_LINK = $_LINK;
		}
	}

	/*设置连接主机*/
	private function setHost($host){
		$this->_HOST = $host;
	}
	/*设置连接端口*/
	private function setPort($port){
		$this->_PORT = $port;
	}
	/*设置连接数据库名称*/
	private function setDbName($dbName){
		$this->_DBNAME = $dbName;
	}
	/*设置连接用户名*/
	private function setUser($user){
		$this->_USER = $user;
	}
	/*设置连接密码*/
	private function setPwd($pwd){
		$this->_PWD = $pwd;
	}
	/*获取查询信息*/
	public function getData($sql){
		$result = mysql_query( $sql, $this->_LINK);
		if($this->errno()){
			return false;
		}
		$resultArr = array();
		while($row = mysql_fetch_assoc($result)){
			$resultArr[] = $row;
		}
		return $resultArr;
	}

	public function getLine($sql){
		$result = mysql_query($sql, $this->_LINK);
		if($this->errno()){
			return false;
		}
		return mysql_fetch_assoc($result);
	}
	
	//返回影响执行语句影响的行数
	public function affectRows(){
		return mysql_affected_rows($this->_LINK);
	}

	/*执行SQL语句*/
	public function runSql($sql){
		$result = mysql_query( $sql, $this->_LINK);
		if($this->errno()){
			return false;
		}
		return true;
	}
	
	/*返回当前连接最后一次插入数据库返回的id*/
	public function lastId(){
		return mysql_insert_id($this->_LINK);
	}
	/*返回mysql错误代码*/
	public function errno(){
		return mysql_errno($this->_LINK);
	}
	/*返回mysql错误信息*/
	public function errmsg(){
		return mysql_error($this->_LINK);
	}
	/*过滤SQL语句*/
	public function escape($str){
		return mysql_real_escape_string( $str, $this->_LINK );
	}
	/*关闭当前连接*/
	function closeDb(){
		@mysql_close($this->_LINK);
	}
}

/**
 *	@class LcMemcache
 *	@description memcache
 *
 */

 class LcMemcache{
	 var $_LINK;

	 function __construct(){
		 try{
		 $this->_LINK = new Memcache();
		 $c = new Config();
		 $Config = $c->_CONFIG;
		 $this->_LINK->connect($Config['mem_host'],$Config['mem_port']);
		 }catch(Exception $e){
			 return false;
		 }
	 }

	 public function set($key,$val,$flag,$expire){
		 $result = $this->_LINK->set($key,$val,$flag,$expire);
		 return $result;
	 }

	 public function get($key){
		 $result = $this->_LINK->get($key);
		 return $result;
	 }
	 public function delete($key){
		 $result = $this->_LINK->delete($key);
		 return $result;
	 }

	 public function add($key,$val,$flag,$expire){
		 $result = $this->_LINK->add($key,$val,$flag,$expire);
		 return $result;
	 }

	 public function close(){
		 $this->_LINK->close();
	 }
 }



/**
 *	@class LocalFileStore
 *	@description 处理本地文件存储类
 *
 */


class LocalFileStore{
	var $_FILE_SAVE_PATH;
	var $_STORAGE_PATH;
	var $_STORAGE_NAME;
	public $config;
	public $errno;
	public $errmsg;

	function __construct(){
		$c = new Config();
		$Config = $c->_CONFIG;
		$this->config = $Config;
		$this->setSavePath($Config['file_storage_path']);
		if(!$this->setStorage($Config['storage_name'])){
			return false;
		}
	}
	/* 处理上传文件 */
	public function upload($storageName, $destFileName, $srcFileName){
		if($storageName !== $this->_STORAGE_NAME){
			$this->errno = 1;
			$this->errmsg = 'no such storage';
			return false;
		}
		if(is_uploaded_file($srcFileName)){
			if(copy($srcFileName, $this->_STORAGE_PATH . $destFileName)){
				return true;
			}else{
				$this->errno = 1;
				$this->errmsg = 'upload failed';
				return false;
			}
		}else{
			$this->errno = 1;
			$this->errmsg = 'unknown error'.$srcFileName;
			return false;
		}
	}
	//返回文件名
	public function getUrl($storageName, $fileName){
		return 'http://'.$this->config['file_host'].'/'.$storageName . '/' . $fileName;
	}
	public function delete($storageName, $fileName){
		$file = $this->_FILE_SAVE_PATH . $storageName . '/' . $filename;
		if(file_exists($file)){
			if(unlink($file)){
				return true;
			}else{
				$this->errno = 1;
				$this->errmsg = 'delete failed!';
				return false;
			}
		}else{
			$this->errno = 1;
			$this->errmsg = 'no such file exist!';
			return false;
		}
	}
	/* 检查storage是否存在 */
	private function setStorage($storageName){
		$this->_STORAGE_NAME = $storageName;
		$storagePath = $this->_FILE_SAVE_PATH . $storageName;
		if( strrchr( $storagePath , "/" ) != "/" ) {
			$storagePath .= "/";
		}
		if(!file_exists($storagePath)){
			$this->errno = 1;
			$this->errmsg = 'file path not exist!';
			return false;
		}
		$this->_STORAGE_PATH = $storagePath;
		return true;
	}
	/* 设置存储路径 */
	private function setSavePath($pathStr){
		if( strrchr( $pathStr , "/" ) != "/" ) {
			$pathStr .= "/";
		}
		$pos = strpos($pathStr,'/');
		while($pos!==false){
			$tmpPath = substr($pathStr,0,$pos+1);
			if(!file_exists($tmpPath)){
				if(!mkdir($tmpPath,0777, true)){
					return false;
				}
			}
			$pos = strpos($pathStr,'/',$pos+1);
		}
		$this->_FILE_SAVE_PATH = $pathStr;
		return true;
	}
	public function errno(){
		return $this->errno;
	}
	public function errmsg(){
		return $this->errmsg;
	}
}

	/**
	 *
	 *	@class LcStorage
	 *	@description 文件存储类
	 *
	 */

class LcStorage{
	var $_STORAGE_TYPE;	//storage类型;
	var $_STORAGE;
	var $errno;
	var $errmsg;

	function __construct($ak = NULL, $sk = NULL, $host = NULL){
		$c = new Config();
		$Config = $c->_CONFIG;
		$this->setStorageType($Config['env']);
		$this->_STORAGE = new LocalFileStore();
	}

  	private function setStorageType($env){
    	$this->_STORAGE_TYPE = $env;
    }
	public function upload($storageName, $destFileName, $srcFileName){
		if($this->_STORAGE_TYPE == 'LOCAL'){
			$result = $this->_STORAGE->upload($storageName, $destFileName, $srcFileName);
			if($this->_STORAGE->errno()){
				$this->errno = $this->_STORAGE->errno();
				$this->errmsg = $this->_STORAGE->errmsg();
				return $this->errmsg();
			}else{
				return $this->_STORAGE->getUrl($storageName, $destFileName);
			}
		}else{
			return "no upload server!";
		}
	}

	public function getUrl($storageName, $fileName){
		if($this->_STORAGE_TYPE == 'LOCAL'){
			return $this->_STORAGE->getUrl($storageName, $fileName);
		}
	}

	public function delete($storageName, $fileName){
		if($this->_STORAGE_TYPE == 'LOCAL'){
			$this->_STORAGE->delete($storageName, $fileName);
			if($this->_STORAGE->errno()){
				$this->errno = $this->_STORAGE->errno();
				$this->errmsg = $this->_STORAGE->errmsg();
				return false;
			}else{
				return true;
			}
		}
	}

	public function errno(){
		return $this->errno;
	}

	public function errmsg(){
		return $this->errmsg;
	}
}

class FetchUrl{

	function __construct(){
	}
	public function fetch($url, $method, $connect_timeout, $timeout, $parameter=NULL)
	{
		$ci = curl_init();
		curl_setopt($ci, CURLOPT_ENCODING, 'gzip');
		curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, $connect_timeout); 
		curl_setopt($ci, CURLOPT_TIMEOUT, $timeout); 
		curl_setopt($ci, CURLOPT_RETURNTRANSFER, TRUE); 
		curl_setopt($ci, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0)");
		curl_setopt($ci, CURLOPT_URL, $url);	
		switch ($method) {
		case 'POST': 
			curl_setopt($ci, CURLOPT_POST, TRUE); 
			if (!empty($parameter)) { 
				curl_setopt($ci, CURLOPT_POSTFIELDS, http_build_query($parameter)); 
			} 
			break; 
		case 'GET': 
			
			break;
		} 
		$htmlpage = curl_exec($ci); 
		if($htmlpage === false)
		{
			$errinfo = curl_error($ci);
			$response = array('errinfo'=>$errinfo);
			return $response;
		}
		$status = curl_getinfo($ci, CURLINFO_HTTP_CODE);
		curl_close ($ci); 
		$response = array("errinfo"=>"","status"=>$status,"htmlpage"=>$htmlpage);
		return $response; 
	}
}