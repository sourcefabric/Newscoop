{{ config_load file="{{ $gimme->language->english_name }}.conf" section="poll" }}
{{ list_articles length="1" ignore_issue="true" ignore_section="true" constraints="type is poll" }}

{{ list_debates length="1" item="article" }}
{{ if $gimme->current_list->at_beginning }}
<article id="polldiv">
	<h2>{{ #pollTitle# }}</h2>
{{ /if }}

{{ if $gimme->debate_action->defined }}
                        <h4>{{ $gimme->debate->question }}</h4>
                        
                        <ul class="article-list">
                        {{ assign var="votes" value=0 }}
                        {{ list_debate_answers }}
                          <li>
                              <label for="radio{{ $gimme->current_list->index }}">{{ $gimme->debateanswer->answer }}</label>
                              <span class="q-score" style="width:{{ math equation="round(x)" x=$gimme->debateanswer->percentage_overall format="%d" }}%;"> <small>{{ math equation="round(x)" x=$gimme->debateanswer->percentage_overall format="%d" }}%</small></span>
                            </li>
                            {{ assign var="votes" value=$votes+$gimme->debateanswer->votes }}
                        {{ /list_debate_answers }}
    {{ if $gimme->debate->user_vote_count >= $gimme->debate->votes_per_user || $gimme->debate_action->ok }}
    <em class="poll-bottom">{{ #numberOfVotes# }} {{ $votes }}. {{ #thankYouPoll# }}</em>
    {{ elseif $gimme->debate_action->is_error }}
    <em class="poll-bottom">{{ #numberOfVotes# }} {{ $votes }}. {{ #alreadyVoted# }}</em>
    {{ /if }}
                        </ul>

{{ else }}
  
   {{ if $gimme->debate->is_votable }}
   
                        <h4>{{ $gimme->debate->question }}</h4> 
                        {{ debate_form template="_tpl/front-poll.tpl" submit_button="{{ #pollButton# }}" html_code="id=\"poll-button\" class=\"center\"" }}  
                        
{{* this is to find out template id for this template, will have to be assigned as hidden form field *}}     
{{ $uriAry=explode("tpl=", {{ uri options="template _tpl/front-poll.tpl" }}, 2) }}                        

                        <input name="tpl" value="{{ $uriAry[1] }}" type="hidden">
                        <ul class="article-list">
                        {{ list_debate_answers }}
                          <li>
                              <!--input type="radio" id="radio{{ $gimme->current_list->index }}" name="radios1" /-->
                              {{ debateanswer_edit html_code="id=\"radio{{ $gimme->current_list->index }}\"" }}<label for="radio{{ $gimme->current_list->index }}">{{ $gimme->debateanswer->answer }}</label>
                              <span class="q-score" style="width:{{ math equation="round(x)" x=$gimme->debateanswer->percentage_overall format="%d" }}%;"> <small>{{ math equation="round(x)" x=$gimme->debateanswer->percentage_overall format="%d" }}%</small></span>
                          </li>
                        {{ /list_debate_answers }}
                        </ul> 
                        {{ /debate_form }}                
                        
   {{ else }}                       
                        <h4>{{ $gimme->debate->question }}</h4>   
                        <ul class="article-list">
                        {{ list_debate_answers }}
                          <li>
                              <label for="radio{{ $gimme->current_list->index }}">{{ $gimme->debateanswer->answer }}</label>
                              <span class="q-score" style="width:{{ math equation="round(x)" x=$gimme->debateanswer->percentage_overall format="%d" }}%;"> <small>{{ math equation="round(x)" x=$gimme->debateanswer->percentage_overall format="%d" }}%</small></span>
                          </li>
                        {{ /list_debate_answers }}
                        </ul> 
                        {{ if $gimme->debate->user_vote_count >= $gimme->debate->votes_per_user }}<em class="poll-bottom">{{ #thankYouPoll# }}</em>{{ /if }}                           
   {{ /if }}
   
{{ /if }}   

{{ if $gimme->current_list->at_end }}
</article>
{{ /if }}
{{ /list_debates }}

{{ /list_articles }}