<?php
require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/pub/pub_common.php");
require_once($GLOBALS['g_campsiteDir']."/classes/SimplePager.php");
camp_load_translation_strings("api");

// Check permissions
$PubOffs = camp_session_get('PubOffs', 0);
if ($PubOffs < 0) {
    $PubOffs = 0;
}
$ItemsPerPage = 15;

$sqlOptions = array("LIMIT" => array("START" => $PubOffs, "MAX_ROWS" => $ItemsPerPage),
                    "ORDER BY" => array("Name" => "ASC"));
$publications = Publication::GetPublications(null, null, $sqlOptions);
$numPublications = Publication::GetNumPublications();

$pager = new SimplePager($numPublications, $ItemsPerPage, "PubOffs", "index.php?");

camp_html_content_top(getGS('Publication List'), null);

if ($g_user->hasPermission("ManagePub") && SaaS::singleton()->hasPermission("AddPub")) { ?>
<P>
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
<?php if ($numPublications > 0) { ?>
<table class="indent">
<TR>
	<TD>
		<?php echo $pager->render(); ?>
    </TD>
</TR>
</TABLE>
<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="3" class="table_list">
<TR class="table_list_header">
	<td align="center" valign="top"><?php putGS("Number"); ?></td>
    <TD ALIGN="LEFT" VALIGN="TOP"><?php  putGS("Name"); ?><BR><SMALL>(<?php putGS('click to see issues');?>)</SMALL></TD>
    <TD ALIGN="LEFT" VALIGN="TOP"><?php  putGS("Default Site Alias"); ?></TD>
    <TD ALIGN="LEFT" VALIGN="TOP"><?php  putGS("Default Language"); ?></TD>
    <?php  if ($g_user->hasPermission("ManagePub")) { ?>
    <TD ALIGN="LEFT" VALIGN="TOP"><?php  putGS("Comments enabled"); ?></TD>
    <TD ALIGN="center" VALIGN="TOP"><?php  putGS("URL Type"); ?></TD>
    <TD ALIGN="LEFT" VALIGN="TOP"><?php  putGS("Configure"); ?></TD>
    <?php  }
    if ($g_user->hasPermission("DeletePub")) { ?>
    <TD ALIGN="LEFT" VALIGN="TOP"><?php  putGS("Delete"); ?></TD>
    <?php  } ?>
</TR>
<?php
$color = 0;
foreach ($publications as $pub) { ?>
        <TR <?php  if ($color) { $color=0; ?>class="list_row_even"<?php  } else { $color=1; ?>class="list_row_odd"<?php  } ?>>
        <td align="center"><?php p($pub->getPublicationId()); ?></td>
        <TD>
            <A HREF="/<?php echo $ADMIN; ?>/issues/?Pub=<?php  p($pub->getPublicationId()); ?>"><?php  p(htmlspecialchars($pub->getName())); ?></A>
        </TD>
        <TD>
            <?php
            	$aliasObj = new Alias($pub->getDefaultAliasId());
            	p(htmlspecialchars($aliasObj->getName()));
            ?>&nbsp;
        </TD>
        <TD>
            <?php
                $languageObj = new Language($pub->getDefaultLanguageId());
                p(htmlspecialchars($languageObj->getName()));
            ?>&nbsp;
        </TD>
        <?php  if ($g_user->hasPermission("ManagePub")) { ?>
        <TD align="center">
            <?php if ($pub->commentsEnabled()) { ?>
                <img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/is_shown.png" border="0">
            <?php } else { ?>
                <img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/is_hidden.png" border="0">
            <?php } ?>
        </TD>
        <TD align="center">
            <?php
            	$urlTypeObj = new UrlType($pub->getUrlTypeId());
            	p(htmlspecialchars($urlTypeObj->getName()));
            ?>&nbsp;
        </TD>
        <TD ALIGN="CENTER">
            <A HREF="/<?php p($ADMIN); ?>/pub/edit.php?Pub=<?php p($pub->getPublicationId()); ?>"><img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/configure.png" alt="<?php  putGS("Configure"); ?>" title="<?php  putGS("Configure"); ?>"  border="0"></A>
        </TD>
        <?php  }
        if ($g_user->hasPermission("DeletePub")) { ?>
        <TD ALIGN="CENTER">
        <A HREF="/<?php p($ADMIN); ?>/pub/do_del.php?Pub=<?php p($pub->getPublicationId()); ?>&<?php echo SecurityToken::URLParameter(); ?>" onclick="return confirm('<?php putGS('Are you sure you want to delete the publication $1?', htmlspecialchars($pub->getName())); ?>');"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/delete.png" BORDER="0" ALT="<?php  putGS('Delete publication $1',htmlspecialchars($pub->getName())); ?>" TITLE="<?php  putGS('Delete publication $1',htmlspecialchars($pub->getName())); ?>" ></A>
        </TD>
        <?php  } ?>
</TR>
<?php
} // foreach ?>
</table>
<table class="indent">
<TR>
	<TD>
		<?php echo $pager->render(); ?>
    </TD>
</TR>
</TABLE>
<?php
} else {
	?>
	<BLOCKQUOTE>
	<LI><?php  putGS('No publications.'); ?></LI>
	</BLOCKQUOTE>
	<?php
}
camp_html_copyright_notice(); ?>
