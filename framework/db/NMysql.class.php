<?php
/**
 *
 *	数据库操作类
 *
 **/


//static const $Config;

class NMysql extends NDb{
	public $_pdo;
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
		$port = empty(self::$config['db']['port'])?'3306':NM::$config['db']['port'];
		$charset = empty(self::$config['db']['charset'])?'utf8':NM::$config['db']['charset'];
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
		$exeres = $query->execute($arr);
		$resultArr = array();
		if($exeres){
			while($row = $query->fetch()){
				$resultArr[] = $row;
			}
		}else{
			$resultArr = false;
		}
		return $resultArr;
	}

	public function getLine($sql, $arr=null){
		$query = $this->_pdo->prepare($sql);
		$exeres = $query->execute($arr);
		$resultArr = array();
		if($exeres){
			$row = $query->fetch()
			$resultArr = $row;
		}else{
			$resultArr = false;
		}
		return $resultArr;
	}
	
	/*执行SQL语句*/
	public function runSql($sql, $arr=null){
		$query = $this->_pdo->prepare($sql);
		$exeres = $query->execute($arr);
		if($this->errno()){
			return false;
		}
		return true;
	}
	
	/*返回当前连接最后一次插入数据库返回的id*/
	public function lastId(){
		return $this->_pdo->lastInsertId();
	}
	/*返回mysql错误代码*/
	public function errno(){
		return $this->_pdo->errorCode();
	}
	/*返回mysql错误信息*/
	public function errmsg(){
		return $this->_pdo->errorInfo();
	}
	/*过滤SQL语句*/
	public function escape($str){
		return self::$gpc?$str:addslashes($str);
	}
	/*关闭当前连接*/
	function closeDb(){
		$this->_pdo = null;
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