<style type="text/css">
.results
{
	border: 1px solid #ccc;
	margin: 0 30px 30px 30px;
	box-shadow: 0 2px 2px rgba(0, 0, 0, 0.1);
	background-color: #f3f3f3;
}
    .results .item-def
    {
    	float: left;
    	border-right: 1px solid #ccc;
    	height: <?php echo count($answers)*30 + 20 ?>px;
    }
    	.results .item-def .value
		{
			height: <?php echo count($answers)*30 - 10 ?>px;
			padding: 5px 10px;
			border-bottom: 1px solid #ccc;
		}
    .results .item
    {
		width: 50px;
    	float: left;
    	height: <?php echo count($answers)*30 + 20 ?>px;
    	border-right: 1px solid #ccc;
    }
    	.results .item .value
		{
			height: <?php echo count($answers)*30 ?>px;
			border-bottom: 1px solid #ccc;
		}
		.results .item .bottom, .results .item-def .bottom
		{
			height: 20px;
			line-height: 20px;
			text-align: center;
			color: #888;
		}
    	.results .item .division
    	{
    		text-align: center;
		}
    	.results .item .division:nth-child(3n+1)
		{
  			background-color: #fff;
		}
		.results .item .division:nth-child(3n+2)
		{
  			background-color: #eef;
		}
		.results .item .division:nth-child(3n+3) {
  			background-color: #ddf;
		}
</style>

{{list_debates length="1" order="bynumber desc"}}

	{{if $gimme->debate->is_votable}}
 		{{debate_form template="debate.tpl" submit_button=false}}
  			{{list_debate_answers order="bynumber asc"}}
				{{debateanswer_edit}}
				{{$gimme->debateanswer->answer}} <br />
  			{{/list_debate_answers}}
 			<input type="submit" id="submit_debate" class="button" value="I think so!" />
		{{/debate_form}}
	{{/if}}

	{{list_debate_answers order="bynumber asc"}}
	<div class="answer">{{$gimme->debateanswer->percentage|string_format:"%d"}}%: {{ $gimme->debateanswer->answer }}
		<div style="width:{{$gimme->debateanswer->percentage|string_format:"%d"}}%;background:#5d4040;">&nbsp;</div>
    </div>
	{{/list_debate_answers}}

	<div class="results">
	{{list_debate_days length="10"}}
		<div class="item">
    		<div class="value">
			{{list_debate_votes}}
			<div class="division" style="height:{{$gimme->debatevotes->percentage|string_format:"%.2f"}}%">
				{{$gimme->debatevotes->percentage|string_format:"%.2f"}}%
			</div>
			{{/list_debate_votes}}
			</div>
		</div>
	{{/list_debate_days}}
 	<div style="clear: both"></div>
	</div>

{{/list_debates}}