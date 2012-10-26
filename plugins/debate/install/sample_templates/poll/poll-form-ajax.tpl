{{ if $included }}

<style>
 .debate_bar {
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


{{ debate_form template='debate/debate-form-ajax.tpl' submit_button=false ajax=true }} 
       
       
    {{ $gimme->debate->title }}<br>
    {{*
    Question: {{ $gimme->debate->question }}<br>
    Voting Begin: {{ $gimme->debate->date_begin|date_format }}<br>
    Voting End: {{ $gimme->debate->date_end|date_format }}<br>
    *}}
    Votes: {{ $gimme->debate->votes }}<br>
    
    <div style="height: 10px;" /></div>

    
    {{ list_debate_answers }}
       
         {{ debateanswer_ajax }}
         
	          <div class="debate_bar" style="width:{{ $gimme->debateanswer->percentage }}%;" /></div>
	          <div style="position: absolute">
	          	{{ $gimme->debateanswer->answer }}
	          	({{ $gimme->debateanswer->percentage|string_format:"%d" }})%
	         </div>
	          
        {{ /debateanswer_ajax }}
        
        <div style="clear: both"></div>
        {{ list_debateanswer_attachments }}
            {{ if $gimme->attachment->mime_type|substr:0:5 == 'audio' }}
                <a href="javascript: void(0);" onClick="play('{{ uri options="articleattachment" }}')">
                    <img src="/css/is_shown.png" border="0">
                </a>
                <a href="javascript: void(0);" onClick="stop()">
                    <img src="/css/unlink.png" border="0">
                </a>
            {{ /if }}
        {{ /list_debateanswer_attachments }}
        
		<div style="clear: both"></div>
		
		{{ if $gimme->debate->is_votable }}
		Give a note: 
            {{ section name=foo start=1 loop=6 }}
                {{ debateanswer_ajax value=$smarty.section.foo.index }}{{ $smarty.section.foo.index }}{{ /debateanswer_ajax }}
            {{ /section }}
            <br>
        {{ /if }}
            
        {{ $gimme->debateanswer->votes }} votes |
        &#216;{{ $gimme->debateanswer->average_value|string_format:"%.1f" }} |
        sum: {{ $gimme->debateanswer->value }}
        
        <div style="clear: both; height: 10px"></div>

    {{ /list_debate_answers }}
    
    {{ if !$gimme->debate->is_votable }}
        You reached max_vote_count, or this debate has expired.
    {{ /if }}
           
{{ /debate_form }}
 

{{ if $included }}
	</div>
	<div id="player_div"></div>
{{ /if }}
