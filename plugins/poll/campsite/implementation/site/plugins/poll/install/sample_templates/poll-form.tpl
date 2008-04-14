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


<div style="width: 250px; border:1px solid #000; padding: 6px">
{{ /if }}


{{ poll_form template='poll-form.tpl' submit_button=false ajax=true }} 
   
    {{ $campsite->poll->title }}<br>
    {{*
    Question: {{ $campsite->poll->question }}<br>
    Voting Begin: {{ $campsite->poll->date_begin|date_format }}<br>
    Voting End: {{ $campsite->poll->date_end|date_format }}<br>
    *}}
    Votes: {{ $campsite->poll->votes }}<br>
    
    <div style="height: 10px;" /></div>
    
    {{ list_poll_answers order="byaverage_value desc"}}
       
         {{ pollanswer_ajax }}
         
	          <div class="poll_bar" style="width:{{ $campsite->pollanswer->percentage }}%; position:absulute" /></div>
	          <div style="position: absolute">
	          	{{ $campsite->pollanswer->answer }}
	          	({{ $campsite->pollanswer->percentage|string_format:"%d" }})%
	         </div>
	          
        {{ /pollanswer_ajax }}
        
		<div style="clear: both"></div>
        
		Stars: 
        {{ section name=foo start=1 loop=6 }}
            {{ pollanswer_ajax value=$smarty.section.foo.index }}{{ $smarty.section.foo.index }}{{ /pollanswer_ajax }}
        {{ /section }}
        
        ({{ $campsite->pollanswer->average_value|string_format:"%.1f" }})
        
        <div style="clear: both; height: 10px"></div>


    {{ /list_poll_answers }}
           
{{ /poll_form }}
 

{{ if $included }}
	</div>
{{ /if }}