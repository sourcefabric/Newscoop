<h4>{{ $smarty.template }}</h4>

<table border=1 width=100%>
    <tr><td width=150>InterviewId</td><td>{{ $campsite->interview->identifier }}</td></tr>
    <tr><td>Title</td>
        <td>
            {{ $campsite->interview->title }}
            
            {{ if $show_actions }}
                <div align="right">
                
                {{ strip }}
                {{ if $campsite->user->has_permission('plugin_interview_admin') }}
                    <a href="{{ uripath }}?action=interview_edit&amp;f_interview_id={{ $campsite->interview->identifier }}">
                        <input type="Button" value="edit">
                    </a>
                    &nbsp;&nbsp;
                {{ /if }}
                    
                <a href="{{ uripath }}?interview_id={{ $campsite->interview->identifier }}">
                    <input type="Button" value="list items">
                </a>
                {{ /strip }}
            {{ /if }}
        </td>
    </tr>
    <tr><td>Language</td><td>{{ $campsite->interview->language->Name }}</td></tr>
    <tr><td>Moderator</td><td>{{ $campsite->interview->moderator->Name }}</td></tr>
    <tr><td>Guest</td><td>{{ $campsite->interview->guest->Name }}</td></tr>
    <tr><td>Thumbnail</td><td>{{ if $campsite->interview->image->thumbnailurl }}<img src="{{ $campsite->interview->image->thumbnailurl }}">{{ /if }}</td></tr>
    <tr><td>Description (short)</td><td>{{ $campsite->interview->description_short }}</td></tr>
    <tr><td>Description</td><td>{{ $campsite->interview->description }}</td></tr>
    <tr><td>Interview Begin</td><td>{{ $campsite->interview->interview_begin|date_format }}</td></tr>
    <tr><td>Interview End</td><td>{{ $campsite->interview->interview_end|date_format }}</td></tr>
    <tr><td>Questions Begin</td><td>{{ $campsite->interview->questions_begin|date_format }}</td></tr>
    <tr><td>Questions Begin</td><td>{{ $campsite->interview->questions_begin|date_format }}</td></tr>
    <tr><td>Questions Limit</td><td>{{ $campsite->interview->questions_limit }}</td></tr>
    <tr>
        <td>Status</td>
        <td>
            {{ $campsite->interview->status }}
            
            {{ if $show_actions }}
              <div align="right">
                {{ if $campsite->interview->status == 'draft' }}
                    <a href="{{ uripath }}?f_interviewstatus=pending&amp;f_interview_id={{ $campsite->interview->identifier }}">
                        <input type="button" value="invite now">
                    </a>
                {{ elseif $campsite->interview->status == 'pending' }}
                    <a href="{{ uripath }}?f_interviewstatus=published&amp;f_interview_id={{ $campsite->interview->identifier }}">
                        <input type="button" value="set published">
                    </a>
                {{ elseif $campsite->interview->status == 'published' }}
                    <a href="{{ uripath }}?f_interviewstatus=offline&amp;f_interview_id={{ $campsite->interview->identifier }}">
                        <input type="button" value="set offline">
                    </a>
                {{ elseif $campsite->interview->status == 'offline' }}
                    <a href="{{ uripath }}?f_interviewstatus=draft&amp;f_interview_id={{ $campsite->interview->identifier }}">
                        <input type="button" value="set draft">
                    </a>
                {{ /if }}
                
              </div>
            {{ /if }}
        </td>
    </tr>
</table>