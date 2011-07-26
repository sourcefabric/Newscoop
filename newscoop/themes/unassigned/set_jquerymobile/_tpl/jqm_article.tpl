 
<body> 

<div data-role="page" id="main">
{{ include file="_tpl/jqm_header-page.tpl" }}
  <div data-role="content"> 
    <h2 class="post-title">{{ $gimme->article->name }}</h2>
{{ if $gimme->article->type_name == "news" }}
    <small>
      <p>
        {{ $gimme->article->publish_date|camp_date_format:"%M %e, %Y" }} | <a href="{{ uri options="section" }}">{{ $gimme->section->name }}</a>
        {{ list_article_locations }}{{ strip }}
          {{ if $gimme->current_list->at_beginning }}<br /><em>{{ /if }} 
          {{ if $gimme->location->enabled }}{{ $gimme->location->name }}{{ if $gimme->current_list->at_end }}</em>{{ else }}, {{ /if }}{{ /if }}
        {{ /strip }}{{ /list_article_locations }}
      </p>
      <div class="ui-body ui-body-c">
        {{ list_article_authors }}{{ strip }}
          <strong>{{ $gimme->author->name }}</strong> ({{ $gimme->author->type|lower }})
          {{ if !$gimme->current_list->at_end }}<br /> {{ /if }}
        {{ /strip }}{{ /list_article_authors }}
      </div>
    </small>
{{ /if }}

<!-- comment feedback / error messages - if posted -->
{{ include file="_tpl/jqm_article-comments-errorinformation.tpl" }}

<div data-role="fieldcontain" class="full_text">
    {{ if $gimme->article->content_accessible }}
      {{ if $gimme->article->type_name == "news" }}
        {{ if $gimme->article->has_image(1) }}
          <img src="{{ url options="image 1 width 280" }}" alt="{{ $gimme->article->image1->description }}" style="max-width: 50%; margin-right: 10px;" align="left"/>
        {{ elseif $gimme->article->has_image(2)}}
          <img src="{{ url options="image 2 width 280" }}" alt="{{ $gimme->article->image2->description }}" style="max-width: 50%; margin-right: 10px;" align="left"/>
        {{ /if }}
      {{ /if }}
      {{ $gimme->article->full_text }}
    {{ else }}
      <div class="ui-body ui-body-e">
        <p><strong>This article is accessible only to subscribers.</strong> You need to be registered and logged in.</p>
      </div>
      <!-- Login / Registration / Subscriptions -->
      {{ include file="_tpl/jqm_login-registration-embed.tpl" }}
    {{ /if }}      
    </small></div><!--fieldcontain-->    

{{ if $gimme->article->type_name == "news" }}          
  <!-- author info and links in collapsible list -->
  {{ include file="_tpl/jqm_article-authorinfo.tpl" }}
  <!-- comments -->
  {{ include file="_tpl/jqm_article-comments.tpl" }}
  <!-- list of latest news -->
  {{ include file="_tpl/jqm_list-latest-news.tpl" }}
  <!-- navigation: all sections -->
  {{ include file="_tpl/jqm_nav-sections.tpl" }}
{{ /if }}

  </div><!-- /content -->

{{ include file="_tpl/jqm_footer.tpl" }}
</div><!-- /page -->

<!--/body>
</html-->
