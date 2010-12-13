<table style="width: 100%; border: 1px solid #EEEEEE;">
<?php
$map_id = Geo_Map::GetArticleMapId($articleObj);
$locations = array();
if ($map_id)
{
    $locations = Geo_Map::GetLocationsByArticle($articleObj);
}
$detachUrl = "/$ADMIN/articles/locations/do_unlink.php?f_publication_id=$f_publication_id&f_issue_number=$f_issue_number&f_section_number=$f_section_number&f_article_number=$f_article_number&f_language_selected=$f_language_selected&f_language_id=$f_language_id&".SecurityToken::URLParameter();
?>
<tr>
	<td>
        <table cellpadding="3" cellspacing="0" style="width: 100%; background-color: #eee;">
			<tr>
                <td style="text-align: left">
                    <strong>Locations</strong>
                </td>
				<td style="text-align: right">
                    <table cellpadding="2" cellspacing="0">
                        <tr>
                            <?php if (($f_edit_mode == "edit") && $g_user->hasPermission('ChangeArticle')) {  ?>
                                <td><img src="<?php p($Campsite["ADMIN_IMAGE_BASE_URL"]);?>/add.png" /></td>
                                <td><a href="javascript: void(0);" onclick="window.open('<?php echo camp_html_article_url($articleObj, $f_language_id, "locations/popup.php"); ?>', 'autopublish_window', 'scrollbars=yes, resizable=yes, menubar=no, toolbar=no, width=1050, height=600, top=200, left=200');">Edit</a></td>
                            <?php } ?>
                        </tr>
                    </table>
                </td>
            </tr>
<?php if ($map_id) { ?>
			<tr>
	             <td align="center" colspan="2">
                    <table><tr>
                    <td>
                        <a href="#" onclick="window.open('<?php echo camp_html_article_url($articleObj, $f_language_id, "locations/preview.php"); ?>', 'map_preview_window', 'scrollbars=yes, resizable=yes, menubar=no, toolbar=no, width=1050, height=600, top=200, left=200');"><img src="<?php echo $Campsite['ADMIN_STYLE_URL']; ?>/images/world_map.png" /></a>
                    </td>
                    <?php if (($f_edit_mode == "edit") && $g_user->hasPermission('ChangeArticle')) {  ?>
                        <td align="left">
                            <a href="<?php p($detachUrl); ?>" onclick="return confirm('<?php putGS("Are you sure you want to remove the map from the article?"); ?>'); return false;"><img src="<?php p($Campsite["ADMIN_IMAGE_BASE_URL"]);?>/unlink.png" border="0"></a>
                    <?php } ?>
                    </td>
                    </tr></table>
                </td>
			</tr>
<?php } ?>
        </table>
    </td>
</tr>
<?php
//$locations = Geo_Map::GetLocationsByArticle($articleObj);
if (!empty($locations)) { ?>
<tr>
    <td>
        <ol class="points">
            <?php foreach ($locations as $location) { ?>
            <li><?php
                $poi_display = true;
                if (0 == $location["display"]) {$poi_display = false;}

                if (!$poi_display) {echo "<i>";}
                echo $location["name"];
                if (!$poi_display) {echo "</i>";}
            ?></li>
            <?php } ?>
        </ol>
    </td>
</tr>
<?php } ?>
</table>
