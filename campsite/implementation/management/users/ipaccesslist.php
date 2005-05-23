<?php
query ("SELECT Name FROM Users WHERE Id=$User", 'users');
if ($NUM_ROWS) {
	fetchRow($users);
?>
<?php  } ?>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="100%">
<TR>
	<TD><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1"><TR><TD><A HREF="ipadd.php?User=<?php  p($User); ?>" ><IMG SRC="/admin/img/icon/add.png" BORDER="0"></A></TD><TD><A HREF="ipadd.php?User=<?php  p($User); ?>" ><B><?php  putGS("Add new IP address group"); ?></B></A></TD></TR></TABLE></TD>
	<TD ALIGN="RIGHT">
	</TD>
</TABLE>
<P><?php
todefnum('IPOffs');
if ($IPOffs < 0)
	$IPOffs= 0;

query ("SELECT (StartIP & 0xff000000) >> 24 as ip0, (StartIP & 0x00ff0000) >> 16 as ip1, (StartIP & 0x0000ff00) >> 8 as ip2, StartIP & 0x000000ff as ip3, StartIP, Addresses FROM SubsByIP WHERE IdUser = $User LIMIT $IPOffs, 11", 'IPs');
if ($NUM_ROWS) {
	$nr= $NUM_ROWS;
	$i= 10;
	$color= 0;
?><TABLE BORDER="0" CELLSPACING="1" CELLPADDING="3" WIDTH="100%" class="table_list">
	<TR class="table_list_header">
		<TD ALIGN="LEFT" VALIGN="TOP"  ><B><?php  putGS("Start IP"); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP"  ><B><?php  putGS("Number of addresses"); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP" WIDTH="1%" ><B><?php  putGS("Delete"); ?></B></TD>
	</TR>
<?php 
	for($loop=0;$loop<$nr;$loop++) {
		fetchRow($IPs);
		if ($i) { ?>	<TR <?php  if ($color) { $color=0; ?>class="list_row_even"<?php  } else { $color=1; ?>class="list_row_odd"<?php  } ?>>
		<TD >
			<?php  p(getHVar($IPs,'ip0').'.'.getHVar($IPs,'ip1').'.'.getHVar($IPs,'ip2').'.'.getHVar($IPs,'ip3') ); ?>
		</TD>
		<TD >
			<?php  pgetHVar($IPs,'Addresses'); ?>
		</TD>
		<TD ALIGN="CENTER">
			<A HREF="/admin/users/ipdel.php?User=<?php  p($User); ?>&StartIP=<?php  pgetVar($IPs,'StartIP'); ?>"><IMG SRC="/admin/img/icon/delete.png" BORDER="0" ALT="<?php  putGS('Delete'); ?>" TITLE="<?php  putGS('Delete'); ?>"></A>
		</TD>
	</TR>
<?php  
			$i--;
		}
	}
?>	<TR><TD COLSPAN="2" NOWRAP>
<?php  if ($IPOffs <= 0) { ?>		&lt;&lt; <?php  putGS('Previous'); ?>
<?php  } else { ?>		<B><A HREF="ipaccesslist.php?User=<?php  p($User); ?>&IPOffs=<?php  p($IPOffs - 10); ?>">&lt;&lt; <?php  putGS('Previous'); ?></A></B>
<?php  } ?><?php  if ($nr < 11) { ?>		 | <?php  putGS('Next'); ?> &gt;&gt;
<?php  } else { ?>		 | <B><A HREF="ipaccesslist.php?User=<?php  p($User); ?>&IPOffs=<?php  p($IPOffs + 10); ?>"><?php  putGS('Next'); ?> &gt;&gt</A></B>
<?php  } ?>	</TD></TR>
</TABLE>
<?php  } else { ?><BLOCKQUOTE>
	<LI><?php  putGS('No records.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>
