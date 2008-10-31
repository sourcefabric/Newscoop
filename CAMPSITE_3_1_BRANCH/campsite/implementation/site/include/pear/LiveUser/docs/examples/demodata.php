<?php
/**
 * This script will populate the database with the
 * necessary data to run the example.
 *
 *
 * Syntax:
 * DefineGenerator [options]
 * ...where [options] can be:
 * -h --help : Shows this list of options
 *
 * -d --dsn (required): Defines the PEAR::DB DSN to connect to the database.
 * Example: --dsn=mysql://user:passwd@hostname/databasename
 * or -d "mysql://user:passwd@hostname/databasename"
 *
 * -c --create (optional): Defines if the database needs to be created or not.
 * Example: --create=1 or -c "1"
 *
 * -f --file (required): input file containing the structure and
 *                       data in MDB2_Schema format.
 * Example: --file=/path/to/output/file.xml
 *
 * Example usage: php demodata.php -d mysql://root:@localhost/liveuser_test_example5 -f
 * example5/demodata.xml
 *
 * Alternativly you can also call the script from the web using GET
 * demodata.php?dsn=mysql://root:@localhost/liveuser_test_example5&file=example5/demodata.xml&create=1
 *
 * PHP version 4 and 5
 *
 * LICENSE: This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston,
 * MA  02111-1307  USA
 *
 *
 * @category  authentication
 * @package   LiveUser
 * @author    Lukas Smith <smith@pooteeweet.org>
 * @author    Arnaud Limbourg <arnaud@limbourg.com>
 * @copyright 2002-2006 Markus Wolff
 * @license   http://www.gnu.org/licenses/lgpl.txt
 * @version   CVS: $Id: demodata.php,v 1.16 2006/05/23 14:32:59 lsmith Exp $
 * @link      http://pear.php.net/LiveUser
 */

require_once 'MDB2/Schema.php';

$dsn = $file = '';
$create = false;

if (array_key_exists('REQUEST_METHOD', $_SERVER) && $_SERVER['REQUEST_METHOD'] == 'GET') {
    echo '<pre>';
    if (array_key_exists('help', $_GET)) {
        printHelp();
    }
    if (array_key_exists('file', $_GET)) {
        $file = $_GET['file'];
    }
    if (array_key_exists('dsn', $_GET)) {
        $dsn = $_GET['dsn'];
    }
    if (array_key_exists('create', $_GET)) {
        $create = (bool)$_GET['create'];
    }
} else {
    require_once 'Console/Getopt.php';
    $argv = Console_Getopt::readPHPArgv();

    $shortoptions = "h?d:f:c:";
    $longoptions = array('file=', 'dsn=', 'create=');

    $con = new Console_Getopt;
    $args = $con->readPHPArgv();
    array_shift($args);
    $options = $con->getopt($args, $shortoptions, $longoptions);

    if (PEAR::isError($options)) {
        printHelp($options);
    }

    $options = $options[0];
    foreach ($options as $opt) {
        switch ($opt[0]) {
        case 'c':
        case '--create':
            $create = (bool)$opt[1];
            break;
        case 'd':
        case '--dsn':
            $dsn = $opt[1];
            break;
        case 'f':
        case '--file':
            $file = $opt[1];
            break;
        case 'h':
        case '--help':
            printHelp();
            break;
        }
    }
}

/******************************************************************
Begin sanity checks on arguments
******************************************************************/
if ($dsn == '' || $file == '') {
    printHelp();
}

if (!file_exists($file)) {
    print "The file $file does not exist\n";
    exit();
}
/******************************************************************
End sanity checks on arguments
******************************************************************/

print "\n";

$options = array(
#    'portability' => (MDB2_PORTABILITY_ALL ^ MDB2_PORTABILITY_EMPTY_TO_NULL),
#   'seqcol_name' = >'id', // uncomment this line if you want to use DB as the backend
);
$dsn = MDB2::parseDSN($dsn);
$database = $dsn['database'];
unset($dsn['database']);
$manager =& MDB2_Schema::factory($dsn, $options);

if (PEAR::isError($manager)) {
   print "I could not connect to the database\n";
   print "  " . $manager->getMessage()  . "\n";
   print "  " . $manager->getUserInfo() . "\n";
   exit();
}

$variables = array(
    'database' => $database,
    'create' => (int)$create,
);
$res = $manager->updateDatabase($file, false, $variables);

if (PEAR::isError($res)) {
    print "I could not populate the database, see error below\n";
    print "  " . $res->getMessage()  . "\n";
    print "  " . $res->getUserInfo() . "\n";
} else {
    print "Database populated successfully\n";
}

/**
 * printHelp()
 *
 * @return void
 * @desc Prints out a list of commandline options
 */
function printHelp()
{
echo ('
Syntax:
DefineGenerator [options]

...where [options] can be:
-h --help : Shows this list of options

-d --dsn (required) : Defines the PEAR::DB DSN to connect to the database.
Example: --dsn=mysql://user:passwd@hostname/databasename

-c --create (optional): Defines if the database needs to be created or not.
Example: --create=1 or -c "1"

-f --file (required) : input file containing the structure and
data in MDB2_Schema format. Example: --file=/path/to/output/file.xml

Example usage: Make sure the database exists beforehand

php demodata.php -d mysql://root:@localhost/liveuser_test_exampleX -f exampleX/demodata.xml

Alternativly you can also call the script from the web using GET

demodata.php?dsn=mysql://root:@localhost/liveuser_test_exampleX&file=exampleX/demodata.xml&create=1
');
exit;
}
?>
