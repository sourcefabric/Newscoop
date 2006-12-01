<?php
$color = 0;
foreach ($clips as $clip) {
    if ($color == 1) {
        $color = 0;
        $class = 'list_row_even';
    } else {
        $color = 1;
        $class = 'list_row_odd';
    }
?>
    <TR class="<?php echo $class; ?>">
    <?php
    if ($articleObj->userCanModify($g_user)) {
    ?>
        <FORM method="POST" action="do_link.php">
        <INPUT type="hidden" name="f_language_id" value="<?php p($f_language_id); ?>" />
        <INPUT type="hidden" name="f_language_selected" value="<?php p($f_language_selected); ?>" />
        <INPUT type="hidden" name="f_article_number" value="<?php p($f_article_number); ?>" />
        <INPUT type="hidden" name="f_audioclip_id" value="<?php echo $clip->getGunId(); ?>" />
        <TD align="center">
            <INPUT type="checkbox" value="143_1" name="f_article_code[]" id="checkbox_0" class="input_checkbox" onclick="checkboxClick(this, 0);" />
        </TD>
        </FORM>
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
} // foreach
?>
