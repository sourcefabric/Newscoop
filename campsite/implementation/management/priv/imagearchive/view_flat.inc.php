<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="3" WIDTH="100%" class="table_list">
	<TR class="table_list_header">
		<TD ALIGN="LEFT" VALIGN="TOP" WIDTH="5%">
		  <table border="0" cellspacing="0" cellpadding="0">
		  <tr>
		  	<td valign="top">
		  		<B><a href="<?php echo $IdHref; ?>"><?php  putGS("Identifier"); ?></a></B>
		  	</td>
		  	<td align="left">
		  		<a href="<?php echo $IdHref; ?>"><?php echo $IdOrderIcon; ?></a>
		  	</td>
		  </tr>
		  </table>
		</TD>
		<TD ALIGN="LEFT" VALIGN="TOP" width="35%">
		  <table border="0" cellspacing="0" cellpadding="0">
		  <tr>
		  	<td nowrap valign="top">
		  		<B><a href="<?php echo $DescriptionHref; ?>"><?php  putGS("Description <SMALL>(Click to view details)</SMALL>"); ?></a></B>
		  	</td>
		  	<td align="left">
		  		<a href="<?php echo $DescriptionHref; ?>"><?php echo $DescriptionOrderIcon; ?></a>
		  	</td>
		  	</tr>
		  	</table>
		</TD>
		<TD ALIGN="LEFT" VALIGN="TOP" width="35%">
		  <table border="0" cellspacing="0" cellpadding="0">
		  <tr>
		  	<td valign="top">
		  		<B><a href="<?php echo $PhotographerHref; ?>"><?php  putGS("Photographer"); ?></a></B>
		  	</td>
		  	<td align="left">
		  		<a href="<?php echo $PhotographerHref; ?>"><?php echo $PhotographerOrderIcon; ?></a>
		  	</td>
		  </tr>
		  </table>
		</TD>
		<TD ALIGN="LEFT" VALIGN="TOP" width="15%">
		  <table border="0" cellspacing="0" cellpadding="0">
		  <tr>
		  	<td nowrap valign="top">
		  		<B><a href="<?php echo $DateHref; ?>"><?php  putGS("Date <SMALL>(yyyy-mm-dd)</SMALL>"); ?></a></B>
		  	</td>
		  	<td align="left">
		  		<a href="<?php echo $DateHref; ?>"><?php echo $DateOrderIcon; ?></a>
		  	</td>
		  </tr>
		  </table>
		</TD>
		<TD ALIGN="LEFT" VALIGN="TOP" width="5%">
		  <table border="0" cellspacing="0" cellpadding="0">
		  <tr>
		  	<td nowrap valign="top" align="center">
		  		<B><?php  putGS("In use"); ?></B>
		  	</td>
		  	<td align="left">
		  		<a href="<?php echo $InUseHref; ?>"><?php echo $InUseOrderIcon; ?></a>
		  	</td>
		  </tr>
		  </table>
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
		<TR <?php  if ($color) { $color=0; ?>class="list_row_even"<?php  } else { $color=1; ?>class="list_row_odd"<?php  } ?>>
			<TD ALIGN="center">
				<?php echo $image['id']; ?>
			</TD>
			<TD >
				<A HREF="edit.php?image_id=<?php echo $image['id'].'&'.$imageNav->getSearchLink();?>"><?php echo htmlspecialchars($image['description']); ?></A>
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
				// inUse info //
				echo $image['in_use'];
				?>&nbsp;
			</TD>
			<?php
			if ($User->hasPermission('DeleteImage')) {
				if (!$image['in_use']) { ?>
				  <TD ALIGN="CENTER">
					<A HREF="do_del.php?image_id=<?php echo $image['id'].'&'.$imageNav->getSearchLink(); ?>" onclick="return confirm('<?php putGS('Are you sure you want to delete the image $1?', '&quot;'.camp_javascriptspecialchars($image['description']).'&quot;'); ?>');"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/delete.png" BORDER="0" ALT="<?php  putGS('Delete image $1',htmlspecialchars($image['description'])); ?>"></A>
				  </TD>
				<?php
				}
				else {
					?>
					<TD ALIGN="CENTER">&nbsp;</TD> 
					<?php
				}
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
        	<B><A HREF="index.php?<?php echo $imageNav->getPreviousLink(); ?>">&lt;&lt; <?php  putGS('Previous'); ?></A></B>
        	<?php
        } 
        if ($NumImagesFound > ($ImageOffset+CAMPSITE_IMAGEARCHIVE_IMAGES_PER_PAGE)) { 
        	if ($previousLinkExists) {
        		echo ' | ';
        	}
        	?>
      		<B><A HREF="index.php?<?php echo $imageNav->getNextLink(); ?>"><?php  putGS('Next'); ?> &gt;&gt</A></B>
        	<?php
        } 
        ?></td>
        <td colspan="3"><?php putGS('$1 images found', $NumImagesFound); ?></TD>
    </TR>
</TABLE>
