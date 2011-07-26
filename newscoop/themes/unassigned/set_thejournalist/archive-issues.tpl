{{ include file="_tpl/_html-head.tpl" }}

<body>

<div id="container" class="group">

{{ include file="_tpl/header.tpl" }}

    <div id="content">

{{ list_articles ignore_section="true" order="bypublishdate desc" constraints="type is post" }}

{{ if $gimme->current_list->at_beginning }}
        {{ if $gimme->language->english_name == "English" }}<h2 class="archive">Archive for {{ $gimme->issue->name }} ({{ $gimme->current_list->count }} posts)</h2>{{ /if }}
        {{ if $gimme->language->english_name == "Spanish" }}<h2 class="archive">Archivo para {{ $gimme->issue->name }} ({{ $gimme->current_list->count }} posts)</h2>{{ /if }}
        {{ if $gimme->language->english_name == "Polish" }}<h2 class="archive">Archiwum wpisów {{ $gimme->issue->name }} ({{ $gimme->current_list->count }})</h2>{{ /if }}
        {{ if $gimme->language->english_name == "Russian" }}<h2 class="archive">Архив для {{ $gimme->issue->name }} ({{ $gimme->current_list->count }})</h2>{{ /if }}
{{ /if }}

    {{ include file="_tpl/cont-entry-intro.tpl" }}                            

{{ /list_articles }}

    </div><!-- /.content -->

{{ include file="_tpl/sidebar.tpl" }}

</div><!-- /#container -->

{{ include file="_tpl/footer.tpl" }}

</body>
</html>