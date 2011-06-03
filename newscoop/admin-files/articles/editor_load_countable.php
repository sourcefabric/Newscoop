<!-- Load TinyMCE -->
<link rel="stylesheet" type="text/css" href="<?php echo $Campsite['WEBSITE_URL']; ?>/js/jquery/jCountable/jquery.jCountable.css" />
<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/js/jquery/jCountable/jquery.jCountable.js"></script>
<script type="text/javascript">
$().ready(function() {
	$('#f_article_title').jCountable(
			{
				errorLength: 140,
				message: { container: $('#f_article_count') }
			});
	$('.countableft').jCountable(
			{
				message: {
					className: 'j-countable ft'
				}
			});
	$('.countable').jCountable();
});
</script>
