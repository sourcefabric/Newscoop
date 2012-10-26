<style>
    #navmain ul li form {
      height: 30px;
      margin-left: 100px;
      line-height: 30px;
      padding: 4px 10px;
      margin: 0;
      float: right;
    }
    #navmain ul li form select {
      height: 20px;
      width: 200px;
      background-color: #fff;
      font-size: 10px;
      color: #000;
    }
</style>

<script language="JavaScript" type="text/JavaScript">
<!--
function MM_jumpMenu(targ,selObj,restore){ //v3.0
  eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
  if (restore) selObj.selectedIndex=0;
}
//-->
</script>

<div id="header">
  <div id="headernav">
{{ include file="classic/tpl/login-top.tpl" }}
    <div id="logospace">
       <a href="http://{{ $gimme->publication->site }}/{{ $gimme->language->code }}/" style="border: none; dispaly: block; float: left; margin: 25px 0 0 10px;"><img alt="logo" style="border: none" src="http://{{ $gimme->publication->site }}/templates/classic/css/cleanblue/logo-blue.png" /></a>
       {{ include file="classic/tpl/banner/bannerlogo.tpl" }}
      </div>
      
    <div id="navmain">
    {{ if $gimme->section->defined }}
      {{ assign var='curr_section' value=$gimme->section->number }}
      {{ set_current_issue }}
      {{ set_section number=$curr_section }}
    {{ /if }}
       
      <ul>
      <li id="navlinksection-home">
        <div class="navlink">
          <a href="{{ uri options="publication" }}" id="navlinksection-home">
            {{ if $gimme->language->name == "English" }}Home{{ else }}Portada{{ /if }}
          </a>
        </div>
      </li>
      {{ list_sections name="sections" }}
        {{ if $gimme->section->number == $gimme->default_section->number }}
          <li class="active" id="navlinksection-{{ $gimme->section->number }}"><div class="navlink"><a href="{{ uri options="section" }}" id="navlinksection-{{ $gimme->section->number }}">{{ $gimme->section->name }}</a></div></li>
        {{ else }}
          <li id="navlinksection-{{ $gimme->section->number }}"><div class="navlink"><a href="{{ uri options="section" }}" id="navlinksection-{{ $gimme->section->number }}">{{ $gimme->section->name }}</a></div></li>
        {{ /if }}
      {{ /list_sections }}
      
      {{ unset_section }}
      {{ unset_article }}
      {{ unset_topic }}
          <li id="navlinksection-blogs"><div class="navlink">
<a href="{{ uri options="template classic/tpl/blog/section-blog.tpl" }}" id="navlinksection-blogs">Blogs</a></div></li>
      {{ set_default_section }}
      {{ set_default_article }}
      {{ set_default_topic }}
      
<li>
<form method="post" action="">
  <select name="menu" onChange="MM_jumpMenu('parent',this,0)">
    <option selected>Language/Idioma</option>
  {{ local }}{{ set_language name="English" }}
  <option value="{{ uri }}">English</option>
  {{ set_language name="Spanish" }}
  <option value="{{ uri }}">Español</option>
  {{ /local }}
  </select>
</form>
</li>      
      
      </ul>
    </div><!-- #navmain -->
    
  </div><!-- #headernav -->
</div><!-- #header -->

{{ if !$is_index }}
{{ if $gimme->template->name == "classic/topic.tpl" }}
    {{ if $gimme->topic->defined }}
  <div class="sectionheader">
    <div class="sectionheaderinner">
        {{ if $gimme->language->name == "English" }}Topic:{{ else }}Tema:{{ /if }} {{ $gimme->topic->name }}
    </div><!-- .sectionheaderinner -->
  </div><!-- .sectionheader -->
    {{ else }}
  <div class="sectionheader">
    <div class="sectionheaderinner">
        {{ if $gimme->language->name == "English" }}Topics{{ else }}Temas{{ /if }}
    </div><!-- .sectionheaderinner -->
  </div><!-- .sectionheader -->
    {{ /if }}
{{ elseif $gimme->template->name == "classic/archive.tpl" }}
  <div class="sectionheader">
    <div class="sectionheaderinner">
    Archive
    </div><!-- .sectionheaderinner -->
  </div><!-- .sectionheader -->
{{ elseif $gimme->search_articles_action->ok }}
  <div class="sectionheader">
    <div class="sectionheaderinner">
        {{ if $gimme->language->name == "English" }}Search results for:{{ else }}Resultados de la búsqueda{{ /if }} {{ $gimme->search_articles_action->search_phrase }}
    </div><!-- .sectionheaderinner -->
  </div><!-- .sectionheader -->
{{ elseif $gimme->section->defined }}
  <div class="sectionheader sectionheader-{{ $gimme->section->number }}">
    <div class="sectionheaderinner">
        {{ $gimme->section->name }}
    </div><!-- .sectionheaderinner -->
  </div><!-- .sectionheader -->
{{ /if }}
  {{ include file="classic/tpl/breadcrumbs.tpl" }}
{{ /if }}

<!-- end headernav.tpl -->