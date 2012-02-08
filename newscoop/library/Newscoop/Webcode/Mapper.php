<?php

namespace Newscoop\Webcode;
use Newscoop\Webcode;

class Mapper implements Webcode
{
	static $map = array(
	0 => 'A',
	1 => 'B',
	2 => 'C',
	3 => 'D',
	4 => 'E',
	5 => 'F',
	6 => 'G',
	7 => 'H',
	8 => 'I',
	9 => 'J',
	10 => 'K',
	11 => 'L',
	12 => 'M',
	13 => 'N',
	14 => 'O',
	15 => 'P',
	16 => 'Q',
	17 => 'R',
	18 => 'S',
	19 => 'T',
	20 => 'U',
	21 => 'V',
	22 => 'W',
	23 => 'X',
	24 => 'Y',
	25 => 'Z'
	);

	public static function base26($article_no) {

		$base26Array = array();
		$num = $article_no;
		$index = 0;
		while ($num != 0) {
			$base26Array[$index] = $num % 26;
            $num = floor($num / 26);
            $index ++;
		}
        return array_reverse($base26Array);
	}

	public static function base10($article_no) {
        $base10 = 0;
        foreach($article_no as $key=>$value) {
            $base10 += $value * pow(26, $key);
        }
        return $base10;
	}

    public static function encode($article_no)
    {

    	if (!is_numeric($article_no)) {
    		return FALSE;
    	}

    	$cleanCode = self::base26($article_no);
    	$letterCode = '';
        foreach($cleanCode as $no) {
        	$letterCode .= self::$map[$no];
        }
        $returnCode = $letterCode;
        for ($i = 0; $i < (5 - strlen($letterCode)); $i ++) {
        	$returnCode = self::$map[0] . $returnCode;
        }
        return strtolower('+'.$returnCode);
    }

    public static function decode($webcode)
    {
        $webcode = preg_replace('/^\+/', '', $webcode);
    	$decodeMap = array_flip(self::$map);
        $article_no = array();
        $webcode = str_split(ltrim($webcode, 'aA'));
        for ($i = 0; $i < count($webcode); $i ++) {
        	if (array_key_exists(strtoupper($webcode[$i]), $decodeMap)) {
        		$article_no[]= $decodeMap[strtoupper($webcode[$i])];
        	}
        }
        return self::base10(array_reverse($article_no));
    }

    public static function isWebcode($string) {
        if (preg_match('/^[\+@][a-zA-Z]{5,6}$/', $string) == 1) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
}







