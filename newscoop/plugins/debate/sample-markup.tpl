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
 			<ul>
  				{{list_debate_answers order="bynumber asc"}}
				<li>
					some text here
					<em>{{$gimme->debateanswer->percentage|string_format:"%d"}}%</em>
					<a onclick="$('#answer-{{$gimme->debateanswer->number}}').trigger('click'); return false;" href="javascript:void(0)">
						{{$gimme->debateanswer->answer}}
					</a>
					<!-- f_debateanswer_nr name mandatory -->
					<input type="radio" name="f_debateanswer_nr"
						value="{{$gimme->debateanswer->number}}" id="answer-{{$gimme->debateanswer->number}}"
						onclick="$('#submit-debate').trigger('click');"
					/>
				</li>
	  			{{/list_debate_answers}}
  			</ul>
 			<input type="submit" id="submit-debate" class="button" value="I think so!" style="display:none" />
		{{/debate_form}}
	{{else}}
		<ul>
    		{{list_debate_answers order="bynumber asc"}}
    		<li style="height:100px">
				<div style="height: {{$gimme->debateanswer->percentage|string_format:"%d"}}%;" class="gray"></div>
                <em>{{$gimme->debateanswer->percentage|string_format:"%d"}}%</em>
                <span>{{$gimme->debateanswer->answer}}</span>
            </li>
    		{{/list_debate_answers}}
		</ul>
   	{{/if}}

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
			<strong>{{$gimme->debatedays->time|date_format:"%b %e"}}</strong>
		</div>
	{{/list_debate_days}}
 	<div style="clear: both"></div>
	</div>

{{/list_debates}}