<?php
$ImgPrefix = '/images/cms-image-';

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
	global $S, $de, $ph, $pl, $da, $O, $ImgOffs, $lpp, $D;



	// regarding parameters from search form or link //////////////////////
	todef('S');
	todef('de');
	todef('ph');
	todef('pl');
	todef('da');

	if ($S && (isset($de) || isset($ph) || isset($pl) || isset($da))) {

		if (isset($de)) {
			$Link['S']   .= "&S=1&de=".urlencode($de);
		}
		if (isset($ph)) {
			$Link['S']   .= "&S=1&ph=".urlencode($ph);
		}
		if (isset($pl)) {
			$Link['S']   .= "&S=1&pl=".urlencode($pl);
		}
		if (isset($da)) {
			$Link['S']   .= "&S=1&da=".urlencode($da);
		}

	}
	////////////////////////////////////////////////////////////////////

	// build the order statement ///////////////////////////////////////
	todef('O');
	todef('D');

	if ($D == 'DESC') {
		$HrefDir  = "DESC";
	} else {
		$HrefDir  = "ASC";
	}

	switch ($O) {
	case 'de':
		$Link['O'] .= '&O=de&D='.$HrefDir;
		break;

	case 'ph':
		$Link['O'] .= '&O=ph&D='.$HrefDir;
		break;

	case 'pl':
		$Link['O'] .= '&O=pl&D='.$HrefDir;
		break;

	case 'da':
		$Link['O'] .= '&O=da&D='.$HrefDir;
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
	$Link['P'] = 'ImgOffs='.($ImgOffs - $lpp).$Link['S'].$Link['O'];
	$Link['N'] = 'ImgOffs='.($ImgOffs + $lpp).$Link['S'].$Link['O'];

	$Link['S'] .= '&ImgOffs='.$ImgOffs;
	$Link['SO'] = $Link['S'].$Link['O'];

	return $Link;
}
?>
