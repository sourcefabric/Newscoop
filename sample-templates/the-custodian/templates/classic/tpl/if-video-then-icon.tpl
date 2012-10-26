{{ assign var="has_video" value="0" }}
{{ list_article_attachments }}
{{ if ($gimme->attachment->extension == mpg) || ($gimme->attachment->extension == flv) || ($gimme->attachment->extension == avi) || ($gimme->attachment->extension == wmf) }}
{{ assign var="has_video" value="1" }}
{{ /if }}
{{ /list_article_attachments }}

{{ if $has_video == "1" }}<img style="border: none; margin-right: 5px" alt="This article has a video attachment" src="http://{{ $gimme->publication->site }}/templates/classic/img/Video_32.png" />{{ /if }}