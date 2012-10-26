{{ include file="classic/tpl/header.tpl" }}

<body id="archive">
<div id="container">
<div id="wrapbg">
<div id="wrapper">

{{ include file="classic/tpl/headernav.tpl" }}

<div class="colmask rightmenu">
    <div class="colleft">
        <div class="col1wrap">
            <div class="col1">
                <!-- Column 1 start -->

<div class="list-nested" id="archive-tree">
{{ set_default_issue }}
    <h2>{{ $gimme->issue->name }} ({{ if $gimme->language->name == "English" }}issue #{{  $gimme->issue->number }} / published{{ else }}Edición #{{  $gimme->issue->number }} / publicado {{ /if }}{{ $gimme->issue->publish_date|camp_date_format:'%Y-%M-%D' }})</h2>
    
    {{ list_sections order="bynumber asc" }}
        <h3><a href="{{ uri }}" class="linksection-{{ $gimme->section->number }}">{{ $gimme->section->name }}</a><!--{{ $gimme->section->number }}--></h3>
        
        <ul>
        {{ list_articles }}
            <li id="list-article"><h4><a href="{{ uri }}" class="linksection-{{ $gimme->section->number }}">{{ $gimme->article->name }}</a></h4>
  <div class="list-article-published">
            {{ if $gimme->language->name == "English" }}posted{{ else }}publicado el{{ /if }} {{ $gimme->article->publish_date|camp_date_format:'%M %D, %Y %h:%i:%s' }}
  </div>
{{ include file="classic/tpl/topic-list.tpl" }}
</li>
        {{ /list_articles }}
        </ul>
    {{ /list_sections }}
</div>

        <!-- Column 1 end -->
            </div>
        </div>
        <div class="col2">
            <!-- Column 2 start -->

{{ include file="classic/tpl/search-box.tpl" }}

{{ list_issues length="10" order="byNumber desc" }}
  <h2><a href="{{ url options="template classic/archive.tpl" }}">{{ $gimme->issue->name }}</a></h2>
 {{ if $gimme->language->name == "English" }}Issue{{ else }}Edición{{ /if }} #{{ $gimme->issue->number }} / 
      ({{ if $gimme->language->name == "English" }}published{{ else }}publicado{{ /if }} {{ $gimme->issue->publish_date|camp_date_format:'%M %D, %Y %h:%i:%s' }})

  {{ include file="skins/greenpiece/includes/pagination.tpl" }}
{{ /list_issues }}

<!-- Banner -->
{{ include file="classic/tpl/banner/bannerrightcol.tpl" }}

      <!-- Column 2 end -->
        </div>
    </div>
</div>

{{ include file="classic/tpl/footer.tpl" }}

</div><!-- id="wrapper" -->
</div><!-- id="wrapbg" -->
</div><!-- id="container"-->
</body>
</html>
