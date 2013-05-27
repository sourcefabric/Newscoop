{{ include file="_tpl/debate-answers.tpl" scope="parent" }}

{{ capture name="votes" }}
    <div class="debate-score">
        {{ strip }}
        {{ foreach $answers as $answer }}
        <div class="debate-value">
	    	<div class="progress progress-danger progress-striped debate-bar">
				<div class="bar" style="width: {{ $answer.percent }}%"></div>
			</div>
	        <span><b>{{ $answer.answer|escape }}</b> {{ $answer.percent }}%</span>
    	</div>
        {{ /foreach }}
        {{ /strip }}
    </div>
{{ /capture }}

{{ if !$gimme->debate->is_votable }}
    {{ $smarty.capture.votes }}
    <small>{{ if $gimme->debate->is_current && !$gimme->user->logged_in }}{{ #currentResult# }}{{ else }}{{ #finalResult# }}{{ /if }}</small>
{{ /if }}
