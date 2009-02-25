
{{ if $campsite->issue->is_current }}{{ else }}{{ local }}{{ set_current_issue }}<p><b>&larr;</b> <a href="/tpl/fastnews/issue.tpl?{{ urlparameters options="issue" }}">Current issue #{{ $campsite->issue->number }}<a></p>{{ /local }}{{ /if }}

<h3><a href="/tpl/fastnews/issue.tpl?{{ urlparameters options="issue" }}">{{ if $campsite->issue->is_current }}Current Issue {{ else }}{{ $campsite->issue->name }}{{ /if }}(#{{ $campsite->issue->number }})</a></h3>

