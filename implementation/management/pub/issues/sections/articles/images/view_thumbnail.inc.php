<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0" WIDTH="100%">
    <TR BGCOLOR="#C0D0FF">
        <TD ALIGN="LEFT" VALIGN="TOP" WIDTH="5%">
          <table border="0" cellspacing="0" cellpadding="0"><tr><td style="padding: 3px;"><B><a href="<?php echo $IdHref; ?>"><?php  putGS("Thumbnail"); ?></a></B></td><td align="left"><a href="<?php echo $IdHref; ?>"><?php echo $IdOrderIcon; ?></a></td></tr></table>
        </TD>
        <TD ALIGN="LEFT" VALIGN="TOP" width="35%">
          <table border="0" cellspacing="0" cellpadding="0"><tr><td style="padding: 3px;"><B><a href="<?php echo $DescriptionHref; ?>"><?php  putGS("Description"); ?></a></B></td><td align="left"><a href="<?php echo $DescriptionHref; ?>"><?php echo $DescriptionOrderIcon; ?></a></td></tr></table>
        </TD>
        <TD ALIGN="LEFT" VALIGN="TOP" width="20%">
          <table border="0" cellspacing="0" cellpadding="0" ><tr><td style="padding: 3px;"><B><a href="<?php echo $PhotographerHref; ?>"><?php  putGS("Photographer"); ?></a></B></td><td align="left"><a href="<?php echo $PhotographerHref; ?>"><?php echo $PhotographerOrderIcon; ?></a></td></tr></table>
        </TD>
        <TD ALIGN="LEFT" VALIGN="TOP" width="20%">
          <table border="0" cellspacing="0" cellpadding="0"><tr><td style="padding: 3px;"><B><a href="<?php echo $PlaceHref; ?>"><?php  putGS("Place"); ?></a></B></td><td align="left"><a href="<?php echo $PlaceHref; ?>"><?php echo $PlaceOrderIcon; ?></a></td></tr></table>
        </TD>
        <TD ALIGN="LEFT" VALIGN="TOP" width="15%">
          <table border="0" cellspacing="0" cellpadding="0"><tr><td style="padding: 3px;" nowrap><B><a href="<?php echo $DateHref; ?>"><?php  putGS("Date<BR><SMALL>(yyyy-mm-dd)</SMALL>"); ?></a></B></td><td align="left"><a href="<?php echo $DateHref; ?>"><?php echo $DateOrderIcon; ?></a></td></tr></table>
        </TD>
        <TD ALIGN="LEFT" VALIGN="TOP" width="5%">
          <table border="0" cellspacing="0" cellpadding="0"><tr><td align="center" style="padding: 3px;" nowrap><B><a href="<?php echo $InUseHref; ?>"><?php  putGS("In use"); ?></a></B></td><td align="left"><a href="<?php echo $InUseHref; ?>"><?php echo $InUseOrderIcon; ?></a></td></tr></table>
        </TD>
        <?php
        if ($User->hasPermission('DeleteImage')) { ?>
        <TD ALIGN="center" VALIGN="TOP" WIDTH="5%" style="padding: 3px;"><B><?php  putGS("Link Image to Article"); ?></B></TD>
    <?php  } ?>
    </TR>  
    <?php
    $color = 0;
    foreach ($imageData as $image) {
        ?>
        <TR <?php  if ($color) { $color=0; ?>BGCOLOR="#D0D0B0"<?php  } else { $color=1; ?>BGCOLOR="#D0D0D0"<?php  } ?>>
            <TD ALIGN="center">
                <A HREF="<?php echo 
                CampsiteInterface::ArticleUrl($articleObj, $InterfaceLanguageId, "images/view.php")
                .'&ImageId='.$image['id']
                .'&'.$imageNav->getSearchLink()
                .'&BackLink=search.php'; ?>">
                  <img src="<?php echo $image['thumbnail_url']; ?>" border="0">
                </a>
            </TD>
            <TD style="padding-left: 5px;">
                <A HREF="<?php echo CampsiteInterface::ArticleUrl($articleObj, $InterfaceLanguageId, "images/view.php") 
                .'&ImageId='.$image['id']
                .'&'.$imageNav->getSearchLink()
                .'&BackLink=search.php'; ?>"><?php echo htmlspecialchars($image['description']); ?></A>
            </TD>
            <TD style="padding-left: 5px;">
                <?php
                $PhotographerLink = 'search.php?' 
                	.'search_photographer='.urlencode($image['photographer'])
                	.'&view='.$view;
                echo "<a href='$PhotographerLink'>"
                	.orE(htmlspecialchars($image['photographer']))."</a>";
                ?>&nbsp;
            </TD>
            <TD style="padding-left: 5px;">
                <?php
                $PlaceLink = 'search.php?'
                	.'search_place='.urlencode($image['place'])
                	.'&view='.$view;
                echo "<a href='$PlaceLink'>"
                	.orE(htmlspecialchars($image['place']))."</a>";
                ?>&nbsp;
            </TD>
            <TD style="padding-left: 5px;">
                <?php
                $DateLink = 'search.php?'
                	.'search_date='.urlencode($image['date'])
                	.'&view='.$view;
                echo "<a href='$DateLink'>".orE(htmlspecialchars($image['date']))."</a>";
                ?>&nbsp;
            </TD>
            <TD align="center">
                <?php
                $InUseLink = 'search.php?'
                	.'search_inuse='.urlencode($image['in_use'])
                	.'&view='.$view;
                echo "<a href='$InUseLink'>".htmlspecialchars($image['in_use'])."</a>";
                ?>&nbsp;
            </TD>
            <?php
            if ($User->hasPermission('ChangeArticle')) { ?>
        		<form method="POST" action="do_link.php" onsubmit="return validateForm(this, 0, 0, 0, 1, 8);">
				<input type="hidden" name="PublicationId" value="<?php p($PublicationId); ?>">
				<input type="hidden" name="IssueId" value="<?php p($IssueId); ?>">
				<input type="hidden" name="SectionId" value="<?php p($SectionId); ?>">
				<input type="hidden" name="InterfaceLanguageId" value="<?php p($InterfaceLanguageId); ?>">
				<input type="hidden" name="ArticleLanguageId" value="<?php p($ArticleLanguageId); ?>">
				<input type="hidden" name="ArticleId" value="<?php p($ArticleId); ?>">
        		<input type="hidden" name="ImageId" value="<?php echo $image['id']; ?>">
            	<TD ALIGN="CENTER">
					<input type="image" src="/priv/img/icon/link_image_to_article.gif"></td>
              	</TD>
           		</form>
            	<?php
         	}
         	else {
         		?>
            	<TD ALIGN="CENTER">&nbsp;</TD>             		
         		<?php
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
            <B><A HREF="<?php echo CampsiteInterface::ArticleUrl($articleObj, $InterfaceLanguageId, "images/search.php").'&'.$imageNav->getPreviousLink(); ?>">&lt;&lt; <?php  putGS('Previous'); ?></A></B>
        	<?php  
        	$previousLinkExists = true;
        } 
        if ($NumImagesFound > ($ImageOffset+$ImagesPerPage)) { 
        	if ($previousLinkExists) {
        		echo ' | ';
        	}
        	?>
            <B><A HREF="<?php echo CampsiteInterface::ArticleUrl($articleObj, $InterfaceLanguageId, "images/search.php").'&'.$imageNav->getNextLink(); ?>"><?php  putGS('Next'); ?> &gt;&gt</A></B>
        	<?php  
        } 
        ?></td>
        <td colspan="3" style="color: #00008b;"><?php putGS('$1 images found', $NumImagesFound); ?></TD>
    </TR>
</TABLE>
