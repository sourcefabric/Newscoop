    <h3>{{ if $gimme->language->english_name == "English" }}Search{{ /if }}{{ if $gimme->language->english_name == "Spanish" }}Búsqueda{{ /if }}{{ if $gimme->language->english_name == "Polish" }}Szukaj{{ /if }}{{ if $gimme->language->english_name == "Russian" }}Поиск{{ /if }}</h3>

{{ search_form template="search.tpl" submit_button="Search" html_code="class=\"group\" id=\"search-form\"" button_html_code="id=\"search-button\"" }} 
    {{ camp_edit object="search" attribute="keywords" html_code="id=\"search-field\"" }}
{{ /search_form }}
