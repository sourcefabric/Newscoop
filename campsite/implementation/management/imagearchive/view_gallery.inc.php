<TABLE BORDER="0" CELLSPACING="10" CELLPADDING="1" WIDTH="100%">
    <?php
    $count = 0;
    foreach ($imageData as $image) {
        if ((($count+1)%5) == 0) {
            echo '<tr>';
        }
        ?>
        <TD ALIGN="center">
          <A HREF="edit.php?image_id=<?php echo $image['id'].'&'.$imageNav->getSearchLink(); ?>">
            <img src="<?php echo $image['thumbnail_url']; ?>" border="0">
          </a>
          <br>
          <small>
          <?php echo htmlspecialchars($image['description']); ?>
          <br>
          <?php
          $PhotographerLink = '?search_photographer='
          		.urlencode($image['photographer']).'&view='.$view;
          echo "<a href='$PhotographerLink'>"
          		.orE(htmlspecialchars($image['photographer']))."</a><br>";

          $DateLink = '?search_date='.urlencode($image['date']).'&view='.$view;
          echo "<a href='$DateLink'>".orE(htmlspecialchars($image['date']))."</a><br>";

          echo $image['in_use']."<br>";

          if ($User->hasPermission('DeleteImage') && !$image['in_use']) { ?>
                <A HREF="do_del.php?image_id=<?php echo $image['id'].'&'.$imageNav->getSearchLink(); ?>" onclick="return confirm('<?php putGS('Are you sure you want to delete the image $1?', '&quot;'.camp_javascriptspecialchars($image['description']).'&quot;'); ?>');"><IMG SRC="/<?php echo $ADMIN; ?>/img/icon/delete.png" BORDER="0" ALT="<?php  putGS('Delete image $1', htmlspecialchars($image['description'])); ?>"></A>
          <?php
          }
          ?>
          </small>
        </td>
        <?php
        if ((($count+2)%5) ==0) {
            echo '</tr><tr><td colspan="5"><br></td></tr>';
        }
    	$count++;
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
