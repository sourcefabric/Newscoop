{{ config_load file="{{ $gimme->language->english_name }}.conf" section="poll" }}

{{ list_articles length="1" ignore_issue="true" ignore_section="true" constraints="type is poll" }}

{{ list_debates length="1" item="article" }}
{{ if $gimme->current_list->at_beginning }}
<div id="polldiv" class="clearfix">
	<h3>{{ #pollTitle# }}</h3>
{{ /if }}

{{ if $gimme->debate_action->defined }}
                        <blockquote>{{ $gimme->debate->question }}</blockquote>
    {{ if $gimme->debate->user_vote_count >= $gimme->debate->votes_per_user || $gimme->debate_action->ok }}
    <p class="poll-info">{{ #thankYouPoll# }}</p>
    {{ elseif $gimme->debate_action->is_error }}
    <p>{{ #alreadyVoted# }}</p>
    {{ /if }}                        
                        <ul class="question-list">
                        {{ assign var="votes" value=0 }}
                        {{ list_debate_answers }}
                          <li>
                              <label for="radio{{ $gimme->current_list->index }}">{{ $gimme->debateanswer->answer }}</label>
                              <span class="q-score" style="width:{{ math equation="round(x)" x=$gimme->debateanswer->percentage_overall format="%d" }}%;"> <small>{{ math equation="round(x)" x=$gimme->debateanswer->percentage_overall format="%d" }}%</small></span>
                            </li>
                            {{ assign var="votes" value=$votes+$gimme->debateanswer->votes }}
                            {{ if $gimme->current_list->at_end }}
                            <li class="total-votes"><span>{{ #numberOfVotes# }} {{ $votes }}</span></li>
                            {{ /if }} 
                        {{ /list_debate_answers }}

                        </ul>

{{ else }}
  
   {{ if $gimme->debate->is_votable }}
   
                        <blockquote>{{ $gimme->debate->question }}</blockquote> 
                        {{ debate_form template="_tpl/sidebar-poll.tpl" submit_button="{{ #pollButton# }}" html_code="id=\"poll-button\" class=\"button debbut center\"" }}  
                        
{{* this is to find out template id for this template, will have to be assigned as hidden form field *}}     
{{ $uriAry=explode("tpl=", {{ uri options="template _tpl/sidebar-poll.tpl" }}, 2) }}                        

                        <input name="tpl" value="{{ $uriAry[1] }}" type="hidden">
                        <ul class="question-list">
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
                        <blockquote>{{ $gimme->debate->question }}</blockquote> 
                        {{ if $gimme->debate->user_vote_count >= $gimme->debate->votes_per_user }}<p class="poll-info">{{ #thankYouPoll# }}</p>{{ /if }}  
                        <ul class="question-list">
                        {{ list_debate_answers }}
                          <li>
                              <label for="radio{{ $gimme->current_list->index }}">{{ $gimme->debateanswer->answer }}</label>
                              <span class="q-score" style="width:{{ math equation="round(x)" x=$gimme->debateanswer->percentage_overall format="%d" }}%;"> <small>{{ math equation="round(x)" x=$gimme->debateanswer->percentage_overall format="%d" }}%</small></span>
                          </li>
                        {{ /list_debate_answers }}
                        </ul>    
   {{ /if }}
   
{{ /if }}   

{{ if $gimme->current_list->at_end }}
</div>
{{ /if }}
{{ /list_debates }}

{{ /list_articles }}
