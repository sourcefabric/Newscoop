{{ include file="classic/tpl/header.tpl" }}

<body id="article" class="section-{{ $campsite->section->number }}">
<div id="container">
<div id="wrapbg">
<div id="wrapper">

  {{ include file="classic/tpl/headernav.tpl" }}

  <div class="colmask rightmenu">
    <div class="colleft">
      <div class="col1wrap">
        <div class="col1">
        <!-- Column 1 start -->

{{ if $campsite->search_articles_action->defined }}
  
    {{ if $campsite->search_articles_action->is_error }}
      {{ $campsite->search_articles_action->error_message }}
    {{ /if }}

    {{ if $campsite->search_articles_action->ok }}

        {{ list_search_results name="results" length=9 }}
        {{ if $campsite->current_list->at_beginning }}
            <p>Found {{ $campsite->current_list->count }} articles matching the condition.</p>
        {{ /if }}
<div class="teaserframe teaserframebig teaserframe-{{ $campsite->section->number }} teaserframebig-{{ $campsite->section->number }}">
<div class="teaserframebiginner">
	<div class="teaserhead">
	<div class="teaserheadinner">
	</div><!-- .teaserheadinner -->
	</div><!-- .teaserhead -->
        	<div class="teasercontent content">
        	<h2 class="title title_big"><a href="{{ uri options="article template article.tpl" }}">{{ $campsite->article->name }}</a></h2>
        	<p class="text">(Section <a href="{{ uri options="section template section.tpl" }}">{{ $campsite->section->name }}</a>, {{ $campsite->article->Date }} {{ $campsite->article->Time }}) {{ $campsite->article->Deck }}</p>
        	</div><!-- .teasercontent content -->
        </div><!-- .teaserframebiginner -->
        </div><!-- .teaserframebig -->
        	{{ unset_section }}
            {{ include file="classic/tpl/pagination.tpl" }}
        {{ /list_search_results }}

        {{ if $campsite->prev_list_empty }}
          <div class="error"><div class="errorinner">
          There were no articles found.
          </div></div>
        {{ /if }}
    {{ /if }}
{{ /if }}
        <!-- Column 1 end -->
        </div>
      </div>
      
      <div class="col2">
      <!-- Column 2 start -->
              
        {{ include file="classic/tpl/search-box.tpl" }}

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