<?php

//返回某目录下子文件夹名称
function NMgetDirFile($path, &$files){
	if(!file_exists($path)){
		return false;
	}
	$path = rtrim($path, '/');
	$handle = opendir($path);
	while (false !== $f = readdir($handle)) { 
		if ($f != '.' && $f != '..') { 
			$filePath = $path.DIRECTORY_SEPARATOR.$f;
			if (is_file($filePath)) { 
				continue;
			} elseif (is_dir($filePath)) {
				if(preg_match('/^[0-9a-zA-Z]+$/',$f)){
					$files[] = $f;
				}
			} 
		} 
	} 
	closedir($handle); 
}
//url请求解析，决定路由走向
function urlParse($url){
	$match = array();
	if(preg_match('/^[\/0-9a-zA-Z]+$/',$url,$match)){
		return explode('/', trim($match[0],'/'));
	}else{
		return false;
	}
}

//查找字符串中第一次出现在字符列表中的位置
function findCharlist($str, array $charArr){
	$pos = false;
	foreach($charArr as $c){
		if($tmppos = strpos($str,$c)!==false){
			if($tmppos<=$pos){
				$pos = $tmppos;
			}
		}
	}
	return $pos;
}
