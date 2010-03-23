<?php
if ($f_order_direction == 1) {
    $arrowImage = "<img align=\"absmiddle\" src=\"".$Campsite["ADMIN_IMAGE_BASE_URL"]."/search_order_direction_up.png\" border=\"0\" />";
} else {
    $arrowImage = "<img align=\"absmiddle\" src=\"".$Campsite["ADMIN_IMAGE_BASE_URL"]."/search_order_direction_down.png\" border=\"0\" />";
}
?>
<script type="text/javascript" src="<?php echo $Campsite["WEBSITE_URL"]; ?>/javascript/domTT/domLib.js"></script>
<script type="text/javascript" src="<?php echo $Campsite["WEBSITE_URL"]; ?>/javascript/domTT/domTT.js"></script>
<script type="text/javascript">
    function ClipList_reOrder(formName, orderBy, orderDirection)
    {
        document.forms[formName].elements['f_order_by'].value = orderBy;
        document.forms[formName].elements['f_order_direction'].value = orderDirection;
        return document.forms[formName].submit();
    } // fn ClipList_reOrder

    var domTT_styleClass = 'domTTOverlib';
</script>
<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/campsite-checkbox.js"></script>
<table class="table_actions">
<tr>
    <td align="right">
        <input type="button" class="button" value="<?php putGS("Select All"); ?>" onclick="checkAll(<?php p($clipCount); ?>, 'rw_');">
        <input type="button" class="button" value="<?php putGS("Select None"); ?>" onclick="uncheckAll(<?php p($clipCount); ?>, 'rw_');">
    </td>
</tr>
</table>
<form method="POST" name="audioclip_list" action="do_link.php">
<input type="hidden" name="f_language_id" value="<?php p($f_language_id); ?>" />
<input type="hidden" name="f_language_selected" value="<?php p($f_language_selected); ?>" />
<input type="hidden" name="f_article_number" value="<?php p($f_article_number); ?>" />
<table border="0" cellspacing="1" cellpadding="6" class="table_list" width="100%">
<tr class="table_list_header">
    <?php if ($articleObj->userCanModify($g_user)) { ?>
    <td align="center" valign="top" style="padding: 3px;"></td>
    <?php } ?>
    <?php $formName = ($f_audio_search_mode == 'search') ? 'search' : 'browse'; ?>
    <td align="left" valign="top">
        <a href="#" onclick="ClipList_reOrder('<?php p($formName); ?>', 'dc:title', '<?php p($orderDirections['dc:title']); ?>');"><?php putGS("Title"); ?></a>
        <?php if ($f_order_by == 'dc:title') echo $arrowImage; ?>
    </td>
    <td align="left" valign="top">
        <a href="#" onclick="ClipList_reOrder('<?php p($formName); ?>', 'dc:creator', '<?php p($orderDirections['dc:creator']); ?>');"><?php putGS("Creator"); ?></a>
        <?php if ($f_order_by == 'dc:creator') echo $arrowImage; ?>
    </td>
    <td align="left" valign="top">
        <a href="#" onclick="ClipList_reOrder('<?php p($formName); ?>', 'dcterms:extent', '<?php p($orderDirections['dcterms:extent']); ?>');"><?php putGS("Duration"); ?></a>
        <?php if ($f_order_by == 'dcterms:extent') echo $arrowImage; ?>
    </td>
</tr>

<?php
$color = 0;
$counter = 0;
foreach ($clips as $clip) {
    $toolTipCaption = '';
    $toolTipContent = '';
    $aClipMetaTags = $clip->getAvailableMetaTags();
    foreach ($aClipMetaTags as $metaTag) {
        list($nameSpace, $localPart) = explode(':', strtolower($metaTag));
        if ($localPart == 'title') {
            $toolTipCaption = '<strong>'.$metatagLabel[$metaTag] . ': ' . $clip->getMetatagValue($localPart) . '</strong><br />';
        } else {
            $toolTipContent .= $metatagLabel[$metaTag] . ': ' . $clip->getMetatagValue($localPart) . '<br />';
        }
    }
    if ($color == 1) {
        $color = 0;
        $rowClass = 'list_row_even';
    } else {
        $color = 1;
        $rowClass = 'list_row_odd';
    }
?>
    <script>
        default_class[<?php p($counter); ?>] = "<?php p($rowClass); ?>";
    </script>
    <tr id="rw_<?php p($counter); ?>" class="<?php p($rowClass); ?>" onmouseover="setPointer(this, <?php p($counter); ?>, 'over'); domTT_activate(this, event, 'caption', '<?php p(addslashes($toolTipCaption)); ?>', 'content', '<?php p(addslashes($toolTipContent)); ?>', 'trail', true, 'delay', 300);" onmouseout="setPointer(this, <?php p($counter); ?>, 'out'); domTT_close(this);">
    <?php
    if ($articleObj->userCanModify($g_user)) {
    ?>
        <td align="center">
            <input type="checkbox" value="<?php p($clip->getGunId()); ?>" name="f_audioclip_code[]" id="checkbox_<?php p($counter); ?>" class="input_checkbox" onclick="checkboxClick(this, <?php p($counter); ?>, 'rw_');" />
        </td>
    <?php
    } else {
    ?>
        <td align="center">&nbsp;</td>
    <?php
    }
    ?>
        <td>
            <a href="<?php echo camp_html_article_url($articleObj, $f_language_id, "audioclips/edit.php", camp_html_article_url($articleObj, $f_language_id, "audioclips/popup.php"))
            .'&f_action=edit&f_audioclip_id='.$clip->getGunId(); ?>" >
            <?php echo htmlspecialchars($clip->getMetatagValue('title')); ?>
            </a>
        </td>
        <td style="padding-left: 5px;">
            <a href="<?php echo camp_html_article_url($articleObj, $f_language_id, "audioclips/edit.php", camp_html_article_url($articleObj, $f_language_id, "audioclips/popup.php"))
            .'&f_action=edit&f_audioclip_id='.$clip->getGunId(); ?>" >
            <?php echo htmlspecialchars($clip->getMetatagValue('creator')); ?></a>
        </td>
        <td style="padding-left: 5px;">
        <?php echo htmlspecialchars(camp_time_format($clip->getMetatagValue('extent'))); ?>&nbsp;
        </td>
    </tr>
<?php
    $counter++;
} // foreach
?>

<tr>
    <td colspan="2" nowrap>
    <?php putGS('$1 audioclips found', $clipCount); ?>
    </td>
    <td colspan="2" align="right">
        <input type="button" class="button" onclick="if (validateCheckboxes('audioclip_list', 'f_audioclip_code[]', 1, '*', '<?php putGS("You must select at least one audioclip to attach."); ?>')) { this.form.submit(); }" value="Attach" />
    </td>
</tr>
</table>
</form>

<table class="action_buttons">
<tr>
    <td>
    <?php echo $pager->render(); ?>
    </td>
</tr>
</table>
