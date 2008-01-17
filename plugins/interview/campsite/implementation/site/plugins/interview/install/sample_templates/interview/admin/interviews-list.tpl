<h6>{{ $smarty.template }}</h6>

<form name="status">
  <select name="filter_interview_status" onchange="location.href='{{ uripath }}?filter_interview_status='+document.forms['status'].elements['filter_interview_status'].value">
    <option value="">Filter status:</option>
    <option value="draft" {{ if $smarty.request.filter_interview_status == 'draft' }}selected{{ /if }}>draft</option>
    <option value="pending" {{ if $smarty.request.filter_interview_status == 'pending' }}selected{{ /if }}>pending</option>
    <option value="published" {{ if $smarty.request.filter_interview_status == 'published' }}selected{{ /if }}>published</option>
    <option value="offline" {{ if $smarty.request.filter_interview_status == 'offline' }}selected{{ /if }}>offline</option>
  </select>
</form>

<table border="1" width="100%">
    <tr><th>Title</th><th>Status</th><th>Moderator</th><th>Guest</th></tr>
    
    {{ if $smarty.request.filter_interview_status }}
        {{ assign var='_constraints' value="status is `$smarty.request.filter_interview_status`" }}
    {{ /if }}
    
    {{ list_interviews length=10 constraints=$_constraints }}
        <tr>
            <td><a href="{{ uripath }}?f_interview_id={{ $campsite->interview->identifier }}">{{ $campsite->interview->title }}</a></td>
            <td>{{ $campsite->interview->status }}</td>
            <td>{{ $campsite->interview->moderator->name }}</td>
            <td>{{ $campsite->interview->guest->name }}</td>
    {{ /list_interviews }}   
</table> 