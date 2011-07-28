{{strip}}
{{ if ! $gimme->article->content_accessible }}
<span class="premium">Premium</span>
{{*<img class="a-icon" alt="Article locked" src="{{ url static_file='_img/icons/locked.png' }}" />*}}
{{ /if }}

{{ list_article_images length="1" }}
{{if $gimme->current_list->count > 2}}
<img class="a-icon" alt="This article has photo gallery" src="{{ url static_file='_img/icons/photo.png' }}" />
{{ /if }}
{{ /list_article_images }}

{{ assign var="has_audio" value="0" }}
{{ list_article_attachments }}
{{ if $gimme->attachment->extension == mp3 }}
{{ assign var="has_audio" value="1" }}
{{ /if }}
{{ /list_article_attachments }}

{{ if $has_audio == "1" }}<img class="a-icon" alt="This article has an audio attachment" src="{{ url static_file='_img/icons/audio.png' }}" />{{ /if }}

{{ assign var="has_video" value="0" }}
{{ list_article_attachments }}
{{ if ($gimme->attachment->extension == mpg) || ($gimme->attachment->extension == flv) || ($gimme->attachment->extension == avi) || ($gimme->attachment->extension == wmf) }}
{{ assign var="has_video" value="1" }}
{{ /if }}
{{ /list_article_attachments }}

{{ if $has_video == "1" }}<img class="a-icon" alt="This article has a video attachment" src="{{ url static_file='_img/icons/video.png' }}" />{{ /if }}
{{/strip}}