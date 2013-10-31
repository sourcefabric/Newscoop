<?php
$translator = \Zend_Registry::get('container')->getService('translator');

// Check permissions
if (!$g_user->hasPermission('plugin_debate_admin')) {
    camp_html_display_error($translator->trans('You do not have the right to manage debates.', array(), 'plugin_debate'));
    exit;
}

$f_debate_nr = Input::Get('f_debate_nr', 'int');
$f_fk_language_id = Input::Get('f_fk_language_id', 'int');

$f_nr_answer = Input::Get('f_nr_answer', 'int');

$debate = new Debate($f_fk_language_id, $f_debate_nr);

$format = '%.2f';

$display[] = $debate;

foreach($debate->getTranslations() as $translation) {
    if ($translation->getLanguageId() != $debate->getLanguageId()) {
        $display[] = $translation;
    }
}

echo camp_html_breadcrumbs(array(
    array($translator->trans('Plugins', array(), 'plugin_debate'), $Campsite['WEBSITE_URL'] . '/admin/plugins/manage.php'),
    array($translator->trans('Debates', array(), 'plugin_debate'), $Campsite['WEBSITE_URL'] . '/admin/debate/index.php'),
    array($translator->trans('Result', array(), 'plugin_debate'), ''),
));
?>

<?php $answers = $debate->getAnswers($f_debate_nr, $f_fk_language_id); ?>

<style type="text/css">
.results
{
	border: 1px solid #ccc;
	margin: 0 30px 30px 30px;
	box-shadow: 0 2px 2px rgba(0, 0, 0, 0.1);
	background-color: #f3f3f3;
}
    .results .item-def
    {
    	float: left;
    	border-right: 1px solid #ccc;
    	height: <?php echo count($answers)*30 + 20 ?>px;
    }
    	.results .item-def .value
		{
			height: <?php echo count($answers)*30 - 10 ?>px;
			padding: 5px 10px;
			border-bottom: 1px solid #ccc;
		}
    .results .item
    {
		width: 50px;
    	float: left;
    	height: <?php echo count($answers)*30 + 20 ?>px;
    	border-right: 1px solid #ccc;
    }
    	.results .item .value
		{
			height: <?php echo count($answers)*30 ?>px;
			border-bottom: 1px solid #ccc;
		}
		.results .item .bottom, .results .item-def .bottom
		{
			height: 20px;
			line-height: 20px;
			text-align: center;
			color: #888;
		}
    	.results .item .division
    	{
    		text-align: center;
		}
    	.results .item .division:nth-child(3n+1)
		{
  			background-color: #fff;
		}
		.results .item .division:nth-child(3n+2)
		{
  			background-color: #eef;
		}
		.results .item .division:nth-child(3n+3) {
  			background-color: #ddf;
		}
</style>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" class="action_buttons" style="padding-top: 5px;">
<TR>
    <TD><A HREF="index.php"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/left_arrow.png" BORDER="0"></A></TD>
    <TD><A HREF="index.php"><B><?php  echo $translator->trans("Debate List", array(), 'plugin_debate'); ?></B></A></TD>
    <TD style="padding-left: 20px;"><A HREF="edit.php" ><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/add.png" BORDER="0"></A></TD>
    <TD><A HREF="edit.php" ><B><?php  echo $translator->trans("Add new Debate", array(), 'plugin_debate'); ?></B></A></TD>
</tr>
</TABLE>
<p>
<?php foreach ($display as $translation) : $color = 0; ?>
    <TABLE BORDER="0" CELLSPACING="1" CELLPADDING="3" class="table_list" style="padding-top: 5px;">
        <TR class="table_list_header">
            <TD ALIGN="LEFT" VALIGN="TOP"><?php  echo $translator->trans("Title", array(), 'plugin_debate'); ?></TD>
            <TD ALIGN="center" VALIGN="TOP"><?php  echo $translator->trans("Votes", array(), 'plugin_debate'); ?></TD>
            <TD ALIGN="center" VALIGN="TOP"><?php  echo $translator->trans("Percentage this language", array(), 'plugin_debate'); ?></TD>
            <TD ALIGN="center" VALIGN="TOP"><?php  echo $translator->trans("Percentage all languages", array(), 'plugin_debate'); ?></TD>
        </TR>
        <tr>
            <th><?php p($translation->getProperty('title')); ?> (<?php p($translation->getLanguageName()); ?>)</th>
            <td align="CENTER"><?php p($translation->getProperty('nr_of_votes')); ?> / <?php p($translation->getProperty('nr_of_votes_overall')); ?></td>
            <td align="LEFT">
                <img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/mainbarlinks.png" width="1" height="9" class="IMG_norm"><img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/mainbar.png" width="<?php p($translation->getProperty('percentage_of_votes_overall')); ?>" height="9" class="IMG_norm"><img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/mainbarrechts.png" width="1" height="9" class="IMG_norm">
                <?php printf($format, $translation->getProperty('percentage_of_votes_overall')); ?>%
            </td>
            <th> </th>
        </tr>
        <?php
        foreach ($translation->getAnswers() as $answer) {
            if ($color) {
                $rowClass = "list_row_even";
            } else {
                $rowClass = "list_row_odd";
            }
            $color = !$color;
            ?>
            <tr class="<?php p($rowClass); ?>" >
              <td width="400"><?php p($answer->getProperty('answer')); ?></td>
              <td width="50" ALIGN="center"><?php p($answer->getProperty('nr_of_votes')); ?></td>
              <td width="200">
                <img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/mainbarlinks.png" width="1" height="9" class="IMG_norm"><img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/mainbar.png" width="<?php p($answer->getProperty('percentage')); ?>" height="9" class="IMG_norm"><img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/mainbarrechts.png" width="1" height="9" class="IMG_norm">
                <?php printf($format, $answer->getProperty('percentage')); ?>%
              </td>
              <td width="200">
                <img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/mainbarlinks.png" width="1" height="9" class="IMG_norm"><img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/mainbar.png" width="<?php p($answer->getProperty('percentage_overall')); ?>" height="9" class="IMG_norm"><img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/mainbarrechts.png" width="1" height="9" class="IMG_norm">
                <?php printf($format, $answer->getProperty('percentage_overall')); ?>%
              </td>
            </tr>
            <?php
        }
    ?>
    </table>
    <p>
<?php endforeach ?>

<h3 style="margin-left:30px">
    <?php echo ucfirst($debate->getProperty('results_time_unit')) ?> results:
</h3>

<div class="results">

	<div class="item-def">
		<div class="value">
        <?php foreach ($answers as $answer) : ?>
        	<div><?php echo $answer->getProperty('nr_answer') ?>. <?php echo $answer->getProperty('answer') ?></div>
        <?php endforeach ?>
    	</div>
    	<div class="bottom">
    	<?php
    	    switch($debate->getProperty('results_time_unit'))
    	    {
    	        case 'daily' : echo $translator->trans('Day', array(), 'plugin_debate'); $dformat = '%e.%m.%y'; break;
    	        case 'weekly' : echo $translator->trans('Week', array(), 'plugin_debate'); $dformat = '%W-%y'; break;
    	        case 'monthly' : echo $translator->trans('Month', array(), 'plugin_debate'); $dformat = '%b-%y'; break;
    	    }
    	?>
    	</div>
	</div>

    <?php foreach (DebateVote::getResults($f_debate_nr, $f_fk_language_id) as $results) : ?>
    	<div class="item">
    		<div class="value">
        	<?php foreach ($results as $result) : ?>
        		<?php if (!is_array($result)) continue ?>
        		<div class="division"
        			style="height:<?php echo ($percentage = number_format($result['value']*100/$results['total_count'], 2)) ?>%; ">
        		    <?php echo $percentage ?>%
        		</div>
        	<?php endforeach ?>
        	</div>
    		<div class="bottom"><?php echo strftime($dformat, $results['time']) ?></div>
    	</div>
    <?php endforeach ?>

    <div style="clear: both"></div>
</div>