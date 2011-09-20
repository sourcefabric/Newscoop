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
 * A persistent storage class based on the memcache, which is not
 * really very persistent, as soon as you restart your memcache daemon
 * the storage will be wiped, however for debugging and/or speed
 * it can be useful, kinda, and cache is a lot cheaper then storage.
 * 
 * @author Chris Chabot
 */
class Hybrid_Storage_Memcache extends Hybrid_Storage { 
  private $connection = false;

  public function __construct($host, $port) {
    if (! function_exists('memcache_connect')) {
      throw new storageException("Memcache functions not available");
    }
    if ($host == '' || $port == '') {
      throw new storageException("You need to supply a valid memcache host and port");
    }
    $this->host = $host;
    $this->port = $port;
  }

  private function isLocked($key) {
    $this->check();
    if ((@memcache_get($this->connection, $key . '.lock')) === false) {
      return false;
    }
    return true;
  }

  private function createLock($key) {
    $this->check();
    // the interesting thing is that this could fail if the lock was created in the meantime..
    // but we'll ignore that out of convenience
    @memcache_add($this->connection, $key . '.lock', '', 0, 5);
  }

  private function removeLock($key) {
    $this->check();
    // suppress all warnings, if some other process removed it that's ok too
    @memcache_delete($this->connection, $key . '.lock');
  }

  private function waitForLock($key) {
    $this->check();
    // 20 x 250 = 5 seconds
    $tries = 20;
    $cnt = 0;
    do {
      // 250 ms is a long time to sleep, but it does stop the server from burning all resources on polling locks..
      usleep(250);
      $cnt ++;
    } while ($cnt <= $tries && $this->isLocked());
    if ($this->isLocked()) {
      // 5 seconds passed, assume the owning process died off and remove it
      $this->removeLock($key);
    }
  }

  // I prefer lazy initalization since the cache isn't used every request
  // so this potentially saves a lot of overhead
  private function connect() {
    if (! $this->connection = @memcache_pconnect($this->host, $this->port)) {
      throw new storageException("Couldn't connect to memcache server");
    }
  }

  private function check() {
    if (! $this->connection) {
      $this->connect();
    }
  }

  /**
   * @inheritDoc
   */
  public function get($key, $expiration = false) {
	$key = $this->storageKey . ":" . $key;

    $this->check();
    if (($ret = @memcache_get($this->connection, $key)) === false) {
      return false;
    }
    if (! $expiration || (time() - $ret['time'] > $expiration)) {
      $this->delete($key);
      return false;
    }
    return $ret['data'];
  }

  /**
   * @inheritDoc
   */
  public function set($key, $value) {
	$key = $this->storageKey . ":" . $key;

    $this->check();
    // we store it with the cache_time default expiration so objects will atleast get cleaned eventually.
    if (@memcache_set($this->connection, $key, array('time' => time(), 
        'data' => $value), false) == false) {
      throw new storageException("Couldn't store data in cache");
    }
  }

  /**
   * @inheritDoc
   */
  public function delete($key) {
	$key = $this->storageKey . ":" . $key;

    $this->check();
    @memcache_delete($this->connection, $key);
  }
}
  