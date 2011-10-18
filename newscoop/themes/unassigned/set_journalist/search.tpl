{{ include file="_tpl/_html-head.tpl" }}

<body>

<div id="container" class="group">

{{ include file="_tpl/header.tpl" }}

    <div id="content">
        {{ if $gimme->language->english_name == "English" }}<h2 class="archive">Search results</h2>{{ /if }}
        {{ if $gimme->language->english_name == "Spanish" }}<h2 class="archive">Resultados de la búsqueda</h2>{{ /if }}
        {{ if $gimme->language->english_name == "Polish" }}<h2 class="archive">Wyniki wyszukiwania</h2>{{ /if }}
        {{ if $gimme->language->english_name == "Russian" }}<h2 class="archive">Результаты поиска</h2>{{ /if }}

{{ list_search_results length="8" ignore_issue="true" ignore_section="true" order="bypublishdate desc" constraints="type is post" }}

    {{ include file="_tpl/cont-entry-intro.tpl" }}                            

{{ if $gimme->current_list->at_end }}
    {{ include file="_tpl/cont-navigate.tpl" }}
{{ /if }}

{{ /list_search_results }}

{{ if $gimme->prev_list_empty }}
  <p style="margin: 20px 0">
        {{ if $gimme->language->english_name == "English" }}Nothing found, sorry.{{ /if }}
        {{ if $gimme->language->english_name == "Spanish" }}No he encontrado nada, lo siento.{{ /if }}
        {{ if $gimme->language->english_name == "Polish" }}Nic nie znaleziono, przepraszam.{{ /if }}
        {{ if $gimme->language->english_name == "Russian" }}Ничего не найдено, извините.{{ /if }}  
  </p>
{{ /if }}

    </div> 

{{ include file="_tpl/sidebar.tpl" }}

</div><!-- /#container -->

{{ include file="_tpl/footer.tpl" }}

</body>
</html>