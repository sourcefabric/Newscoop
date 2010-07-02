<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Poll</title>
</head>
<body style="padding:5px;">
<div style="width:305px;">
<!-- poll -->
      <div>

        <div style="width:260px;font-size:14px;font-family:Arial, sans-serif;"><span><b>Poll</b></span></div>
        <div style="width:260px;font-size:12px;font-family:Arial, sans-serif;">

{{ if $campsite->poll_action->ok }}
        
            <p>Thank you for voting</p>
            {{ assign var='display_poll_result' value=true }}
        
{{ else $campsite->poll_action->is_error }}
        
           {{* <p>Error:  $campsite->poll_action->error_message </p>*}}
            {{ assign var='display_poll_result' value=true }}

{{ /if }}
        {{ if $campsite->poll->user_vote_count >= $campsite->poll->votes_per_user }}
            <p>You can not vote again</p>
        {{ /if }}

    {{ if $display_poll_result }}
    
       <p>Poll results: {{ $campsite->poll->votes }}</p><br />
        {{ list_poll_answers }} 
            {{ $campsite->pollanswer->percentage|string_format:"%d" }}%: {{ $campsite->pollanswer->answer }}
            <div style="clear:left;width:{{ $campsite->pollanswer->percentage|string_format:"%d" }}%;background-color: #336699">&nbsp;</div>
            <br>
        {{ /list_poll_answers }}
        
    {{ /if }}

        </div>
        <div class="bottom-blank"><span>&nbsp;</span></div>
      </div>
      <!-- end poll -->
</div>
</body>
</html>