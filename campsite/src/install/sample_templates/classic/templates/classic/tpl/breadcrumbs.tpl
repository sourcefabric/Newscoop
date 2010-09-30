<div class="breadcrumbs"><div class="breadcrumbsinner">
<a href="{{ uri options="publication" }}">{{ if $campsite->language->name == "English" }}Home{{ else }}Portada{{ /if }}</a>
&gt;
{{ if $campsite->template->name == "classic/topic.tpl" }}
    {{ if $campsite->topic->defined }}
        {{ if $campsite->language->name == "English" }}Topic{{ else }}Tema{{ /if }}: {{ $campsite->topic->name }}
    {{ else }}
        {{ if $campsite->language->name == "English" }}Topics{{ else }}Temas{{ /if }}
    {{ /if }}
{{ elseif $campsite->template->name == "classic/archive.tpl" }}
    {{ if $campsite->language->name == "English" }}Archive{{ else }}Archivo{{ /if }}
{{ elseif $campsite->section->defined }}
    <a href="{{ uri options="section" }}">{{ $campsite->section->name }}</a>
{{ /if }}

</div><!-- .breadcrumbsinner -->
</div><!-- .breadcrumbs -->