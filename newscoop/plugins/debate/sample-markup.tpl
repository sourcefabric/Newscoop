{{list_debates length="1" order="bynumber desc"}}

	{{if $gimme->debate->is_votable}}
 		{{debate_form template="poll.tpl" submit_button=false}}
  			{{list_debate_answers order="bynumber asc"}}
				{{debateanswer_edit}}
				{{$gimme->debateanswer->answer}} <br />
  			{{/list_debate_answers}}
 			<input type="submit" id="submit_debate" class="button" value="Vote!" />
		{{/debate_form}}
	{{/if}}

	{{list_debate_answers order="bynumber asc"}}
	<div class="answer">{{$gimme->debateanswer->percentage|string_format:"%d"}}%: {{ $gimme->debateanswer->answer }}
		<div style="width:{{$gimme->debateanswer->percentage|string_format:"%d"}}%;background:#5d4040;">&nbsp;</div>
    </div>
	{{/list_debate_answers}}

	{{list_debate_days length="3"}}
		{{list_debate_votes}}
			<div style="height:{{$gimme->debatevotes->percentage|string_format:"%.2f"}}%">
				{{$gimme->debatevotes->percentage|string_format:"%.2f"}}
			</div>
		{{/list_debate_votes}}
	{{/list_debate_days}}

{{/list_debates}}