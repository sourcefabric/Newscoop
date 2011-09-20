{{ include file="_tpl/_html-head.tpl" }}

<body>

<div id="container" class="group">

{{ include file="_tpl/header.tpl" }}

    <div id="content">
         
{{ list_articles length="8" ignore_issue="true" ignore_section="true" order="bypublishdate desc" constraints="type is post" }}

{{ if $gimme->current_list->at_beginning }}
        {{ if $gimme->language->english_name == "English" }}<h2 class="archive">Archive for the ‘{{ $gimme->topic->name }}’ Tag ({{ $gimme->current_list->count }} posts)</h2>{{ /if }}
        {{ if $gimme->language->english_name == "Spanish" }}<h2 class="archive">Archivo para ‘{{ $gimme->topic->name }}’ Tag ({{ $gimme->current_list->count }} posts)</h2>{{ /if }}
        {{ if $gimme->language->english_name == "Polish" }}<h2 class="archive">Archiwum dla tagu ‘{{ $gimme->topic->name }}’ ({{ $gimme->current_list->count }} posts)</h2>{{ /if }}
        {{ if $gimme->language->english_name == "Russian" }}<h2 class="archive">Архив для ‘{{ $gimme->topic->name }}’ Тег ({{ $gimme->current_list->count }})</h2>{{ /if }}
        {{ include file="_tpl/dynamic-map-topic.tpl" }}
{{ /if }}

    {{ include file="_tpl/cont-entry-intro.tpl" }}                            

    {{ include file="_tpl/cont-navigate.tpl" }}

{{ /list_articles }}

    </div> 

{{ include file="_tpl/sidebar.tpl" }}

</div><!-- /#container -->

{{ include file="_tpl/footer.tpl" }}

</body>
</html>