{{ if $included }}

<style>
 .poll_bar {
    border:1px solid #000; 
    background-color: #dfdfdf; 
    height: 20px; 
    vertical-align: center;
    float: left;
}
</style>

<script>
function play(url)
{
    var tag;
    
    if (navigator.appName=="Microsoft Internet Explorer") {
        tag = '<bgsound src="'+url+'" loop="false">';
    } else {
        tag = '<embed src="'+url+'" hidden="true" border="0" width="0" height="0" autostart="true" loop="false">';
    }
    $('player_div').innerHTML = tag;    
}

function stop()
{
    $('player_div').innerHTML = '';   
}    
</script>

<div style="width: 250px; border: 1px solid #000; padding: 6px">
{{ /if }}


{{ poll_form template='poll-form.tpl' submit_button=false ajax=true }} 
       
    {{ if $smarty.request.onhitlist == 'y'}}
        {{ assign var='constraints' value='onhitlist is 1' }}
    {{ /if }}
    {{ if $smarty.request.onhitlist == 'n'}}
        {{ assign var='constraints' value='onhitlist is 0' }}
    {{ /if }}

       
    {{ $campsite->poll->title }}<br>
    {{*
    Question: {{ $campsite->poll->question }}<br>
    Voting Begin: {{ $campsite->poll->date_begin|date_format }}<br>
    Voting End: {{ $campsite->poll->date_end|date_format }}<br>
    *}}
    Votes: {{ $campsite->poll->votes }}<br>
    
    <div style="height: 10px;" /></div>

    
    {{ list_poll_answers order="byvalue desc" constraints=$constraints }}
       
         {{ pollanswer_ajax }}
         
	          <div class="poll_bar" style="width:{{ $campsite->pollanswer->percentage }}%;" /></div>
	          <div style="position: absolute">
	          	{{ $campsite->pollanswer->answer }}
	          	({{ $campsite->pollanswer->percentage|string_format:"%d" }})%
	         </div>
	          
        {{ /pollanswer_ajax }}
        
        <div style="clear: both"></div>
        {{ list_pollanswer_attachments }}
            {{ if $campsite->attachment->mime_type|substr:0:5 == 'audio' }}
                <a href="javascript: void(0);" onClick="play('{{ uri options="articleattachment" }}')">
                    <img src="/css/is_shown.png" border="0">
                </a>
                <a href="javascript: void(0);" onClick="stop()">
                    <img src="/css/unlink.png" border="0">
                </a>
            {{ /if }}
        {{ /list_pollanswer_attachments }}
        
		<div style="clear: both"></div>
		Give a note: 
        {{ section name=foo start=1 loop=6 }}
            {{ pollanswer_ajax value=$smarty.section.foo.index }}{{ $smarty.section.foo.index }}{{ /pollanswer_ajax }}
        {{ /section }}

        <br>
        
        {{ $campsite->pollanswer->votes }} votes |
        &#216;{{ $campsite->pollanswer->average_value|string_format:"%.1f" }} |
        sum: {{ $campsite->pollanswer->value }}
        
        <div style="clear: both; height: 10px"></div>

    {{ /list_poll_answers }}
           
{{ /poll_form }}
 

{{ if $included }}
	</div>
	<div id="player_div"></div>
{{ /if }}