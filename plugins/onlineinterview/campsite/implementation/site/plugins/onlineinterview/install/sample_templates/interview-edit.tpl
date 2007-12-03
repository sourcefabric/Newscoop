{{ if $campsite->interview->store }}
        saved
{{ else }}
    {{ interview_form }}{{ /interview_form }}
{{ /if }}