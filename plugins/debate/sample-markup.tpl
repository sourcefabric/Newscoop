{{list_debates length="1" order="bynumber desc"}}

	{{if $gimme->debate->is_votable}}
 		{{debate_form template="debate.tpl" submit_button=false}}
 			<ul class="vote-results">
  				{{list_debate_answers order="bynumber asc"}}
				<li>
					<em>heute stimmten</em>
					{{$gimme->debateanswer->percentage|string_format:"%d"}}% fur
					<a onclick="$('#answer-{{$gimme->debateanswer->number}}').trigger('click'); return false;" href="javascript:void(0)">
						{{$gimme->debateanswer->answer}}
					</a>
					<!-- f_debateanswer_nr name mandatory -->
					<span style="display: none">
    					<input type="radio" name="f_debateanswer_nr"
    						value="{{$gimme->debateanswer->number}}" id="answer-{{$gimme->debateanswer->number}}"
    						onclick="$('#submit-debate').trigger('click');"
    					/>
					</span>
				</li>
	  			{{/list_debate_answers}}
  			</ul>
 			<input type="submit" id="submit-debate" class="button" value="I think so!" style="display:none" />
		{{/debate_form}}
	{{/if}}

	<ul class="vote-stat clearfix">
		<li>
			{{list_debate_answers order="bynumber asc"}}
				<div class="voteheader">{{$gimme->debateanswer->answer}}</div>
    		{{/list_debate_answers}}
		</li>
    	{{list_debate_days length="7"}}
    	<li style="height:100px">
    		<!-- the list of votes, numbering in 2 usually -->
    		{{list_debate_votes}}
    		<div
    			class="{{if $gimme->debatevotes->number % 2}}gray{{/if}}"
    			style="height: {{$gimme->debatevotes->percentage|string_format:"%.2f"}}%;"></div>
        		{{if $gimme->debatevotes->number % 2}}
        			<em>{{$gimme->debatevotes->percentage|string_format:"%.0f"}}%</em>
        		{{else}}
        			<small>{{$gimme->debatevotes->percentage|string_format:"%.0f"}}%</small>
        		{{/if}}
        	{{/list_debate_votes}}
        	<span>{{$gimme->debatedays->time|date_format:"%a"}}.</span>
    	</li>
    	{{/list_debate_days}}
	</ul>

	{{if !$gimme->debate->is_votable}}
		 <ul class="vote-score clearfix">
		 	{{list_debate_answers order="bynumber asc"}}
        	<li style="height:100px">
				<div class="gray" style="height:{{$gimme->debateanswer->percentage|string_format:"%d"}}%;"></div>
                <em>{{$gimme->debateanswer->percentage|string_format:"%d"}}%</em>
                <span>{{$gimme->debateanswer->answer}}</span>
            </li>
            {{/list_debate_answers}}
        </ul>
	{{/if}}

{{/list_debates}}