<?php

function base64ToText($text)
{
        $b64s = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_"';
        $r = '';
        $m = 0;
        $a = 0;
        for($n = 0; $n < strlen($text); $n++) {
                $c = strpos($b64s, ($text{$n}));
                if ($c >= 0) {
                        if ($m) {
                                $r .= chr(($c << (8 - $m)) & 255 | $a);
                        }
                        $a = $c >> $m;
                        $m = ($m + 2) % 8;
                }
        }
        return $r;
}

function rc4($key, $text)
{
        $kl = strlen($key);
        $s = array();

        for($i = 0; $i < 256; $i++) {
                $s[$i] = $i;
        }
        $y = 0;
        for($j = 0; $j < 2; $j++) {
                for($x = 0; $x < 256; $x++) {
                        $y = (ord($key[$x % $kl]) + $s[$x] + $y) % 256;
                        $t = $s[$x];
                        $s[$x] = $s[$y];
                        $s[$y] = $t;
                }
        }
        $z = '';
        for($x = 0; $x < strlen($text); $x++) {
                $x2 = $x & 255;
                $y = ($s[$x2] + $y) & 255;
                $t = $s[$x2];
                $s[$x2] = $s[$y];
                $s[$y] = $t;
                $z .= chr((ord($text[$x]) ^ $s[($s[$x2] + $s[$y]) % 256]));
        }
        return $z;
}

?>
