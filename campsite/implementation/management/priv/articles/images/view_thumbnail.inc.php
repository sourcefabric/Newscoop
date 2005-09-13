<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0" WIDTH="100%" class="table_list">
    <TR class="table_list_header">
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
        <TD ALIGN="center" VALIGN="top" width="5%" style="padding: 3px;" nowrap>
          <?php  putGS("In use"); ?>
        </TD>
        <?php if ($articleObj->userCanModify($User)) { ?>
        <TD ALIGN="center" VALIGN="top" WIDTH="5%" style="padding: 3px;"><B><?php  putGS("Link Image to Article"); ?></B></TD>
    	<?php } ?>
    </TR>  
    <?php
    $color = 0;
    $articleUrlData = "&Pub=$Pub&Issue=$Issue&Section=$Section"
    	."&Language=$Language&sLanguage=$sLanguage&Article=$Article";
    foreach ($imageData as $image) {
        ?>
        <TR <?php  if ($color) { $color=0; ?>class="list_row_even"<?php  } else { $color=1; ?>class="list_row_odd"<?php  } ?>>
            <TD ALIGN="center">
                <A HREF="<?php echo 
                camp_html_article_url($articleObj, $Language, "images/edit.php", $_SERVER['REQUEST_URI'])
                .'&ImageId='.$image['id']
                .'&'.$imageNav->getSearchLink(); ?>">
                  <img src="<?php echo $image['thumbnail_url']; ?>" border="0">
                </a>
            </TD>
            <TD style="padding-left: 5px;">
                <A HREF="<?php echo camp_html_article_url($articleObj, $Language, "images/edit.php", $_SERVER['REQUEST_URI']) 
                .'&ImageId='.$image['id']
                .'&'.$imageNav->getSearchLink()
                .$articleUrlData; ?>"><?php echo htmlspecialchars($image['description']); ?></A>
            </TD>
            <TD style="padding-left: 5px;">
                <?php
                $PhotographerLink = 'search.php?' 
                	.'search_photographer='.urlencode($image['photographer'])
                	.$articleUrlData;
                echo "<a href='$PhotographerLink'>"
                	.orE(htmlspecialchars($image['photographer']))."</a>";
                ?>&nbsp;
            </TD>
            <TD style="padding-left: 5px;">
                <?php
                $PlaceLink = 'search.php?'
                	.'search_place='.urlencode($image['place'])
                	.$articleUrlData;
                echo "<a href='$PlaceLink'>"
                	.orE(htmlspecialchars($image['place']))."</a>";
                ?>&nbsp;
            </TD>
            <TD style="padding-left: 5px;">
                <?php
                $DateLink = 'search.php?'
                	.'search_date='.urlencode($image['date'])
                	.$articleUrlData;
                echo "<a href='$DateLink'>".orE(htmlspecialchars($image['date']))."</a>";
                ?>&nbsp;
            </TD>
            <TD align="center">
                <?php
                echo htmlspecialchars($image['in_use']);
                ?>&nbsp;
            </TD>
            <?php
            if ($articleObj->userCanModify($User)) { ?>
        		<form method="POST" action="do_link.php">
				<input type="hidden" name="Pub" value="<?php p($Pub); ?>">
				<input type="hidden" name="Issue" value="<?php p($Issue); ?>">
				<input type="hidden" name="Section" value="<?php p($Section); ?>">
				<input type="hidden" name="Language" value="<?php p($Language); ?>">
				<input type="hidden" name="sLanguage" value="<?php p($sLanguage); ?>">
				<input type="hidden" name="Article" value="<?php p($Article); ?>">
        		<input type="hidden" name="ImageId" value="<?php echo $image['id']; ?>">
            	<TD ALIGN="CENTER">
					<input type="image" src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/add.png"></td>
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
            <B><A HREF="<?php echo camp_html_article_url($articleObj, $Language, "images/search.php").'&'.$imageNav->getPreviousLink(); ?>">&lt;&lt; <?php  putGS('Previous'); ?></A></B>
        	<?php  
        	$previousLinkExists = true;
        } 
        if ($NumImagesFound > ($ImageOffset+$ImagesPerPage)) { 
        	if ($previousLinkExists) {
        		echo ' | ';
        	}
        	?>
            <B><A HREF="<?php echo camp_html_article_url($articleObj, $Language, "images/search.php").'&'.$imageNav->getNextLink(); ?>"><?php  putGS('Next'); ?> &gt;&gt</A></B>
        	<?php  
        } 
        ?></td>
        <td colspan="3"><?php putGS('$1 images found', $NumImagesFound); ?></TD>
    </TR>
</TABLE>
