{{ include file="classic/tpl/header.tpl" }}

<body id="article" class="section-{{ $gimme->section->number }}">
<div id="container">
<div id="wrapbg">
<div id="wrapper">

  {{ include file="classic/tpl/headernav.tpl" }}

  <div class="colmask rightmenu">
    <div class="colleft">
      <div class="col1wrap">
        <div class="col1">
        <!-- Column 1 start -->
          <div class="articlecontent">
            <div class="date">
              {{ $gimme->article->publish_date }}
              {{ include file="classic/tpl/topic-list.tpl" }}
            </div><!-- .date -->
            <h3 class="deck deck_med">{{ $gimme->article->Deck }}</h3>            
            
            <h1>{{ $gimme->article->name }}</h1>
            <div class="byline">{{ $gimme->article->author->name }}</div>

            {{ include file="classic/tpl/if-map.tpl" }}
            {{ include file="classic/tpl/dynamic-map.tpl" }}
            {{ include file="classic/tpl/if-video.tpl" }}
            {{ include file="classic/tpl/if-audio.tpl" }}
            
            <p class="text">
              {{ $gimme->article->Body }}
            </p>
          </div><!-- .articlecontent -->
          
{{ include file="classic/tpl/article-gallery.tpl" }}          
          
{{ include file="classic/tpl/article-attachments.tpl" }}
        
{{ include file="classic/tpl/comments.tpl" }}

        <!-- Column 1 end -->
        </div>
      </div>
      
      <div class="col2">
      <!-- Column 2 start -->
              
        {{ include file="classic/tpl/search-box.tpl" }} 

<!-- Banner -->
{{ include file="classic/tpl/banner/bannerrightcol.tpl" }}
    
        <!-- start: 3 articles having same topic -->
        {{ list_article_topics }}
          {{ assign var="topic_cond" value="`$topic_cond` topic is `$gimme->topic->identifier` " }}
        {{ /list_article_topics }}
    
        {{ if $topic_cond }}
          <!-- 3 Articles {{ $topic_cond }} -->
          {{ list_articles name="topic_articles" constraints="number not `$gimme->article->number` `$topic_cond` matchAnyTopic"  ignore_issue=true length=3 }}
            {{ include file="classic/tpl/teaserframe_articlelistright.tpl" }}
            {{ include file="classic/tpl/pagination.tpl" }}
            {{ assign var="number_cond" value="`$number_cond` number not `$gimme->article->number` " }}
          {{ /list_articles }}
          {{ unset_topic }}
        {{ /if }}
        <!-- end: 3 articles having same topic end -->
    
        <!-- 3 Articles from section number {{ $gimme->section->number }}, Article {{ $number_cond }} -->
        {{ list_articles name="section_articles" constraints="number not `$gimme->article->number` $number_cond" ignore_issue=true length=3 }} 
          {{ include file="classic/tpl/teaserframe_articlelistright.tpl" }}
          {{ include file="classic/tpl/pagination.tpl" }}
        {{ /list_articles }}
        <!-- 3 articles having same section end -->
       
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