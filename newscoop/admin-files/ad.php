<?php
require_once dirname(__FILE__) . '/../db_connect.php';
require_once dirname(__FILE__) . '/../classes/Input.php';

$translator = \Zend_Registry::get('container')->getService('translator');

$ADReason = Input::Get('ADReason', 'string', $translator->trans('You do not have the right to access this page.', array(), 'home'), true);
$BackLink = Input::Get('Back', 'string', "/$ADMIN/home.php", true);
?>
<p>
<FORM>
<CENTER>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box" ALIGN="CENTER" style="margin-top: 50px; margin-bottom: 50px;">
<TR>
	<TD COLSPAN="2">
		<B><font color="red"><?php echo $translator->trans("Error"); ?> </font></B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
		<BLOCKQUOTE>
		<font color="red">
		<li><?php print htmlspecialchars($ADReason, ENT_QUOTES); ?></li>
		</font>
		</BLOCKQUOTE>
	</TD>
</TR>
<TR>
	<TD COLSPAN="2" align="center">
		<DIV ALIGN="CENTER">
            <INPUT id="ok-button" TYPE="button" class="button" NAME="OK" VALUE="<?php echo $translator->trans('OK'); ?>" />
            <script type="text/javascript">
            $(function() {
                $('#ok-button').click(function() {
		            <?php if ($BackLink != "/$ADMIN/home.php") { ?>
                    location.href = <?php echo json_encode($BackLink); ?>;
                    <?php } else { ?>
                    window.history.back();
                    <?php } ?>
                });
            });
            </script>
		</DIV>
	</TD>
</TR>
</TABLE>
</CENTER>
</FORM>
</p>
<?php camp_html_copyright_notice(); ?>
