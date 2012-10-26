{{ assign var="has_audio" value="0" }}
{{ list_article_attachments }}
{{ if ($gimme->attachment->extension == mp3) || ($gimme->attachment->extension == ogg) }}
{{ assign var="has_audio" value="1" }}
{{ /if }}
{{ /list_article_attachments }}

{{ if $has_audio == "1" }}<img style="border: none; margin-right: 5px" alt="This article has an audio attachment" src="http://{{ $gimme->publication->site }}/templates/classic/img/Speaker_32.png" />{{ /if }}