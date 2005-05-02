<?php

    // /home/username/foo/public_html/
    $dir          = $_SERVER['DOCUMENT_ROOT'];
    $include      = '/\.(php|shtml|html|htm|shtm|cgi|txt|doc|pdf|rtf|xls|csv)$/';
    $exclude      = '';
    $dirinclude   = '';
    $direxclude   = '/(^|\/)[._]|htmlarea/'; // Exclude the htmlarea tree by default

    $hash = '';
    foreach(explode(',', 'dir,include,exclude,dirinclude,direxclude') as $k)
    {
      if(isset($_REQUEST[$k]))
      {
        if(get_magic_quotes_gpc())
        {
          $_REQUEST[$k] = stripslashes($_REQUEST[$k]);
        }
        $hash .= $k . '=' . $_REQUEST[$k];
        $$k = $_REQUEST[$k];
      }
    }

    if($hash)
    {
      session_start();
      if(!isset($_SESSION[sha1($hash)]))
      {
        ?>
        [ ];
        <?php
        exit;
      }
    }

    function scan($dir, $durl = '')
    {
      global $include, $exclude, $dirinclude, $direxclude;
      static $seen = array();

      $files = array();

      $dir = realpath($dir);
      if(isset($seen[$dir]))
      {
        return $files;
      }
      $seen[$dir] = TRUE;
      $dh = @opendir($dir);


      while($dh && ($file = readdir($dh)))
      {
        if($file !== '.' && $file !== '..')
        {
          $path = realpath($dir . '/' . $file);
          $url  = $durl . '/' . $file;

          if(($dirinclude && !preg_match($dirinclude, $url)) || ($direxclude && preg_match($direxclude, $url))) continue;
          if(is_dir($path))
          {
            if($subdir = scan($path, $url))
            {
              $files[] = array('url'=>$url, 'children'=>$subdir);
            }
          }
          elseif(is_file($path))
          {
            if(($include && !preg_match($include, $url)) || ($exclude && preg_match($exclude, $url))) continue;
            $files[] = array('url'=>$url);
          }

        }
      }
      @closedir($dh);
      return dirsort($files);
    }

    function dirsort($files)
    {
      usort($files, 'dircomp');
      return $files;
    }

    function dircomp($a, $b)
    {
      if(is_array($a)) $a = $a[0];
      if(is_array($b)) $b = $b[0];
      return strcmp(strtolower($a), strtolower($b));
    }

    function to_js($var, $tabs = 0)
    {
      if(is_numeric($var))
      {
        return $var;
      }

      if(is_string($var))
      {
        return "'" . js_encode($var) . "'";
      }

      if(is_array($var))
      {
        $useObject = false;
        foreach(array_keys($var) as $k) {
            if(!is_numeric($k)) $useObject = true;
        }
        $js = array();
        foreach($var as $k => $v)
        {
          $i = "";
          if($useObject) {
            if(preg_match('#[a-zA-Z]+[a-zA-Z0-9]*#', $k)) {
              $i .= "$k: ";
            } else {
              $i .= "'$k': ";
            }
          }
          $i .= to_js($v, $tabs + 1);
          $js[] = $i;
        }
        if($useObject) {
            $ret = "{\n" . tabify(implode(",\n", $js), $tabs) . "\n}";
        } else {
            $ret = "[\n" . tabify(implode(",\n", $js), $tabs) . "\n]";
        }
        return $ret;
      }

      return 'null';
    }

    function tabify($text, $tabs)
    {
      if($text)
      {
        return str_repeat("  ", $tabs) . preg_replace('/\n(.)/', "\n" . str_repeat("  ", $tabs) . "\$1", $text);
      }
    }

    function js_encode($string)
    {
      static $strings = "\\,\",',%,&,<,>,{,},@,\n,\r";

      if(!is_array($strings))
      {
        $tr = array();
        foreach(explode(',', $strings) as $chr)
        {
          $tr[$chr] = sprintf('\x%02X', ord($chr));
        }
        $strings = $tr;
      }

      return strtr($string, $strings);
    }


    echo to_js(scan($dir));
?>
