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


{{ poll_form template='poll/poll-form-ajax.tpl' submit_button=false ajax=true }} 
       
       
    {{ $gimme->poll->title }}<br>
    {{*
    Question: {{ $gimme->poll->question }}<br>
    Voting Begin: {{ $gimme->poll->date_begin|date_format }}<br>
    Voting End: {{ $gimme->poll->date_end|date_format }}<br>
    *}}
    Votes: {{ $gimme->poll->votes }}<br>
    
    <div style="height: 10px;" /></div>

    
    {{ list_poll_answers }}
       
         {{ pollanswer_ajax }}
         
	          <div class="poll_bar" style="width:{{ $gimme->pollanswer->percentage }}%;" /></div>
	          <div style="position: absolute">
	          	{{ $gimme->pollanswer->answer }}
	          	({{ $gimme->pollanswer->percentage|string_format:"%d" }})%
	         </div>
	          
        {{ /pollanswer_ajax }}
        
        <div style="clear: both"></div>
        {{ list_pollanswer_attachments }}
            {{ if $gimme->attachment->mime_type|substr:0:5 == 'audio' }}
                <a href="javascript: void(0);" onClick="play('{{ uri options="articleattachment" }}')">
                    <img src="/css/is_shown.png" border="0">
                </a>
                <a href="javascript: void(0);" onClick="stop()">
                    <img src="/css/unlink.png" border="0">
                </a>
            {{ /if }}
        {{ /list_pollanswer_attachments }}
        
		<div style="clear: both"></div>
		
		{{ if $gimme->poll->is_votable }}
		Give a note: 
            {{ section name=foo start=1 loop=6 }}
                {{ pollanswer_ajax value=$smarty.section.foo.index }}{{ $smarty.section.foo.index }}{{ /pollanswer_ajax }}
            {{ /section }}
            <br>
        {{ /if }}
            
        {{ $gimme->pollanswer->votes }} votes |
        &#216;{{ $gimme->pollanswer->average_value|string_format:"%.1f" }} |
        sum: {{ $gimme->pollanswer->value }}
        
        <div style="clear: both; height: 10px"></div>

    {{ /list_poll_answers }}
    
    {{ if !$gimme->poll->is_votable }}
        You reached max_vote_count, or this poll has expired.
    {{ /if }}
           
{{ /poll_form }}
 

{{ if $included }}
	</div>
	<div id="player_div"></div>
{{ /if }}
