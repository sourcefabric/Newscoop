<TABLE BORDER="0" CELLSPACING="10" CELLPADDING="1" WIDTH="100%">
    <?php
    $count = 0;
    foreach ($imageData as $image) {
        if (($count%5) == 0) {
            echo '<tr>';
        }
        ?>
        <TD ALIGN="center">
          <A HREF="<?php echo CAMPSITE_IMAGEARCHIVE_DIR; ?>edit.php?image_id=<?php echo $image['id'].'&'.Image_GetSearchUrl($_REQUEST); ?>">
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

          $InUseLink = '?search_inuse='.urlencode($image['in_use']).'&view='.$view;
          echo "<a href='$InUseLink'>".htmlspecialchars($image['in_use'])."</a><br>";

          if ($User->hasPermission('DeleteImage') && !$image['in_use']) { ?>
                <A HREF="<?php echo CAMPSITE_IMAGEARCHIVE_DIR; ?>do_del.php?image_id=<?php echo $image['id'].'&'.Image_GetSearchUrl($_REQUEST); ?>" onclick="return confirm('<?php putGS('Are you sure you want to delete the image $1?', $image['description']); ?>');"><IMG SRC="/priv/img/icon/x.gif" BORDER="0" ALT="<?php  putGS('Delete image $1', htmlspecialchars($image['description'])); ?>"></A>
          <?php
          }
          ?>
          </small>
        </td>
        <?php
        if ((($count+1)%5) ==0) {
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
