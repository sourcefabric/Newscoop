{{ assign var="has_video" value="0" }}
{{ list_article_attachments }}
{{ if ($campsite->attachment->extension == mpg) || ($campsite->attachment->extension == flv) || ($campsite->attachment->extension == avi) || ($campsite->attachment->extension == wmf) }}
{{ assign var="has_video" value="1" }}
{{ /if }}
{{ /list_article_attachments }}

{{ if $has_video == "1" }}<img style="border: none; margin-right: 5px" alt="This article has a video attachment" src="/templates/classic/img/Video_32.png" />{{ /if }}