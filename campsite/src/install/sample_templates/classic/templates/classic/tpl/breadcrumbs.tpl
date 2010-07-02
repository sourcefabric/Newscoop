<div class="breadcrumbs"><div class="breadcrumbsinner">
<a href="{{ uri options="publication" }}">Home</a>
&gt;
{{ if $campsite->template->name == "classic/topic.tpl" }}
    {{ if $campsite->topic->defined }}
        Topic: {{ $campsite->topic->name }}
    {{ else }}
        Topics
    {{ /if }}
{{ elseif $campsite->template->name == "classic/archive.tpl" }}
    Archive
{{ elseif $campsite->section->defined }}
    <a href="{{ uri options="section" }}">{{ $campsite->section->name }}</a>
{{ /if }}

</div><!-- .breadcrumbsinner -->
</div><!-- .breadcrumbs -->