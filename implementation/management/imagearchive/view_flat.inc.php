<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="3" WIDTH="100%">
	<TR BGCOLOR="#C0D0FF">
		<TD ALIGN="LEFT" VALIGN="TOP" WIDTH="5%">
		  <table border="0" cellspacing="0" cellpadding="0" width="100%"><tr><td valign="top"><B><a href="<?php echo $IdHref; ?>"><?php  putGS("Identifier"); ?></a></B></td><td align="right" valign="top"><?php echo $IdOrderIcon; ?></td></tr></table>
		</TD>
		<TD ALIGN="LEFT" VALIGN="TOP" width="35%">
		  <table border="0" cellspacing="0" cellpadding="0" width="100%"><tr><td nowrap valign="top"><B><a href="<?php echo $DescriptionHref; ?>"><?php  putGS("Description<BR><SMALL>(Click to view details)</SMALL>"); ?></a></B></td><td valign="top" align="right"><?php echo $DescriptionOrderIcon; ?></td></tr></table>
		</TD>
		<TD ALIGN="LEFT" VALIGN="TOP" width="35%">
		  <table border="0" cellspacing="0" cellpadding="0" width="100%"><tr><td valign="top"><B><a href="<?php echo $PhotographerHref; ?>"><?php  putGS("Photographer"); ?></a></B></td><td align="right" valign="top"><?php echo $PhotographerOrderIcon; ?></td></tr></table>
		</TD>
		<TD ALIGN="LEFT" VALIGN="TOP" width="15%">
		  <table border="0" cellspacing="0" cellpadding="0" width="100%"><tr><td nowrap valign="top"><B><a href="<?php echo $DateHref; ?>"><?php  putGS("Date<BR><SMALL>(yyyy-mm-dd)</SMALL>"); ?></a></B></td><td align="right" valign="top"><?php echo $DateOrderIcon; ?></td></tr></table>
		</TD>
		<TD ALIGN="LEFT" VALIGN="TOP" width="5%">
		  <table border="0" cellspacing="0" cellpadding="0" width="100%"><tr><td nowrap valign="top" align="center"><B><a href="<?php echo $InUseHref; ?>"><?php  putGS("In use"); ?></a></B></td><td align="right" valign="top"><?php echo $InUseOrderIcon; ?></td></tr></table>
		</TD>
		<?php
		if ($User->hasPermission('DeleteImage')) { ?>
		<TD ALIGN="center" VALIGN="TOP" WIDTH="5%" ><B><?php  putGS("Delete"); ?></B></TD>
	<?php  } ?>
	</TR>
	<?php
	$color = 0;
	foreach ($imageData as $image) {
		?>
		<TR <?php  if ($color) { $color=0; ?>BGCOLOR="#D0D0B0"<?php  } else { $color=1; ?>BGCOLOR="#D0D0D0"<?php  } ?>>
			<TD ALIGN="center">
				<?php echo $image['id']; ?>
			</TD>
			<TD >
				<A HREF="<?php echo CAMPSITE_IMAGEARCHIVE_DIR; ?>edit.php?image_id=<?php echo $image['id'].'&'.Image_GetSearchUrl($_REQUEST);?>"><?php echo htmlspecialchars($image['description']); ?></A>
			</TD>
			<TD >
				<?php
				// photographer search link //
				$PhotographerLink = '?search_photographer='
					.urlencode($image['photographer']).'&view='.$view;
				echo "<a href='$PhotographerLink'>"
					.orE(htmlspecialchars($image['photographer']))."</a>";
				?>&nbsp;
			</TD>
			<TD >
				<?php
				// date search link //
				$DateLink = '?search_date='.urlencode($image['date']).'&view='.$view;
				echo "<a href='$DateLink'>".orE(htmlspecialchars($image['date']))."</a>";
				?>&nbsp;
			</TD>
			<TD align="center">
				<?php
				// inUse link //
				$InUseLink = '?search_inuse='.urlencode($image['in_use']).'&view='.$view;
				echo "<a href='$InUseLink'>".htmlspecialchars($image['in_use'])."</a>";
				?>&nbsp;
			</TD>
			<?php
			if ($User->hasPermission('DeleteImage') && !$image['in_use']) { ?>
			  <TD ALIGN="CENTER">
				<A HREF="<?php echo CAMPSITE_IMAGEARCHIVE_DIR; ?>do_del.php?image_id=<?php echo $image['id'].'&'.Image_GetSearchUrl($_REQUEST); ?>" onclick="return confirm('<?php putGS('Are you sure you want to delete the image $1?', $image['description']); ?>');"><IMG SRC="/priv/img/icon/x.gif" BORDER="0" ALT="<?php  putGS('Delete image $1',htmlspecialchars($image['description'])); ?>"></A>
			  </TD>
			<?php
			}
			else {
				?>
				<TD ALIGN="CENTER">&nbsp;</TD> 
				<?
			}
				
			?>
		</TR>
    <?php
    }
	?>
    <TR>
        <TD colspan="2" NOWRAP>
        <?php  
        $previousLinkExists = false;
        if ($ImageOffset > 0) { 
        	$previousLinkExists = true;
        	?>
        	<B><A HREF="<?php echo CAMPSITE_IMAGEARCHIVE_DIR; ?>index.php?<?php echo $Link['previous']; ?>">&lt;&lt; <?php  putGS('Previous'); ?></A></B>
        	<?php
        } 
        if ($NumImagesFound > ($ImageOffset+$ImagesPerPage)) { 
        	if ($previousLinkExists) {
        		echo ' | ';
        	}
        	?>
      		<B><A HREF="<?php echo CAMPSITE_IMAGEARCHIVE_DIR; ?>index.php?<?php echo $Link['next']; ?>"><?php  putGS('Next'); ?> &gt;&gt</A></B>
        	<?php
        } 
        ?></td>
        <td colspan="3"><?php putGS('$1 images found', $NumImagesFound); ?></TD>
    </TR>
</TABLE>
