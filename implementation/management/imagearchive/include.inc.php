<?php
define(_IMG_PREFIX_, '/images/cms-image-');
define(_DIR_, '/priv/imagearchive/');
define(_IMAGEMAGICK_, TRUE);
define(_TUMB_CMD_, 'convert -sample 64x64');
define(_TUMB_PREFIX_, '/images/tumbnails/cms-tumb-');
define(_TMP_DIR_, '/tmp/');


function orE($input)
{
	if (empty($input)) {
		return 'unknown';
	} else {
		return $input;
	}
}

function trColor()
{
	global $color;

	if ($color) {
		$color = 0;
		return 'BGCOLOR="#D0D0B0"';
	} else {
		$color = 1;
		return 'BGCOLOR="#D0D0D0"';
	}
}

function cImgLink()
{
	global $S, $de, $ph, $da, $use, $O, $ImgOffs, $lpp, $D, $v;



	// regarding parameters from search form or link //////////////////////
	todef('S');
	todef('de');
	todef('ph');
	todef('da');
	todef('use');
    todef('v');

	if ($S && (isset($de) || isset($ph)  || isset($da)|| isset($use))) {

		if (isset($de)) {
			$Link['S']   .= "&S=1&de=".urlencode($de);
		}
		if (isset($ph)) {
			$Link['S']   .= "&S=1&ph=".urlencode($ph);
		}
		if (isset($da)) {
			$Link['S']   .= "&S=1&da=".urlencode($da);
		}
		if (isset($use)) {   
			$Link['S']   .= "&S=1&use=".urlencode($use);
		}

	}
	////////////////////////////////////////////////////////////////////

	// build the order statement ///////////////////////////////////////
	todef('O');
	todef('D');

	if ($D == 'ASC') {
		$HrefDir  = "ASC";
	} else {
		$HrefDir  = "DESC";
	}

	switch ($O) {
	case 'de':
		$Link['O'] .= '&O=de&D='.$HrefDir;
		break;

	case 'ph':
		$Link['O'] .= '&O=ph&D='.$HrefDir;
		break;

	case 'da':
		$Link['O'] .= '&O=da&D='.$HrefDir;
		break;

	case 'use':
		$Link['O'] .= '&O=use&D='.$HrefDir;
		break;

	case 'id':
	default:
		$Link['O'] .= '&O=id&D='.$HrefDir;
		break;
	}
	// calculationg offset
	todefnum('ImgOffs');
	todefnum('lpp', 20);

	if ($ImgOffs < 0) {
		$ImgOffs= 0;
	}

	// Prev/Next switch
	$Link['P'] = 'ImgOffs='.($ImgOffs - $lpp).$Link['S'].$Link['O'].'&v='.$v;
	$Link['N'] = 'ImgOffs='.($ImgOffs + $lpp).$Link['S'].$Link['O'].'&v='.$v;

	$Link['S'] .= '&ImgOffs='.$ImgOffs.'&v='.$v;
	$Link['SO'] = $Link['S'].$Link['O'];

	return $Link;
}

function handleRemoteImg ($cDescription, $cPhotographer, $cPlace, $cDate, $cURL, $Id=0)
{
    include_once('Yahc.class.php');
    $data = new Yahc($cURL, 'CAMPWARE');
    $data->request_protocol = 'HTTP/1.0';
    $data->request_method = 'GET';
    if ($data->connect()) {
        // URL OK
        #echo "connect<br>";
        $data->send_request();
        $data->get_response();
            $hrows = explode ("\r\n", $data->response_HEADER);
        foreach ($hrows as $row) {
            if (preg_match('/Content-Type:/', $row)) {
                $ctype = trim(substr($row, strlen('Content-Type:')));
            }
        }
        #echo "ctype $ctype";

        if (preg_match('/image/', $ctype)) {
            // content-type = image
            if ($Id) {
                $query = "UPDATE Images
                          SET Description='$cDescription', Photographer='$cPhotographer', Place='$cPlace', Date='$cDate', ContentType='$ctype', Location='remote', URL='$cURL'
                          WHERE Id=$Id
                          LIMIT 1";
                query($query);    
                $currId = $Id;
            } else {
                $query = "INSERT INTO Images
                          (Description, Photographer, Place, Date, ContentType, Location, URL)
                           VALUES
                          ('$cDescription', '$cPhotographer', '$cPlace', '$cDate', '$ctype', 'remote', '$cURL')";
                query($query);
                $currId = mysql_insert_id();
            }

            if (_IMAGEMAGICK_) {
                $tmpname =_TMP_DIR_.'img'.md5(rand());
                if ($tmphandle = fopen($tmpname, 'w')) {
                    fwrite($tmphandle, $data->response_HTML);
                    fclose($tmphandle);
                    $cmd = _TUMB_CMD_.' '.$tmpname.' '.$_SERVER[DOCUMENT_ROOT]._TUMB_PREFIX_.$currId;
                    system($cmd);
                    unlink($tmpname);
                } else {
                    return getGS('Cannot create <B>$1</B>', $tmpname);
                }
            }
        } else {
            // wrong URL
            return getGS('URL <B>$1</B> have wrong content type <B>$2</B>', $cURL, $ctype);
        }
    } else {
        // no connection
        return getGS('Unable to read image from <B>$1</B>', $cURL);
    }
}

function handleLocalImage($cImageTemp, $cDescription, $cPhotographer, $cPlace, $cDate, $cURL, $Id=0)
{
 	if ($Id) {
        $query = "UPDATE Images
                  SET Description='$cDescription', Photographer='$cPhotographer', Place='$cPlace', Date='$cDate', ContentType='$ctype', Location='local', URL=''
                  WHERE Id=$Id
                  LIMIT 1";
        query($query); 
        $currId = $Id;
    } else {
        $query = "INSERT INTO Images
                  (Description, Photographer, Place, Date, ContentType, Location)
                  VALUES
                  ('$cDescription', '$cPhotographer', '$cPlace', '$cDate', '$cImageType', 'local')";
        query($query);
        $currId = mysql_insert_id();
    }

    $target = $_SERVER[DOCUMENT_ROOT]._IMG_PREFIX_.$currId;
    $tumb   = $_SERVER[DOCUMENT_ROOT]._TUMB_PREFIX_.$currId;

    if (!$Id) {
        if (!move_uploaded_file ($cImageTemp, $target)) {
             return getGS('Unable to move Image to <B>$1</B>', $target);
        }

        if (_IMAGEMAGICK_) {
            $cmd = _TUMB_CMD_.' '.$target.' '.$tumb;
            #echo $cmd;
            system($cmd);
        }
    }
}
?>
