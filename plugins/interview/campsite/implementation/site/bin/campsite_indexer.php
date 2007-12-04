#!/usr/bin/php
<?php
/**
 * @package Campsite
 *
 * @author Holman Romero <holman.romero@gmail.com>
 * @copyright 2007 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @version $Revision$
 * @link http://www.campware.org
 */


// Global keywords hash
$GLOBALS['kwd_hash'] = array();

// Max length for a keyword
define('MAX_KWD', 69);


/**
 * Keyword functions
 */


function make_seed()
{
    list($usec, $sec) = explode(' ', microtime());
    return (float) $sec + ((float) $usec * 100000);
} // fn make_seed


function make_hash()
{
    mt_srand(make_seed());
    $hash = mt_rand(0, 255);
    return $hash;
} // fn make_hash


function init_hash()
{
    global $kwd_hash;

    for($i = 0; $i < 256; $i++) {
        $kwd_hash[$i] = 0;
    }
} // fn init_hash


function add_kwd($p_kwd, $p_length)
{
    global $kwd_hash;

    $h = make_hash();

    if (strcmp($p_kwd, $kwd_hash[$h]) == 0) {
        return;
    }

    $kwd_hash[$h] = substr($p_kwd, 0, $p_length);
} // fn add_kwd


function del_kwd_list()
{
    unset($GLOBALS['kwd_hash']);
    $GLOBALS['kwd_hash'] = array();
} // fn del_kwd_list


function parse_kwd($p_kwd)
{
    $t = array(
               0, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 1, 1, 0, 1, 1, 1, 1, 1, 1,
               1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 1, 1, 0, 0, 0,
               0, 0, 0, 0, 0, 1, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0,
               0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1,
               1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 1, 0, 1, 1, 1,
               1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1,
               1, 1, 1, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1,
               1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1,
               1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1,
               1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1,
               1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1,
               1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1,
               1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1
               );

    if (!empty($p_kwd)) {
        $kwd_l = strlen($p_kwd);
        $q = null;
        $x = 0;
        $l = 0;
        for ($q = $p_kwd; $x < $kwd_l;) {
            $w_l = 0;
            while ($x < $kwd_l && $t[ord(substr($q, $x, 1))]) {
                $x++;
                $w_l++;
            }

            if ($w_l > 1) {
                $word = substr($q, $l, $w_l);
                add_kwd($word, mymin($w_l, MAX_KWD));
            } else {
                $l = $x;
                while ($l < $kwd_l && !$t[ord(substr($q, $l, 1))]) {
                    $l++;
                }
                $x = $l;
            }
        }
    }
} // fn parse_kwd



/**
 * Gather functions
 */


function build_kwd_list($p_article)
{
    parse_kwd($p_article['Keywords']);
    parse_kwd($p_article['Name']);

    if (empty($p_article['Type'])) {
        return;
    }

    $sql_query = 'SELECT * FROM X' . $p_article['Type']
        . ' WHERE NrArticle = ' . $p_article['Number']
        . ' AND IdLanguage = ' . $p_article['IdLanguage'];
    $sql_result = mysql_query($sql_query);
    if (!$sql_result) {
        print('Error getting article: ' . mysql_error());
        exit(1);
    }

    $row = mysql_fetch_row($sql_result);

    if (!empty($row)) {
        $nr_of_fields = mysql_num_fields($sql_result);
        for ($i = 0; $i < $nr_of_fields; $i++) {
            $field = mysql_fetch_field($sql_result, $i);
            if (substr($field->name, 0, 1) == 'F') {
                if ($row[$i]) {
                    parse_kwd($row[$i]);
                }
            }
        }
    }

    mysql_free_result($sql_result);
} // fn build_kwd_list


function gather($p_conf_dir)
{
    // connects to the database server
    db_connect($p_conf_dir);

    // selects articles not yet indexed
    $sql_query = 'SELECT IdPublication, NrIssue, NrSection, Number, '
        .  'IdLanguage, Published, Type, Keywords, Name '
        .  "FROM Articles WHERE IsIndexed = 'N' ORDER BY Number";
    $sql_result = mysql_query($sql_query);
    if (!$sql_result) {
        print('Error selecting articles not yet indexed: ' . mysql_error());
        exit(1);
    }

    $nr_art = 0;
    $nr_new = 0;
    $nr_word = 0;

    while($row = mysql_fetch_array($sql_result, MYSQL_ASSOC)) {
        $article['IdPublication'] = ($row['IdPublication']) ? (int)$row['IdPublication'] : 0;
        $article['NrIssue'] = ($row['NrIssue']) ? (int)$row['NrIssue'] : 0;
        $article['NrSection'] = ($row['NrSection']) ? (int)$row['NrSection'] : 0;
        $article['Number'] = ($row['Number']) ? (int)$row['Number'] : 0;
        $article['IdLanguage'] = ($row['IdLanguage']) ? (int)$row['IdLanguage'] : 0;
        $article['Published'] = ($row['Published'] == 'Y') ? true : false;
        $article['Type'] = ($row['Type']) ? $row['Type'] : '';
        $article['Keywords'] = ($row['Keywords']) ? $row['Keywords'] : '';
        $article['Name'] = ($row['Name']) ? $row['Name'] : '';

        // deletes from index
        $sql_query = 'DELETE FROM ArticleIndex '
            . 'WHERE IdPublication = ' . $article['IdPublication']
            . ' AND IdLanguage = ' . $article['IdLanguage']
            . ' AND NrIssue = ' . $article['NrIssue']
            . ' AND NrSection = ' . $article['NrSection']
            . ' AND NrArticle = ' . $article['Number'];
        if (!mysql_query($sql_query)) {
            print('Error deleting old index: ' . mysql_error());
            exit(1);
        }

        if (!$article['Published']) {
            continue;
        }

        $nr_art++;

        init_hash();
        build_kwd_list($article);

        global $kwd_hash;

        for ($i = 0; $i < 256; $i++) {
            $w = $kwd_hash[$i];

            if (empty($w)) {
                continue;
            }

            $nr_word++;

            $sql_query = 'SELECT Id FROM KeywordIndex '
                . "WHERE Keyword = '" . mysql_real_escape_string($w) ."'";
            $sql_res = mysql_query($sql_query);
            if (!$sql_res) {
                print('Error getting KeywordId: ' . mysql_error());
                exit(1);
            }

            $drow = mysql_fetch_array($sql_res, MYSQL_ASSOC);
            $kwd_id = (!empty($drow) && !empty($drow['Id'])) ? $drow['Id'] : 0;
            mysql_free_result($sql_res);
            if ($kwd_id == 0) {
                if (!mysql_query('LOCK TABLE KeywordIndex WRITE')) {
                    print('Error locking table KeywordIndex: '
                          . mysql_error());
                    exit(1);
                }
                $sql_query = 'SELECT MAX(Id) AS Id FROM KeywordIndex';
                $sql_res = mysql_query($sql_query);
                if (!$sql_res) {
                    print('Error reading the last id: ' . mysql_error());
                    exit(1);
                }

                $drow = mysql_fetch_array($sql_res, MYSQL_ASSOC);
                mysql_free_result($sql_res);
                if (!empty($drow) && !empty($drow['Id'])) {
                    $kwd_id = $drow['Id'] + 1;
                } else {
                    $kwd_id = 1;
                }

                // inserts in keyword list
                $sql_query = 'INSERT INTO KeywordIndex '
                    . "SET Keyword = '".mysql_real_escape_string($w)."', "
                    . 'Id = '.$kwd_id;
                $sql_res = mysql_query($sql_query);
                if (!$sql_res) {
                    print('Error adding keyword: ' . mysql_error());
                    exit(1);
                }

                if (!mysql_query('UNLOCK TABLES')) {
                    print('Error unlocking table KeywordIndex: '
                          . mysql_error());
                    exit(1);
                }

                $nr_new++;
            }

            unset($w);

            // inserts in article index
            $sql_query = 'INSERT IGNORE INTO ArticleIndex '
                . 'SET IdPublication = ' . $article['IdPublication']
                . ', IdLanguage = ' . $article['IdLanguage']
                . ', IdKeyword = ' . $kwd_id
                . ', NrIssue = ' . $article['NrIssue']
                . ', NrSection = ' . $article['NrSection']
                . ', NrArticle = ' . $article['Number'];
            if (!mysql_query($sql_query)) {
                print('Error adding article to index: ' . mysql_error());
                exit(1);
            }
        }

        del_kwd_list();

        unset($article['Name']);
        unset($article['Keywords']);
        unset($article['Type']);

        $sql_query = "UPDATE Articles SET IsIndexed = 'Y' "
            . 'WHERE IdPublication = ' . $article['IdPublication']
            . ' AND NrIssue = ' . $article['NrIssue']
            . ' AND NrSection = ' . $article['NrSection']
            . ' AND Number = ' . $article['Number']
            . ' AND IdLanguage = ' . $article['IdLanguage'];
        if (!mysql_query($sql_query)) {
            print('Error updating article: ' . mysql_error());
            exit(1);
        }

        $m++;

    }

    if ($nr_art > 0 || $nr_word > 0 || $nr_new > 0) {
        print('Campsite: '.$nr_art.' new articles, '
              .$nr_word.' words processed, '
              .$nr_new." of them are new\n");
    }

    mysql_close();

    return 0;
} // fn gather


function db_connect($p_conf_dir)
{
    is_valid_conf_dir($p_conf_dir);

    require_once($p_conf_dir . '/database_conf.php');
    mysql_connect($Campsite['DATABASE_SERVER_ADDRESS'],
                  $Campsite['DATABASE_USER'],
                  $Campsite['DATABASE_PASSWORD']) or
        die('Could not connect: ' . mysql_error());
    mysql_select_db($Campsite['DATABASE_NAME']);
} // fn db_connect


function is_valid_conf_dir($p_conf_dir)
{
    try {
        if (!is_dir($p_conf_dir)) {
            throw new Exception('Invalid configuration directory ' . $p_conf_dir);
        }
        return true;
    } catch (Exception $e) {
        print('Error: ' . $e->getMessage() . "\n");
        exit(1);
    }
} // fn isValidConfDir


function mymin($p_a, $p_b)
{
    return ($p_a < $p_b) ? $p_a : $p_b;
} // fn mymin


function usage()
{
    printf("Usage: campsite_indexer [options]\n"
           ."Options:\n"
           ."\t--conf_dir=[configuration_dir]: Host name of the MySQL Server.\n"
           ."\t--help: Display this information.\n");
} // fn usage


// gets the arguments from command line, if any
if (is_array($GLOBALS['argv']) && !empty($GLOBALS['argv'][1])) {
    $option = explode('=', $GLOBALS['argv'][1]);
    if ($option[0] != '--conf_dir') {
        usage();
        exit(0);
    }

    $conf_dir = (!empty($option[1])) ? rtrim($option[1], '/') : '';
    if (empty($conf_dir)) {
        usage();
        exit(0);
    }
} else {
    $bin_dir = dirname(__FILE__);
    $conf_dir = preg_replace('/bin$/', 'conf', $bin_dir);
}


// runs the script
gather($conf_dir);

?>
