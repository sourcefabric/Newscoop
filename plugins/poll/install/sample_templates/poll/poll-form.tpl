<div style="width: 250px; border: 1px solid #000; padding: 6px">
     
       
    {{ $campsite->poll->title }}<br>
    Question: {{ $campsite->poll->question }}<br>
    Voting Begin: {{ $campsite->poll->date_begin|date_format }}<br>
    Voting End: {{ $campsite->poll->date_end|date_format }}<br>
    Votes: {{ $campsite->poll->votes }}<br>
    
    <div style="height: 10px;" /></div>
    

    {{ if $campsite->poll_action->defined }}
    
        {{ if $campsite->poll_action->ok }}
        
            Thanks for your vote.<p>
            {{ assign var='display_poll_result' value=true }}
        
        {{ elseif $campsite->poll_action->is_error }}
        
            Following error occoured: {{ $campsite->poll_action->error_message }}
            {{ assign var='display_poll_form' value=true }}

        {{ /if }}
        
    {{ elseif $campsite->poll->is_votable }}
    
        {{ assign var='display_poll_form' value=true }}
        
    {{ else }}
    
        This poll expired.<p>
        {{ assign var='display_poll_result' value=true }}
        
    {{ /if }}  
    
    
    {{ if $display_poll_form }}
    
        {{ poll_form template='poll/section-polls.tpl' submit_button='submit' }} 
            {{ list_poll_answers }} 
                {{ pollanswer_edit }}
                   {{ $campsite->pollanswer->answer }}
                {{ /pollanswer_edit }}
                <br>
            {{ /list_poll_answers }}
        {{ /poll_form }}
        
    {{ /if }}
    
    {{ if $display_poll_result }}
    
        Result:<br>
        {{ list_poll_answers order="byvalue desc" }} 
            {{ $campsite->pollanswer->percentage }}%: {{ $campsite->pollanswer->answer }}
            <br>
        {{ /list_poll_answers }}
        
    {{ /if }}
    
</div>
