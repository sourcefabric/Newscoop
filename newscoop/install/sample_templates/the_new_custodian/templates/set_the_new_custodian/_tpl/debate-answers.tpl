{{ $answers = array() }}
{{ list_debate_answers order="bynumber asc" }}
    {{ if empty($answers) }}
        {{ $percent = floor($gimme->debateanswer->percentage) }}
    {{ else }}
        {{ $percent = ceil($gimme->debateanswer->percentage) }}
    {{ /if }}
    {{ $answers[] = ['answer' => $gimme->debateanswer->answer, 'percent' => $percent] }}
{{ /list_debate_answers }}
