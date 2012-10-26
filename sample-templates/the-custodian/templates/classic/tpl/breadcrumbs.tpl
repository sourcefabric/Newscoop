<div class="breadcrumbs"><div class="breadcrumbsinner">
<a href="{{ uri options="publication" }}">{{ if $gimme->language->name == "English" }}Home{{ else }}Portada{{ /if }}</a>
&gt;
{{ if $gimme->template->name == "classic/topic.tpl" }}
    {{ if $gimme->topic->defined }}
        {{ if $gimme->language->name == "English" }}Topic{{ else }}Tema{{ /if }}: {{ $gimme->topic->name }}
    {{ else }}
        {{ if $gimme->language->name == "English" }}Topics{{ else }}Temas{{ /if }}
    {{ /if }}
{{ elseif $gimme->template->name == "classic/archive.tpl" }}
    {{ if $gimme->language->name == "English" }}Archive{{ else }}Archivo{{ /if }}
{{ elseif $gimme->section->defined }}
    <a href="{{ uri options="section" }}">{{ $gimme->section->name }}</a>
{{ /if }}

</div><!-- .breadcrumbsinner -->
</div><!-- .breadcrumbs -->