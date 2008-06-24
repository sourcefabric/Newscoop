--TEST--
DB_driver::locale
--INI--
error_reporting = 2047
--SKIPIF--
<?phpi
chdir(dirname(__FILE__)); require_once './skipif.inc';
if (!function_exists('setlocale')) {
    die('skip setlocale is not defined');
}
if (!OS_UNIX) {
    die('skip not on a UNIX-like platform');
}
?>
--FILE--
<?php
require_once './mktable.inc';

if ($dbh->phptype == 'odbc') {
    if ($dbh->dbsyntax == 'odbc') {
        $type = $dbh->phptype;
    } else {
        $type = $dbh->dbsyntax;
    }
} else {
    $type = $dbh->phptype;
}

switch ($type) {
    case 'access':
        $decimal = 'SINGLE';
        break;
    case 'db2':
    case 'ibase':
        $decimal = 'DECIMAL(3,1)';
        break;
    case 'ifx':
        // doing this for ifx to keep certain versions happy
        $decimal = 'DECIMAL(3,1)';
        break;
    case 'msql':
        $decimal = 'REAL';
        break;
    case 'fbsql':
    case 'oci8':
        $decimal = 'DECIMAL(3,1)';
        break;
    default:
        $decimal = 'DECIMAL(3,1)';
}

$dbh->setErrorHandling(PEAR_ERROR_RETURN);
drop_table($dbh, 'localetest');

$res = $dbh->query("CREATE TABLE localetest (a $decimal)");
if (DB::isError($res)) {
    echo 'Unable to create table: '.$res->getMessage()."\n";
}

setlocale(LC_NUMERIC, 'de_DE');

$res = $dbh->query('INSERT INTO localetest (a) VALUES (?)', array(42.2));
if (DB::isError($res)) {
    echo 'Error inserting record: '.$res->getMessage()."\n";
    var_dump($res);
}

setlocale(LC_NUMERIC, 'en_AU');

$res = $dbh->query('INSERT INTO localetest (a) VALUES (?)', array(42.2));
if (DB::isError($res)) {
    echo 'Error inserting record: '.$res->getMessage()."\n";
    var_dump($res);
}

$res = $dbh->query('SELECT * FROM localetest');
if (DB::isError($res)) {
    echo 'Error retrieving count: '.$res->getMessage()."\n";
    var_dump($res);
} else {
    echo 'Got '.$res->numRows()." records.\n";
}

drop_table($dbh, 'localetest');

?>
--EXPECT--
Got 2 records.
