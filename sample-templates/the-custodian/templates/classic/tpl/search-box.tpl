{{ local }}
{{ unset_issue }}
{{ unset_topic }}

<div class="searchframe searchframebig">
<div class="searchframebiginner">
  <div class="teaserhead">
  <div class="teaserheadinner">{{ if $gimme->language->name == "English" }}Search / Archive{{ else }}Buscar / Archivo{{ /if }}
  </div><!-- .teaserheadinner -->
  </div><!-- .teaserhead -->
<div class="search"><div class="searchinner">


{{ search_form template="classic/search-result.tpl" submit_button="Search" button_html_code="class=\"submitbutton\"" }}
    {{ camp_edit object="search" attribute="keywords" html_code="class=\"text\"" }}
    {{* camp_select object="search" attribute="level" *}}
    {{* camp_select object="search" attribute="mode" *}}
{{ /search_form }}

<div id="topics">
    <a href="{{ uri options="template classic/topic.tpl" }}">{{ if $gimme->language->name == "English" }}Browse all topics{{ else }}Ver todos los temas{{ /if }}</a>
</div>

<div id="issues">
    <a href="{{ uri options="template classic/archive.tpl" }}">{{ if $gimme->language->name == "English" }}Browse all issues{{ else }}Ver todas las ediciónes{{ /if }}</a>
</div>


<div class="clear"></div>
</div></div>
</div><!-- .searchframebiginner -->
</div><!-- .searchframe -->

{{ /local }}
