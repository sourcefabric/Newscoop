<?php
/**
 * Transform data
 */
class DataTransformer {

    /**
     * Truncates string at first non-word character and adds three dots.
     *
     * @param  string $str String to truncate
     * @param  int $len Length at which point to truncate
     *
     * @return string      Truncated string
     */
    public static function truncate($str, $len) {
        $tail = max(0, $len-10);
        $trunk = substr($str, 0, $tail);
        $trunk .= strrev(preg_replace('~^..+?[\s,:]\b|^...~', '...', strrev(substr($str, $tail, $len-$tail))));
        return $trunk;
    }
}

?>