{{ if ! $gimme->article->content_accessible }}<img class="a-icon" alt="{{ #articleLocked# }}" title="{{ #articleLocked# }}" src="{{ url static_file='_img/icons/locked.png' }}" />{{ /if }}

{{ assign var="has_slideshow" value=0 }}              
{{ foreach $gimme->article->slideshows as $slideshow }}     
{{ assign var="has_slideshow" value=$has_slideshow+1 }}
{{ /foreach }}      
{{ if $has_slideshow gt 0 }}<img class="a-icon" alt="{{ #hasPhotoGallery# }}" title="{{ #hasPhotoGallery# }}" src="{{ url static_file='_img/icons/photo.png' }}" />{{ /if }}


{{ assign var="has_audio" value="0" }}
{{ list_article_attachments }}
{{ if $gimme->attachment->extension == oga }}
{{ assign var="has_audio" value="1" }}
{{ /if }}
{{ /list_article_attachments }}

{{ if $has_audio == "1" }}<img class="a-icon" alt="{{ #hasAudioAttachment# }}" title="{{ #hasAudioAttachment# }}" src="{{ url static_file='_img/icons/audio.png' }}" />{{ /if }}

{{ assign var="has_video" value="0" }}
{{ list_article_attachments }}
{{ if ($gimme->attachment->extension == ogv) || ($gimme->attachment->extension == ogg) }}
{{ assign var="has_video" value="1" }}
{{ /if }}
{{ /list_article_attachments }}

{{ if $has_video == "1" }}<img class="a-icon" alt="{{ #hasVideoAttachment# }}" title="{{ #hasVideoAttachment# }}" src="{{ url static_file='_img/icons/video.png' }}" />{{ /if }}