<?php

////////////////////////////////////////////////////////////////////////////////
//                                                                            //
//   Copyright (C) 2006  Phorum Development Team                              //
//   http://www.phorum.org                                                    //
//                                                                            //
//   This program is free software. You can redistribute it and/or modify     //
//   it under the terms of either the current Phorum License (viewable at     //
//   phorum.org) or the Phorum License that was distributed with this file    //
//                                                                            //
//   This program is distributed in the hope that it will be useful,          //
//   but WITHOUT ANY WARRANTY, without even the implied warranty of           //
//   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                     //
//                                                                            //
//   You should have received a copy of the Phorum License                    //
//   along with this program.                                                 //
////////////////////////////////////////////////////////////////////////////////

/*
 * Simple file-based caching-layer
 * Recommended are some more sophisticated solutions, like 
 * memcached-, mmcache/eaccelerator-layer
 */
if(!defined("PHORUM")) return;

/* Only load the caching mechanism if we have a cache directory configured. */
if(!isset($PHORUM["cache"])) return;
 
 
/* initializing our real cache-dir */
$PHORUM['real_cache']=$PHORUM['cache']."/".md5(__FILE__);



/*
 * This function returns the cached data for the given key 
 * or NULL if no data is cached for this key
 */
function phorum_cache_get($type,$key) {
	
	$partpath=$GLOBALS['PHORUM']['real_cache']."/".$type;

	if(is_array($key)) {
		$ret=array();
		foreach($key as $realkey) {
		    $path=$partpath."/".wordwrap(md5($realkey), PHORUM_CACHE_SPLIT, "/", true)."/data.php";
		    if(file_exists($path)){
		        $retval=unserialize(file_get_contents($path));
		        // the data is: array($ttl_time,$data)
		        if($retval[0] < time()) { // timeout
		        	unlink($path);
		        } else {
		        	$ret[$realkey]=$retval[1];	
		        }
		        unset($retval);
		    }				
		}
	} else {
	    $path=$partpath."/".wordwrap(md5($key), PHORUM_CACHE_SPLIT, "/", true)."/data.php";
	    if(!file_exists($path)){
	        $ret=NULL;
	    } else {
	        $ret=unserialize(file_get_contents($path));
	        // the data is: array($ttl_time,$data)
	        if($ret[0] < time()) { // timeout
	        	$ret=NULL;	
	        	unlink($path);
	        } else {
	        	$ret=$ret[1];	
	        }
	    }
	}
	
	
	if(is_array($ret) && count($ret) == 0) {
		$ret=NULL;	
	}
    
    return $ret;
    
}

/*
 * Puts some data into the cache 
 * returns number of bytes written (something 'true') or false ... 
 * depending of the success of the function
 */
function phorum_cache_put($type,$key,$data,$ttl=PHORUM_CACHE_DEFAULT_TTL) {

    $path=$GLOBALS['PHORUM']['real_cache']."/$type/".wordwrap(md5($key), PHORUM_CACHE_SPLIT, "/", true);
    if(!file_exists($path)){
        phorum_cache_mkdir($path);
    }
    $file=$path."/data.php";
    $ttl_time=time()+$ttl;
    $fp=fopen($file,"w");
    $ret=fwrite($fp,serialize(array($ttl_time,$data)));
    fclose($fp);    
    
    return $ret;   
}

/*
 * Removes a key from the cache
 */
function phorum_cache_remove($type,$key) {

    $ret  =true;
    $path=$GLOBALS['PHORUM']['real_cache']."/$type/".wordwrap(md5($key), PHORUM_CACHE_SPLIT, "/", true)."/data.php";
    if(file_exists($path)) {
        $ret=unlink($path);   
    }
    
    return $ret;
} 

/*
 * Clears all data from the cache
 */
function phorum_cache_clear() {
    $dir = $GLOBALS['PHORUM']['real_cache'];
    $ret = false;
    
    if(!empty($dir) && $dir != "/") {
        phorum_cache_rmdir($dir);
    }
    
    return $ret;   
}

/*
 type can be nearly each value to specify a group of data
 used are currently:
 'user'
*/

// helper functions

// recursively deletes all files/dirs in a directory 

// recursively creates a directory-tree
function phorum_cache_mkdir($path) {
    if(empty($path)) return false;
    if(is_dir($path)) return true;
    if (!phorum_cache_mkdir(dirname($path))) return false;
    mkdir($path);
    return true;
}

// recursively deletes all files/dirs in a directory 
function phorum_cache_rmdir( $path ) {
	$stack[]=$path;

	$dirs[]=$path;

	while(count($stack)){
		$path=array_shift($stack);
		$dir = opendir( $path ) ;
		while ( $entry = readdir( $dir ) ) {
			if ( is_file( $path . "/" . $entry ) ) {
				unlink($path."/".$entry);
			} elseif ( is_dir( $path . "/" . $entry ) && $entry != '.' && $entry != '..' ) {
				array_unshift($dirs, $path . "/" . $entry)  ;
				$stack[]=$path . "/" . $entry  ;
			}
		}
		closedir( $dir ) ;
	}
	foreach($dirs as $dir){
		rmdir($dir);
	}
	return;
}

?>
