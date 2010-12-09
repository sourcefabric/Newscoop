<table style="width: 100%; border: 1px solid #EEEEEE;">
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
                            <td><img src="<?php p($Campsite["ADMIN_IMAGE_BASE_URL"]);?>/add.png" /></td>
                            <td><a href="javascript: void(0);" onclick="window.open('<?php echo camp_html_article_url($articleObj, $f_language_id, "locations/popup.php"); ?>', 'autopublish_window', 'scrollbars=yes, resizable=yes, menubar=no, toolbar=no, width=1050, height=600, top=200, left=200');">Edit</a></td>
                        </tr>
                    </table>
                </td>
            </tr>
			<tr>
	             <td align="center" colspan="2"><a href="#" onclick="window.open('<?php echo camp_html_article_url($articleObj, $f_language_id, "locations/preview.php"); ?>', 'map_preview_window', 'scrollbars=yes, resizable=yes, menubar=no, toolbar=no, width=1050, height=600, top=200, left=200');">preview</a>&nbsp;<a href="#" onClick="return false;">x</a></td>
			</tr>
        </table>
    </td>
</tr>
<?php
$locations = Geo_Map::GetLocationsByArticle($articleObj);
if (!empty($locations)) { ?>
<tr>
    <td>
        <ol class="points">
            <?php foreach ($locations as $location) { ?>
            <li><?php echo $location; ?></li>
            <?php } ?>
        </ol>
    </td>
</tr>
<?php } ?>
</table>
