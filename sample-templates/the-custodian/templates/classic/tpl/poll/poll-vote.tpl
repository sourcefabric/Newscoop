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

        <div style="width:260px;font-size:14px;font-family:Arial, sans-serif;"><span><b>{{ if $gimme->language->name == "English" }}Poll{{ else }}Encuesta{{ /if }}</b></span></div>
        <div style="width:260px;font-size:12px;font-family:Arial, sans-serif;">

{{ if $gimme->poll_action->ok }}
        
            <p>{{ if $gimme->language->name == "English" }}Thank you for voting{{ else }}Gracias por votar{{ /if }}</p>
            {{ assign var='display_poll_result' value=true }}
        
{{ else $gimme->poll_action->is_error }}
        
           {{* <p>Error:  $gimme->poll_action->error_message </p>*}}
            {{ assign var='display_poll_result' value=true }}
{{ /if }}

        {{ if $gimme->poll->user_vote_count >= $gimme->poll->votes_per_user }}
            <p>{{ if $gimme->language->name == "English" }}You can not vote again{{ else }}Usted no puede votar de nuevo{{ /if }}</p>
        {{ /if }}

    {{ if $display_poll_result }}
    
       <p>{{ if $gimme->language->name == "English" }}Poll results{{ else }}Resultados de la encuesta{{ /if }}: {{ $gimme->poll->votes }}</p><br />
        {{ list_poll_answers }} 
            {{ $gimme->pollanswer->percentage|string_format:"%d" }}%: {{ $gimme->pollanswer->answer }}
            <div style="clear:left;width:{{ $gimme->pollanswer->percentage|string_format:"%d" }}%;background-color: #336699">&nbsp;</div>
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