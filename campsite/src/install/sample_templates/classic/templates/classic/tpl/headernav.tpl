<!-- start headernav.tpl -->

<div id="header">
  <div id="headernav">
{{ include file="classic/tpl/login-top.tpl" }}
    <div id="logospace">
       <a href="http://{{ $campsite->publication->site }}" style="border: none; dispaly: block; float: left; margin: 25px 0 0 10px;"><img alt="logo" style="border: none" src="http://{{ $campsite->publication->site }}/templates/classic/css/cleanblue/logo-blue.png" /></a>
       {{ include file="classic/tpl/banner/bannerlogo.tpl" }}
      </div>
      
    <div id="navmain">
    {{ if $campsite->section->defined }}
      {{ assign var='curr_section' value=$campsite->section->number }}
      {{ set_current_issue }}
      {{ set_section number=$curr_section }}
    {{ /if }}
       
      <ul>
      <li id="navlinksection-home">
        <div class="navlink">
          <a href="{{ uri options="publication" }}" id="navlinksection-home">
            Home
          </a>
        </div>
      </li>
      {{ list_sections name="sections" }}
        {{ if $campsite->section->number == $campsite->default_section->number }}
          <li class="active" id="navlinksection-{{ $campsite->section->number }}"><div class="navlink"><a href="{{ uri options="section" }}" id="navlinksection-{{ $campsite->section->number }}">{{ $campsite->section->name }}</a></div></li>
        {{ else }}
          <li id="navlinksection-{{ $campsite->section->number }}"><div class="navlink"><a href="{{ uri options="section" }}" id="navlinksection-{{ $campsite->section->number }}">{{ $campsite->section->name }}</a></div></li>
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
      
      </ul>
    </div><!-- #navmain -->
  </div><!-- #headernav -->
</div><!-- #header -->

{{ if !$is_index }}
{{ if $campsite->template->name == "classic/topic.tpl" }}
    {{ if $campsite->topic->defined }}
  <div class="sectionheader">
    <div class="sectionheaderinner">
        Topic: {{ $campsite->topic->name }}
    </div><!-- .sectionheaderinner -->
  </div><!-- .sectionheader -->
    {{ else }}
  <div class="sectionheader">
    <div class="sectionheaderinner">
        Topics
    </div><!-- .sectionheaderinner -->
  </div><!-- .sectionheader -->
    {{ /if }}
{{ elseif $campsite->template->name == "classic/archive.tpl" }}
  <div class="sectionheader">
    <div class="sectionheaderinner">
    Archive
    </div><!-- .sectionheaderinner -->
  </div><!-- .sectionheader -->
{{ elseif $campsite->search_articles_action->ok }}
  <div class="sectionheader">
    <div class="sectionheaderinner">
        Search results for: {{ $campsite->search_articles_action->search_phrase }}
    </div><!-- .sectionheaderinner -->
  </div><!-- .sectionheader -->
{{ elseif $campsite->section->defined }}
  <div class="sectionheader sectionheader-{{ $campsite->section->number }}">
    <div class="sectionheaderinner">
        {{ $campsite->section->name }}
    </div><!-- .sectionheaderinner -->
  </div><!-- .sectionheader -->
{{ /if }}
  {{ include file="classic/tpl/breadcrumbs.tpl" }}
{{ /if }}

<!-- end headernav.tpl -->