<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0" WIDTH="100%">
	<TR BGCOLOR="#C0D0FF">
		<TD ALIGN="LEFT" VALIGN="TOP" WIDTH="5%">
		  <table border="0" cellspacing="0" cellpadding="0" width="100%"><tr><td><B><a href="<?php echo $IdHref; ?>"><?php  putGS("Identifier"); ?></a></B></td><td align="right"><?php echo $IdO; ?></td></tr></table>
		</TD>
		<TD ALIGN="LEFT" VALIGN="TOP" width="35%">
		  <table border="0" cellspacing="0" cellpadding="0" width="100%"><tr><td><B><a href="<?php echo $DeHref; ?>"><?php  putGS("Description<BR><SMALL>(Click to view details)</SMALL>"); ?></a></B></td><td align="right"><?php echo $DeO; ?></td></tr></table>
		</TD>
		<TD ALIGN="LEFT" VALIGN="TOP" width="35%">
		  <table border="0" cellspacing="0" cellpadding="0" width="100%"><tr><td><B><a href="<?php echo $PhHref; ?>"><?php  putGS("Photographer"); ?></a></B></td><td align="right"><?php echo $PhO; ?></td></tr></table>
		</TD>
		<TD ALIGN="LEFT" VALIGN="TOP" width="15%">
		  <table border="0" cellspacing="0" cellpadding="0" width="100%"><tr><td><B><a href="<?php echo $DaHref; ?>"><?php  putGS("Date<BR><SMALL>(yyyy-mm-dd)</SMALL>"); ?></a></B></td><td align="right"><?php echo $DaO; ?></td></tr></table>
		</TD>
		<TD ALIGN="LEFT" VALIGN="TOP" width="5%">
		  <table border="0" cellspacing="0" cellpadding="0" width="100%"><tr><td><B><a href="<?php echo $UseHref; ?>"><?php  putGS("In use"); ?></a></B></td><td align="right"><?php echo $UseO; ?></td></tr></table>
		</TD>
		<?php
		if ($dia != 0) { ?>
		<TD ALIGN="LEFT" VALIGN="TOP" WIDTH="5%" ><B><?php  putGS("Delete"); ?></B></TD>
	<?php  } ?>
	</TR>
	<?php
	for($loop = 0; $loop < $nr; $loop++) {
		fetchRow($q_img);
		if ($i) {
			?>
			<TR <?php  if ($color) { $color=0; ?>BGCOLOR="#D0D0B0"<?php  } else { $color=1; ?>BGCOLOR="#D0D0D0"<?php  } ?>>
				<TD ALIGN="RIGHT">
					<?php  pgetHVar($q_img,'Id'); ?>
				</TD>
				<TD >
					<A HREF="<?php echo CAMPSITE_IMAGEARCHIVE_DIR; ?>edit.php?Id=<?php  pgetUVar($q_img,'Id'); echo $Link['SO']?>"><?php  pgetHVar($q_img,'Description'); ?></A>
				</TD>
				<TD >
					<?php
					// photographer search link //
					$PhLink = '?S=1&ph='.getUVar($q_img,'Photographer').'&v='.$v;
					echo "<a href='$PhLink'>".orE(getHVar($q_img,'Photographer'))."</a>";
					?>&nbsp;
				</TD>
				<TD >
					<?php
					// date search link //
					$DaLink = '?S=1&da='.getUVar($q_img,'Date').'&v='.$v;
					echo "<a href='$DaLink'>".orE(getHVar($q_img,'Date'))."</a>";
					?>&nbsp;
				</TD>
				<TD >
					<?php
					// inUse link //
					$UseLink = '?S=1&use='.getUVar($q_img,'inUse').'&v='.$v;
					echo "<a href='$UseLink'>".getHVar($q_img,'inUse')."</a>";
					?>&nbsp;
				</TD>
				<?php
				if ($dia != 0) { ?>
				  <TD ALIGN="CENTER">
					<A HREF="<?php echo CAMPSITE_IMAGEARCHIVE_DIR; ?>del.php?Id=<?php pgetVar($q_img, 'Id'); echo $Link['SO']; ?>"><IMG SRC="/priv/img/icon/x.gif" BORDER="0" ALT="<?php  putGS('Delete image $1',getHVar($q_img,'Description')); ?>"></A>
				  </TD>
				<?php
				}
				?>
			</TR>
	    <?php
	    $i--;
	    }
    }

	?>
    <TR>
        <TD colspan="2" NOWRAP>
        <?php  if ($ImgOffs <= 0) { ?>		&lt;&lt; <?php  putGS('Previous'); ?>
        <?php  } else { ?>		<B><A HREF="<?php echo CAMPSITE_IMAGEARCHIVE_DIR; ?>index.php?<?php echo $Link['P']; ?>">&lt;&lt; <?php  putGS('Previous'); ?></A></B>
        <?php  } ?><?php  if ($nr < $lpp+1) { ?>		 | <?php  putGS('Next'); ?> &gt;&gt;
        <?php  } else { ?>		 | <B><A HREF="<?php echo CAMPSITE_IMAGEARCHIVE_DIR; ?>index.php?<?php echo $Link['N']; ?>"><?php  putGS('Next'); ?> &gt;&gt</A></B>
        <?php  } ?></td>
        <td colspan="3"><?php query ($baseq, 'q_counter'); putGS('$1 images found', $NUM_ROWS); ?></TD>
    </TR>
</TABLE>
