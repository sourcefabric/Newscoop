<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0" WIDTH="100%" class="table_list">
    <TR class="table_list_header">
        <TD ALIGN="LEFT" VALIGN="TOP" WIDTH="5%">
          <table border="0" cellspacing="0" cellpadding="0">
          <tr>
          	<td style="padding: 3px;">
          		<B><a href="<?php echo $IdHref; ?>"><?php  putGS("Thumbnail"); ?></a></B>
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
          	<td style="padding: 3px;">
          		<B><a href="<?php echo $DescriptionHref; ?>"><?php  putGS("Description <SMALL>(Click to view details)</SMALL>"); ?></a></B>
          	</td>
          	<td align="left">
          		<a href="<?php echo $DescriptionHref; ?>"><?php echo $DescriptionOrderIcon; ?></a>
          	</td>
          	</tr>
          	</table>
        </TD>
        <TD ALIGN="LEFT" VALIGN="TOP" width="20%">
          <table border="0" cellspacing="0" cellpadding="0" >
          <tr>
          	<td style="padding: 3px;">
          		<B><a href="<?php echo $PhotographerHref; ?>"><?php  putGS("Photographer"); ?></a></B>
          	</td>
          	<td align="left">
          		<a href="<?php echo $PhotographerHref; ?>"><?php echo $PhotographerOrderIcon; ?></a>
          	</td>
          	</tr>
          	</table>
        </TD>
        <TD ALIGN="LEFT" VALIGN="TOP" width="20%">
          <table border="0" cellspacing="0" cellpadding="0">
          <tr>
          	<td style="padding: 3px;">
          		<B><a href="<?php echo $PlaceHref; ?>"><?php  putGS("Place"); ?></a></B>
          	</td>
          	<td align="left">
          		<a href="<?php echo $PlaceHref; ?>"><?php echo $PlaceOrderIcon; ?></a>
          	</td>
          	</tr>
          	</table>
        </TD>
        <TD ALIGN="LEFT" VALIGN="TOP" width="15%">
          <table border="0" cellspacing="0" cellpadding="0">
          <tr>
          	<td style="padding: 3px;" nowrap>
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
          	<td align="center" style="padding: 3px;" nowrap>
          		<B><?php  putGS("In use"); ?></B>
          	</td>
          	<td align="center">
          		<a href="<?php echo $InUseHref; ?>"><?php echo $InUseOrderIcon; ?></a>
          	</td>
          </tr>
          </table>
        </TD>
        <?php
        if ($User->hasPermission('DeleteImage')) { ?>
        <TD ALIGN="center" VALIGN="TOP" WIDTH="5%" style="padding: 3px;"><B><?php  putGS("Delete"); ?></B></TD>
    <?php  } ?>
    </TR>  
    <?php
    $color = 0;
    foreach ($imageData as $image) {
        ?>
        <TR <?php  if ($color) { $color=0; ?>class="list_row_even"<?php  } else { $color=1; ?>class="list_row_odd"<?php  } ?>>
            <TD ALIGN="center">
                <A HREF="edit.php?image_id=<?php  echo $image['id'].$imageNav->getSearchLink(); ?>">
                  <img src="<?php echo $image['thumbnail_url']; ?>" border="0">
                </a>
            </TD>
            <TD style="padding-left: 5px;">
                <A HREF="edit.php?image_id=<?php  echo $image['id'].$imageNav->getSearchLink();?>"><?php echo htmlspecialchars($image['description']); ?></A>
            </TD>
            <TD style="padding-left: 5px;">
                <?php
                $PhotographerLink = 'index.php?' 
                	.'search_photographer='.urlencode($image['photographer'])
                	.'&view='.$view;
                echo "<a href='$PhotographerLink'>"
                	.orE(htmlspecialchars($image['photographer']))."</a>";
                ?>&nbsp;
            </TD>
            <TD style="padding-left: 5px;">
                <?php
                $PlaceLink = 'index.php?' 
                	.'search_place='.urlencode($image['place'])
                	.'&view='.$view;
                echo "<a href='$PlaceLink'>"
                	.orE(htmlspecialchars($image['place']))."</a>";
                ?>&nbsp;
            </TD>
            <TD style="padding-left: 5px;">
                <?php
                $DateLink = 'index.php?'
                	.'search_date='.urlencode($image['date'])
                	.'&view='.$view;
                echo "<a href='$DateLink'>".orE(htmlspecialchars($image['date']))."</a>";
                ?>&nbsp;
            </TD>
            <TD align="center">
                <?php
                echo $image['in_use'];
                ?>&nbsp;
            </TD>
            <?php
            if ($User->hasPermission('DeleteImage')) {
            	if (!$image['in_use']) { ?>
	            	<TD ALIGN="CENTER">
	                <A HREF="do_del.php?image_id=<?php echo $image['id'].'&'.$imageNav->getSearchLink(); ?>" onclick="return confirm('<?php putGS("Are you sure you want to delete the image \\'$1\\'?", camp_javascriptspecialchars($image['description'])); ?>');"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/delete.png" BORDER="0" ALT="<?php putGS('Delete image $1',htmlspecialchars($image['description'])); ?>"></A>
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
        <TD colspan="2" NOWRAP style="color: #00008b;">
        <?php  
        $previousLinkExists = false;
        if ($ImageOffset > 0) { 
        	?> 
            <B><A HREF="index.php?<?php echo $imageNav->getPreviousLink(); ?>">&lt;&lt; <?php  putGS('Previous'); ?></A></B>
        	<?php  
        	$previousLinkExists = true;
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
