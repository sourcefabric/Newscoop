<div style="width: 250px; border: 1px solid #000; padding: 6px">
     
       
    {{ $gimme->debate->title }}<br>
    Question: {{ $gimme->debate->question }}<br>
    Voting Begin: {{ $gimme->debate->date_begin|date_format }}<br>
    Voting End: {{ $gimme->debate->date_end|date_format }}<br>
    Votes: {{ $gimme->debate->votes }}<br>
    
    <div style="height: 10px;" /></div>
    

    {{ if $gimme->debate_action->defined }}
    
        {{ if $gimme->debate_action->ok }}
        
            Thanks for your vote.<p>
            {{ assign var='display_debate_result' value=true }}
        
        {{ elseif $gimme->debate_action->is_error }}
        
            Following error occoured: {{ $gimme->debate_action->error_message }}
            {{ assign var='display_debate_form' value=true }}

        {{ /if }}
        
    {{ elseif $gimme->debate->is_votable }}
    
        {{ assign var='display_debate_form' value=true }}
        
    {{ else }}
    
        You reached max_vote_count, or this debate has expired.<p>
        {{ assign var='display_debate_result' value=true }}
        
    {{ /if }}  
    
    
    {{ if $display_debate_form }}
    
        {{ debate_form template='debate/section-debates.tpl' submit_button='submit' }} 
            {{ list_debate_answers }} 
                {{ debateanswer_edit }} {{ $gimme->debateanswer->answer }}
                <br>
            {{ /list_debate_answers }}
        {{ /debate_form }}
        
    {{ /if }}
    
    {{ if $display_debate_result }}
    
        Result:<br>
        {{ list_debate_answers order="byvalue desc" }} 
            {{ $gimme->debateanswer->percentage }}%: {{ $gimme->debateanswer->answer }}
            <br>
        {{ /list_debate_answers }}
        
    {{ /if }}
    
</div>
