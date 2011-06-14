<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Poll results</title>
</head>
<body style="padding:5px;">
<div style="width:305px;">
<!-- poll -->
      <div>

        <div style="width:260px;font-size:14px;font-family:Arial, sans-serif;"><span><b>{{ if $gimme->language->name == "English" }}Poll results{{ else }}Resultados de la encuesta{{ /if }}</b></span></div>
        <div style="width:260px;font-size:12px;font-family:Arial, sans-serif;">

{{ assign var='display_poll_result' value=true }}

    {{ if $display_poll_result }}
    
       <p><strong>{{ $gimme->poll->question }}</strong><br/>>{{ if $gimme->language->name == "English" }}Votes{{ else }}Votos{{ /if }}: {{ $gimme->poll->votes }}</p><br />
        {{ list_poll_answers order="byvalue desc" }} 
            {{ $gimme->pollanswer->percentage|string_format:"%d" }}%: {{ $gimme->pollanswer->answer }}
            <div style="clear:left;width:{{ $gimme->pollanswer->percentage|string_format:"%d" }}%;background-color: #369"">&nbsp;</div>
            <br />
        {{ /list_poll_answers }}
        
    {{ /if }}
</div>
</body>
</html>