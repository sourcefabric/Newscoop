{{ interview_form }}
<table border=0>
    <tr><td width="200">Language</td><td>{{ interview_edit attribute='language' }}</td></tr>
    <tr><td width="200">Title</td><td>{{ interview_edit attribute='title' }}</td></tr>
    <tr><td>Description (short)</td><td>{{ interview_edit attribute='description_short' }}</td></tr>
    <tr><td>Description</td><td>{{ interview_edit attribute='description' }}</td></tr>
    <tr><td width="200">Moderator</td><td>{{ interview_edit attribute='moderator' }}</td></tr>
    <tr><td width="200">Guest</td><td>{{ interview_edit attribute='guest' }}</td></tr>
    <tr><td>Interview begin</td><td>{{ interview_edit attribute='interview_begin' }} (YYYY-MM-DD)</td></tr>
    <tr><td>Interview end</td><td>{{ interview_edit attribute='interview_end' }} (YY-MM-DD)</td></tr>
    <tr><td>Questions begin</td><td>{{ interview_edit attribute='questions_begin' }} (YY-MM-DD)</td></tr>
    <tr><td>Questions end</td><td>{{ interview_edit attribute='questions_end' }} (YY-MM-DD)</td></tr>
    <tr><td>Questions limit</td><td>{{ interview_edit attribute='questions_limit' }}</td></tr>
    <tr><td>Interview image</td><td>{{ interview_edit attribute='image' }}</td></tr>
    <tr><td>Image description</td><td>{{ interview_edit attribute='image_description' }}</td></tr>
    <tr><td>Delete image</td><td>{{ interview_edit attribute='image_delete' }}</td></tr>
</table>
{{ /interview_form }}