<TABLE BORDER="0" CELLSPACING="10" CELLPADDING="1" WIDTH="100%">
    <?php
    for($loop = 0; $loop < $nr; $loop++) {
        fetchRow($q_img);
        if ($i) {
            if (is_int(($loop+5)/5)) {
                echo '<tr>';
            }
            ?>
            <TD ALIGN="center">
              <A HREF="<?php echo CAMPSITE_IMAGEARCHIVE_DIR; ?>edit.php?Id=<?php  pgetUVar($q_img,'Id'); echo $Link['SO']?>">
                <img src="<?php echo _TUMB_PREFIX_.getVar($q_img, 'Id'); ?>" border="0">
              </a>
              <br>
              <small>
              <?php  pgetHVar($q_img,'Description'); ?>
              <br>
              <?php
              $PhLink = '?S=1&ph='.getUVar($q_img,'Photographer').'&v='.$v;
              echo "<a href='$PhLink'>".orE(getHVar($q_img,'Photographer'))."</a><br>";

              $DaLink = '?S=1&da='.getUVar($q_img,'Date').'&v='.$v;
              echo "<a href='$DaLink'>".orE(getHVar($q_img,'Date'))."</a><br>";

              $UseLink = '?S=1&use='.getUVar($q_img,'inUse').'&v='.$v;
              echo "<a href='$UseLink'>".getHVar($q_img,'inUse')."</a><br>";

              if ($dia != 0) { ?>
                    <A HREF="<?php echo CAMPSITE_IMAGEARCHIVE_DIR; ?>del.php?Id=<?php pgetVar($q_img, 'Id'); echo $Link['SO']; ?>"><IMG SRC="/priv/img/icon/x.gif" BORDER="0" ALT="<?php  putGS('Delete image $1',getHVar($q_img,'Description')); ?>"></A>
              <?php
              }
              ?>
              </small>
            </td>
            <?php
            if (is_int(($loop+1)/5)) {
                echo '</tr><tr><td colspan="5"><br></td></tr>';
            }
        $i--;
        }
    }

    ?>
    <TR>
        <TD colspan="2" NOWRAP>
        <?php  if ($ImgOffs <= 0) { ?>        &lt;&lt; <?php  putGS('Previous'); ?>
        <?php  } else { ?>        <B><A HREF="<?php echo CAMPSITE_IMAGEARCHIVE_DIR; ?>index.php?<?php echo $Link['P']; ?>">&lt;&lt; <?php  putGS('Previous'); ?></A></B>
        <?php  } ?><?php  if ($nr < $lpp+1) { ?>         | <?php  putGS('Next'); ?> &gt;&gt;
        <?php  } else { ?>         | <B><A HREF="<?php echo CAMPSITE_IMAGEARCHIVE_DIR; ?>index.php?<?php echo $Link['N']; ?>"><?php  putGS('Next'); ?> &gt;&gt</A></B>
        <?php  } ?></td>
        <td colspan="3"><?php query ($baseq, 'q_counter'); putGS('$1 images found', $NUM_ROWS); ?></TD>
    </TR>
</TABLE>
