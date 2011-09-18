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

/*
 * This class implements a basic on disk storage. While that does
 * work quite well it's not the most elegant and scalable solution.
 * It will also get you into a heap of trouble when you try to run
 * this in a clustered environment. In those cases please use the
 * MySql back-end
 * 
 * @author Chris Chabot
 */
class Hybrid_Storage_File extends Hybrid_Storage {
  private $path;
  
  public function __construct($path)
  {
    $this->path = $path;
  }
  
  private function isLocked($storageFile) {
    // our lock file convention is simple: /the/file/path.lock
    return file_exists($storageFile . '.lock');
  }

  private function createLock($storageFile) {
    $storageDir = dirname($storageFile);
    if (! is_dir($storageDir)) {
      if (! @mkdir($storageDir, 0755, true)) {
        // make sure the failure isn't because of a concurency issue
        if (! is_dir($storageDir)) {
          throw new storageException("Could not create storage directory: $storageDir");
        }
      }
    }
    @touch($storageFile . '.lock');
  }

  private function removeLock($storageFile) {
    // suppress all warnings, if some other process removed it that's ok too
    @unlink($storageFile . '.lock');
  }

  private function waitForLock($storageFile) {
    // 20 x 250 = 5 seconds
    $tries = 20;
    $cnt = 0;
    do {
      // make sure PHP picks up on file changes. This is an expensive action but really can't be avoided
      clearstatcache();
      // 250 ms is a long time to sleep, but it does stop the server from burning all resources on polling locks..
      usleep(250);
      $cnt ++;
    } while ($cnt <= $tries && $this->isLocked($storageFile));
    if ($this->isLocked($storageFile)) {
      // 5 seconds passed, assume the owning process died off and remove it
      $this->removeLock($storageFile);
    }
  }

  private function getStorageDir($hash) {
    // use the first 2 characters of the hash as a directory prefix
    // this should prevent slowdowns due to huge directory listings
    // and thus give some basic amount of scalability
    return $this->path . '/' . substr($hash, 0, 2);
  }

  private function getStorageFile($hash) {
    return $this->getStorageDir($hash) . '/' . $hash;
  }

  public function get($key, $expiration = false) {
	Hybrid_Logger::info( "Hybrid_Storage_File::get( $key )" );
 
	$key = $this->storageKey . ":" . $key;

    $storageFile = $this->getStorageFile(md5($key));
    // See if this storage file is locked, if so we wait upto 5 seconds for the lock owning process to
    // complete it's work. If the lock is not released within that time frame, it's cleaned up.
    // This should give us a fair amount of 'Storage Stampeding' protection
    if ($this->isLocked($storageFile)) {
      $this->waitForLock($storageFile);
    }
    if (file_exists($storageFile) && is_readable($storageFile)) {
      $now = time();
      if (!$expiration || (($mtime = @filemtime($storageFile)) !== false && ($now - $mtime) < $expiration)) {
        if (($data = @file_get_contents($storageFile)) !== false) {
          $data = unserialize($data);  
		  return $data;
        }
      }
    }
 
    return false;
  }

  public function set($key, $value) {
	Hybrid_Logger::info( "Hybrid_Storage_File::set( $key ) ", $value );
	
	$key_o = $key;
	$key = $this->storageKey . ":" . $key;

    $storageDir = $this->getStorageDir(md5($key));
    $storageFile = $this->getStorageFile(md5($key));
    if ($this->isLocked($storageFile)) {
      // some other process is writing to this file too, wait until it's done to prevent hickups
      $this->waitForLock($storageFile);
    }
    if (! is_dir($storageDir)) {
      if (! @mkdir($storageDir, 0755, true)) {
        throw new storageException("Could not create storage directory: $storageDir");
      }
    }
    // we serialize the whole request object, since we don't only want the
    // responseContent but also the postBody used, headers, size, etc
    $data = serialize($value);
    $this->createLock($storageFile);
    if (! @file_put_contents($storageFile, $data)) {
      $this->removeLock($storageFile);
      throw new storageException("Could not store data in the file");
    }
    $this->removeLock($storageFile); 
  }

  public function delete($key) {
	Hybrid_Logger::info( "Hybrid_Storage_File::delete( $key ) " );
	
	$key = $this->storageKey . ":" . $key;

    $file = $this->getStorageFile(md5($key));
    if (file_exists($file)&&! @unlink($file)) {
      throw new storageException("Storage file could not be deleted");
    }
  }
}
