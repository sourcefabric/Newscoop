<?php
$color = 0;
$counter = 0;
foreach ($clips as $clip) {
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
    <TR id="rw_<?php p($counter); ?>" class="<?php p($rowClass); ?>" onmouseover="setPointer(this, <?php p($counter); ?>, 'over');" onmouseout="setPointer(this, <?php p($counter); ?>, 'out');">
    <?php
    if ($articleObj->userCanModify($g_user)) {
    ?>
        <TD align="center">
            <INPUT type="checkbox" value="<?php p($clip->getGunId()); ?>" name="f_audioclip_code[]" id="checkbox_<?php p($counter); ?>" class="input_checkbox" onclick="checkboxClick(this, <?php p($counter); ?>);" />
        </TD>
    <?php
    } else {
    ?>
        <TD align="center">&nbsp;</TD>
    <?php
    }
    ?>
        <TD>
            <A href="<?php echo camp_html_article_url($articleObj, $f_language_id, "audioclips/edit.php", camp_html_article_url($articleObj, $f_language_id, "audioclips/popup.php"))
            .'&f_action=edit&f_audioclip_id='.$clip->getGunId(); ?>">
            <?php echo htmlspecialchars($clip->getMetatagValue('title')); ?>
            </A>
        </TD>
        <TD style="padding-left: 5px;">
            <A href="<?php echo camp_html_article_url($articleObj, $f_language_id, "audioclips/edit.php", camp_html_article_url($articleObj, $f_language_id, "audioclips/popup.php"))
            .'&f_action=edit&f_audioclip_id='.$clip->getGunId(); ?>"><?php echo htmlspecialchars($clip->getMetatagValue('creator')); ?></A>
        </TD>
        <TD style="padding-left: 5px;">
        <?php echo htmlspecialchars(camp_time_format($clip->getMetatagValue('extent'))); ?>&nbsp;
        </TD>
    </TR>
<?php
    $counter++;
} // foreach
?>
