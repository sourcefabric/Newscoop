{{ include file="html_header.tpl" }}
<table class="main" cellspacing="0" cellpadding="0">
<tr>
  <td valign="top">
    <div id="breadcrubm">
    {{ breadcrumb }}
    </div>
    {{** main content area **}}
    <table class="content" cellspacing="0" cellpadding="0">
    <tr>
      <td>

    <hr>
    <h4>Poll-List</h4>     
    {{ list_polls name="polls_list" length="5" item=$smarty.get.poll_item order="bybegin DESC" constraints="begin greater 2007-01-01" }}
       <li>poll: <b>{{ $campsite->current_polls_list->current->name }}</b>/<b>{{ $campsite->current_list->current->name }}</b>,
       list index: <b>{{ $campsite->current_polls_list->getIndex() }}</b>/<b>{{ $campsite->current_list->getIndex() }}</b>,
       column: <b>{{ $campsite->current_polls_list->getColumn() }}</b>/<b>{{ $campsite->current_list->getColumn() }}</b>
       (current polls list/current list)
       <a href="?poll_nr={{ $campsite->current_polls_list->current->number }}&amp;poll_language_id={{ $campsite->current_polls_list->current->language_id }}">display</a>
    {{ /list_polls }}
    <br>
    total count: {{ $campsite->current_polls_list->count }}
    
    <hr>
    
    {{ if $campsite->poll->in_time }}
    
        <h3>Poll Form</h3>
        
        
        {{* 
        <form name="poll_{{ $campsite->poll->identifier }}">
            {{ $campsite->poll->form_hidden }}
        *}}
        
        {{ poll_form template=""}}    
            Title: {{ $campsite->poll->title }}<br>
            Question: {{ $campsite->poll->question }}<br>
            <br>
            
            {{ if $campsite->poll->votable }}
            {{ list_poll_answers }}
                {{ $campsite->current_pollanswers_list->current->form_radio }}
                
                {{* 
                like:
                <input type="radio" name="{{ $campsite->current_pollanswers_list->current->identifier }}" >
                *}}
                {{ $campsite->current_pollanswers_list->current->answer }}<br>
            {{ /list_poll_answers }}
        
            <input type="submit">
            <p>
            {{ /if }}
            
            {{ list_poll_answers }}
                {{ $campsite->current_pollanswers_list->current->nr_answer }}:
                {{ strip }}
                <img src="/css/mainbarlinks.png" width="1" height="10" />
                <img src="/css/mainbar.png" width="{{ $campsite->current_pollanswers_list->current->percentage }}" height="10px"/>
                <img src="/css/mainbarrechts.png" width="1" height="10" />
                {{ /strip }}
                ({{ $campsite->current_pollanswers_list->current->nr_of_votes }}/{{ $campsite->poll->nr_of_votes }} Votes, {{ $campsite->current_pollanswers_list->current->percentage }}%)
                <br>
            {{ /list_poll_answers }}
            
        {{*
        </form>
        *}}
       
        {{ /poll_form }}
        
    {{ /if }}
      
      
      </td>
    </tr>
    </table>
    {{** end main content area **}}
  </td>
  <td valign="top">
    {{ include file="html_rightbar.tpl" }}
  </td>
</tr>
</table>
{{ include file="html_footer.tpl" }}