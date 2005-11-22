<?php
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/pub/pub_common.php");

// Check permissions
list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

$PubOffs = Input::Get('PubOffs', 'int', 0, true);
if ($PubOffs < 0) {
    $PubOffs = 0;
}
$ItemsPerPage = 20;

$sqlOptions = array("LIMIT" => array("START" => $PubOffs, "MAX_ROWS" => ($ItemsPerPage+1)), 
                    "ORDER BY" => array("Name" => "ASC"));
$publications = Publication::GetPublications($sqlOptions);
$numPublications = Publication::GetNumPublications();

camp_html_content_top(getGS('Publication List'), null);

?>

<?php  if ($User->hasPermission("ManagePub")) { ?>    <P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" class="action_buttons">
<TR>
	<TD>
		<A HREF="/<?php echo $ADMIN; ?>/pub/add.php?Back=<?php p(urlencode($_SERVER['REQUEST_URI'])); ?>"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/add.png" BORDER="0"></A>
	</TD>
	<TD>
		<A HREF="/<?php echo $ADMIN; ?>/pub/add.php?Back=<?php p(urlencode($_SERVER['REQUEST_URI'])); ?>"><B><?php  putGS("Add new publication"); ?></B></A>
	</TD>
</TR>
</TABLE>
<?php  } ?>
<P>
<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="3" class="table_list">
<TR class="table_list_header">
    <TD ALIGN="LEFT" VALIGN="TOP"><B><?php  putGS("Name<BR><SMALL>(click to see issues)</SMALL>"); ?></B></TD>
    <TD ALIGN="LEFT" VALIGN="TOP"><B><?php  putGS("Default Site Alias"); ?></B></TD>
    <TD ALIGN="LEFT" VALIGN="TOP"><B><?php  putGS("Default Language"); ?></B></TD>
    <?php  if ($User->hasPermission("ManagePub")) { ?>
    <TD ALIGN="LEFT" VALIGN="TOP" WIDTH="20%" ><B><?php  putGS("URL Type"); ?></B></TD>
    <TD ALIGN="LEFT" VALIGN="TOP"><B><?php  putGS("Configure"); ?></B></TD>
    <?php  }
    if ($User->hasPermission("DeletePub")) { ?>
    <TD ALIGN="LEFT" VALIGN="TOP"><B><?php  putGS("Delete"); ?></B></TD>
    <?php  } ?>
</TR>
<?php
$color = 0;
foreach ($publications as $pub) { ?>    
        <TR <?php  if ($color) { $color=0; ?>class="list_row_even"<?php  } else { $color=1; ?>class="list_row_odd"<?php  } ?>>
        <TD>
            <A HREF="/<?php echo $ADMIN; ?>/issues/?Pub=<?php  p($pub->getPublicationId()); ?>"><?php  p(htmlspecialchars($pub->getName())); ?></A>
        </TD>
        <TD>
            <?php  p(htmlspecialchars($pub->getProperty("Alias"))); ?>&nbsp;
        </TD>
        <TD>
            <?php  p(htmlspecialchars($pub->getProperty("NativeName"))); ?>&nbsp;
        </TD>
        <?php  if ($User->hasPermission("ManagePub")) { ?>        
        <TD>
            <?php  p(htmlspecialchars($pub->getProperty('URLType'))); ?>&nbsp;
        </TD>
        <TD ALIGN="CENTER">
            <A HREF="/<?php p($ADMIN); ?>/pub/edit.php?Pub=<?php p($pub->getPublicationId()); ?>"><img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/configure.png" alt="<?php  putGS("Configure"); ?>" title="<?php  putGS("Configure"); ?>"  border="0"></A>
        </TD>
        <?php  }
        if ($User->hasPermission("DeletePub")) { ?>
        <TD ALIGN="CENTER">
        <A HREF="/<?php p($ADMIN); ?>/pub/do_del.php?Pub=<?php p($pub->getPublicationId()); ?>" onclick="return confirm('<?php putGS('Are you sure you want to delete the publication $1?', htmlspecialchars($pub->getName())); ?>');"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/delete.png" BORDER="0" ALT="<?php  putGS('Delete publication $1',htmlspecialchars($pub->getName())); ?>" TITLE="<?php  putGS('Delete publication $1',htmlspecialchars($pub->getName())); ?>" ></A>
        </TD>
        <?php  } ?>
</TR>
<?php
} // foreach ?>
<TR>
<TD COLSPAN="2" NOWRAP>
<?php  
if ($PubOffs > 0) { ?>
    <B><A HREF="index.php?PubOffs=<?php  print (max(0, ($PubOffs - $ItemsPerPage))); ?>">&lt;&lt; <?php  putGS('Previous'); ?></A></B>
<?php  } ?>
<?php  if ($numPublications > ($PubOffs+$ItemsPerPage)) { ?>
      | <B><A HREF="index.php?PubOffs=<?php  print ($PubOffs + $ItemsPerPage); ?>"><?php  putGS('Next'); ?> &gt;&gt</A></B>
<?php  } ?>    </TD></TR>
</TABLE>
<?php camp_html_copyright_notice(); ?>
