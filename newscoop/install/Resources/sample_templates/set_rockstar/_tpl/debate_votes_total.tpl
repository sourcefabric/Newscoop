{{ include file="_tpl/debate-answers.tpl" scope="parent" }}

{{ capture name="votes" }}
    <ul class="debatte-score">
        {{ strip }}
        {{ foreach $answers as $answer }}
        <li style="width:{{ $answer.percent }}%;" class="{{ if $answer@first }}yes{{ else }}no{{ /if }}"><span><b>{{ $answer.answer|escape }}</b> {{ $answer.percent }}%</span></li>
        {{ /foreach }}
        {{ /strip }}
    </ul>
{{ /capture }}

{{ if !$gimme->debate->is_votable }}
    {{ $smarty.capture.votes }}
    <small>{{ if $gimme->debate->is_current && !$gimme->user->logged_in }}Current result{{ else }}Final result{{ /if }}</small>
{{ /if }}
