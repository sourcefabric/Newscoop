    {{ list_debates length="1" item="article" }}

          <div id="debate" class="clearfix score-box">
              <h3>{{ #debateVoting# }}</h3>

    {{ include file="_tpl/debate_votes_total.tpl" scope="parent" }}

    {{ if $gimme->debate->is_votable }}
        {{ $smarty.capture.votes }}
        {{ include file="_tpl/debate-deadline.tpl" }}
    {{ /if }}     


{{ if $gimme->default_article->defined }}
	<div class="vote-box">
	<div class="button-group">
    {{ if $gimme->debate->is_votable }}
    {{ debate_form template="article.tpl" submit_button=false }}
        {{ list_debate_answers order="bynumber asc" }}          
            
<a onclick="$('#answer-{{ $gimme->debateanswer->number }}').attr('checked','checked');$(this).parents('form:eq(0)').submit(); return false;" href="javascript:void(0)" class="button debbut">{{ $gimme->debateanswer->answer }}</a>            
            
            <!-- f_debateanswer_nr name mandatory -->
            <input type="radio" name="f_debateanswer_nr"
                value="{{ $gimme->debateanswer->number }}" id="answer-{{ $gimme->debateanswer->number }}"
                onclick="$(this).parents('form:eq(0)').submit();" style="display:none" />

        {{ /list_debate_answers }}
    <input type="submit" id="submit-debate" class="button" value="~" style="display:none" />
    {{ /debate_form }}
    {{ /if }}    
    
    </div>         
    </div>   
    {{ if $gimme->debate->is_votable }}<small>{{ #changeYourMind# }}</small>
    {{ elseif $gimme->user->logged_in or !$gimme->debate->is_current }}<small>{{ #debateClosed# }}</small>
    {{ elseif $gimme->debate->is_current && !$gimme->user->logged_in }}<small>{{ #pleaseLoginVote# }}</small>{{ /if }}      
{{ /if }}         

</div>
    {{ /list_debates }}                          