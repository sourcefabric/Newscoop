<?php
/**
 * @package Campsite
 *
 * @author Petr Jasek <petr.jasek@sourcefabric.org>
 * @copyright 2010 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.sourcefabric.org
 */

require_once dirname(__FILE__) . '/../bootstrap.php';

require_once dirname(__FILE__) . '/LocationTest.php';
require_once dirname(__FILE__) . '/MapTest.php';
require_once dirname(__FILE__) . '/MapLocationTest.php';
require_once dirname(__FILE__) . '/MapLocationContentTest.php';
require_once dirname(__FILE__) . '/MapLocationLanguageTest.php';
require_once dirname(__FILE__) . '/MultimediaTest.php';
require_once dirname(__FILE__) . '/PreferencesTest.php';
require_once dirname(__FILE__) . '/NamesTest.php';

class Extension_AllTests
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Geo');

        $suite->addTestSuite('Geo_LocationTest');
        $suite->addTestSuite('Geo_MapTest');
        $suite->addTestSuite('Geo_MapLocationTest');
        $suite->addTestSuite('Geo_MapLocationContentTest');
        $suite->addTestSuite('Geo_MapLocationLanguageTest');
        $suite->addTestSuite('Geo_MultimediaTest');
        $suite->addTestSuite('Geo_PreferencesTest');
        $suite->addTestSuite('Geo_NamesTest');

        return $suite;
    }
}
