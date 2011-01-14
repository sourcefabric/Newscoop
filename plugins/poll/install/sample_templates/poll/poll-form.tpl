<div style="width: 250px; border: 1px solid #000; padding: 6px">
     
       
    {{ $gimme->poll->title }}<br>
    Question: {{ $gimme->poll->question }}<br>
    Voting Begin: {{ $gimme->poll->date_begin|date_format }}<br>
    Voting End: {{ $gimme->poll->date_end|date_format }}<br>
    Votes: {{ $gimme->poll->votes }}<br>
    
    <div style="height: 10px;" /></div>
    

    {{ if $gimme->poll_action->defined }}
    
        {{ if $gimme->poll_action->ok }}
        
            Thanks for your vote.<p>
            {{ assign var='display_poll_result' value=true }}
        
        {{ elseif $gimme->poll_action->is_error }}
        
            Following error occoured: {{ $gimme->poll_action->error_message }}
            {{ assign var='display_poll_form' value=true }}

        {{ /if }}
        
    {{ elseif $gimme->poll->is_votable }}
    
        {{ assign var='display_poll_form' value=true }}
        
    {{ else }}
    
        You reached max_vote_count, or this poll has expired.<p>
        {{ assign var='display_poll_result' value=true }}
        
    {{ /if }}  
    
    
    {{ if $display_poll_form }}
    
        {{ poll_form template='poll/section-polls.tpl' submit_button='submit' }} 
            {{ list_poll_answers }} 
                {{ pollanswer_edit }} {{ $gimme->pollanswer->answer }}
                <br>
            {{ /list_poll_answers }}
        {{ /poll_form }}
        
    {{ /if }}
    
    {{ if $display_poll_result }}
    
        Result:<br>
        {{ list_poll_answers order="byvalue desc" }} 
            {{ $gimme->pollanswer->percentage }}%: {{ $gimme->pollanswer->answer }}
            <br>
        {{ /list_poll_answers }}
        
    {{ /if }}
    
</div>
