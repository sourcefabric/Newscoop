    <h3>{{ if $gimme->language->english_name == "English" }}Search{{ /if }}{{ if $gimme->language->english_name == "Spanish" }}Búsqueda{{ /if }}{{ if $gimme->language->english_name == "Polish" }}Szukaj{{ /if }}{{ if $gimme->language->english_name == "Russian" }}Поиск{{ /if }}</h3>

<form name="search_articles" action="{{ uri }}" method="post" class="group" id="search-form"> 
    <input type="hidden" name="tpl" value="1877" /> 
    <input type="text" id="search-field" name="f_search_keywords" maxlength="255" size="10" value="" />
    <input type="submit" name="f_search_articles" value="Search" id="search-button" />     
    <input type="hidden" name="f_search_articles" /> 
</form>
