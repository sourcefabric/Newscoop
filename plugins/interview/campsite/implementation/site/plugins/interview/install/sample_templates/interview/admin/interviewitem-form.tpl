{{ interviewitem_form }}
<input type="hidden" name="action" value="list_items">
<table border=0>
    <tr><td width="200">Question</td><td>{{ interviewitem_edit attribute='question' }}</td></tr>
    <tr><td>Status</td><td>{{ interviewitem_edit attribute='status' }}</td></tr>
    <tr><td>Answer</td><td>{{ interviewitem_edit attribute='answer' }}</td></tr>
</table>
{{ /interviewitem_form }}