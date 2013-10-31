<?php
$cs_dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$GLOBALS['g_campsiteDir'] = $cs_dir;

require_once($GLOBALS['g_campsiteDir'].DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'campsite_constants.php');
require_once(CS_PATH_SITE.DIR_SEP.'include'.DIR_SEP.'campsite_init.php');
require_once(CS_PATH_SITE.DIR_SEP.'db_connect.php');

function load_geonames_data_3_5()
{
	global $g_ado_db;

    {
        // load geonames
        set_time_limit(0);
        foreach (array('CityNames', 'CityLocations') as $table) {
            $g_ado_db->Execute("TRUNCATE `$table`");
            $g_ado_db->Execute("ALTER TABLE `$table` DISABLE KEYS");
            $csvFile = CS_INSTALL_DIR.DIR_SEP.'sql'.DIR_SEP."$table.csv";
            $csvFile = str_replace("\\", "\\\\", $csvFile);
            $g_ado_db->Execute("LOAD DATA LOCAL INFILE '$csvFile' INTO TABLE $table FIELDS TERMINATED BY ';' ENCLOSED BY '\"'");
            $g_ado_db->Execute("ALTER TABLE `$table` ENABLE KEYS");
        }
    }

} // fn load_geonames_data_3_5

load_geonames_data_3_5();

?>
