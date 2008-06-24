<h3><!** Print Publication name>: Subscription page</h3>

<!** Subscription by_publication do_subscribe.tpl Submit>
<table>
	<!** if subscription paid>
	<tr><td colspan=2 align=left>Total time: <!** print subscription paidtime>&nbsp;<!** print subscription unit></td></tr>
	<tr><td colspan=2 align=left>Total cost: <!** print subscription totalcost>&nbsp;<!** print subscription currency></td></tr>
	<!** endif>
	<!** if subscription trial>
	<tr><td colspan=2 align=left>Total time: <!** print subscription trialtime>&nbsp;<!** print subscription unit></td></tr>
	<!** endif>
</table>
<!** EndSubscription>
