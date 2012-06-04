<?php
$cs_dir = dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))));
$GLOBALS['g_campsiteDir'] = $cs_dir;

require_once($GLOBALS['g_campsiteDir'].DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'campsite_constants.php');
require_once(CS_PATH_SITE.DIR_SEP.'include'.DIR_SEP.'campsite_init.php');
require_once(CS_PATH_SITE.DIR_SEP.'db_connect.php');

function assure_article_authors_4_0()
{
	global $g_ado_db;

    {
        try {
            $g_ado_db->Execute("ALTER TABLE `ArticleAuthors` ADD COLUMN `order` int(2) unsigned DEFAULT NULL");
        }
        catch (\Exception $exc) {
        }
    }

} // fn assure_article_authors_4_0

assure_article_authors_4_0();

?>
