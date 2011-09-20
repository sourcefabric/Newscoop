<?php
/**
 * HybridAuth
 * 
 * An open source Web based Single-Sign-On PHP Library used to authentificates users with
 * major Web account providers and accessing social and data apis at Google, Facebook,
 * Yahoo!, MySpace, Twitter, Windows live ID, etc. 
 *
 * Copyright (c) 2009 (http://hybridauth.sourceforge.net)
 *
 * @package		Hybrid_Auth
 * @author		hybridAuth Dev Team
 * @copyright	Copyright (c) 2009, hybridAuth Dev Team.
 * @license		http://hybridauth.sourceforge.net/licenses.html under MIT and GPL
 * @link		http://hybridauth.sourceforge.net 
 */
 
// ------------------------------------------------------------------------ 

<?php
/*
 * Copyright 2008 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/**
 * This is intended to be an example only!
 *
 * Storage table is created by:
 *   CREATE TABLE storage ( `key` char(255) not null, `value` char(255) not null, created int not null, expiration int not null default '0', index(`key`) );

 *
 * Add your mysql host name, user name and password in the private class variables below
 */

class buzzDBStorage extends buzzStorage {
  private $db;
  private $mysql_host = ':/tmp/mysql.sock';
  private $mysql_user = 'root';
  private $mysql_password = '';

  public function __construct() {
    if (!$this->db = mysql_connect($this->mysql_host, $this->mysql_user, $this->mysql_password)) {
      throw new buzzStorageException("Could not create db link");
    }
    if (!mysql_select_db('buzz', $this->db)) {
      throw new buzzStorageException("Could not select db");
    }
  }

  public function __destruct() {
    // randomize cleanup, so it processes ~ once every 1000 requests
    if (rand(0,999) == 500) {
      // delete all temporary entries that are more then an hour old
      mysql_query($this->db, "delete from storage where key like '%:nonce%' and created < ".time() - 60 * 60);
      mysql_query($this->db, "delete from storage where key like '%:originalUrl%' and created < ".time() - 60 * 60);
    }
  }

  public function get($key, $expiration = false) {
    // query the storage db for the key, but only if the time created + expiration < the current time in seconds, or the expiration == 0 (infinite)
    $res = mysql_query("select `value` from storage where `key` = '" . mysql_real_escape_string($key) . "' and (created + expiration > " . time() . " or expiration = 0)", $this->db);
    if (mysql_num_rows($res)) {
      list($val) = mysql_fetch_row($res);
      $val = unserialize($val);
      return $val;
    }
    return false;
  }

  public function set($key, $val, $expiration = false) {
    // use mysql's 'on duplicate key update syntax to make this a bit faster
    // on db's without this functionality you would do:
    //   insert key, val into table
    //   if failed, update val for key
    $key = mysql_real_escape_string($key, $this->db);
    $val = mysql_real_escape_string(serialize($val), $this->db);
    if (!$expiration) {
      $expiration = '0';
    } else {
      $expiration =  mysql_real_escape_string($expiration, $this->db);
    }
    $time = time();
    $query = "
    insert into storage (`key`, `value`, created, expiration) values (
    	'$key',
    	'$val',
    	$time,
		$expiration
	)
	on duplicate key update
		`value` = '$val',
		created = $time,
		expiration = $expiration";
	$res = mysql_query($query, $this->db);
  }

  public function delete($key) {
    $key = mysql_real_escape_string($key, $this->db);
    mysql_query("delete from storage where key = '$key'", $this->db);
  }
}
