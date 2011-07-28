{{ include file="_tpl/_html-head.tpl" }}

<body class="custom gecko">
<div id="wrap">

<div id="top"> 
      <div id="top-meta">
          <div class="date">{{$smarty.now|camp_date_format:"%M %e, %Y"}}</div>
            <div class="rss">
                <a href="http://{{ $gimme->publication->site }}/?tpl=1133">Subscribe RSS Feed</a>
            </div>

{{ include file="_tpl/top-search-box.tpl" }}

        </div><!-- /#top-meta -->
                 
        <div id="header">
            <div class="logo">
              <a href="http://{{ $gimme->publication->site }}" title="The Journal"><img src="{{ url static_file='_img/logo.png' }}" alt=""></a>
            </div>
        </div>      

    </div><!-- /#top -->
    
<div style="margin: 50px auto;height: 100px; text-align: center">
{{ if !($gimme->url->is_valid) }}
          <h1>Sorry, the requested page was not found.</h1>
{{ /if }}
</div>    
    
<div id="footer" class="wrap">
      <ul id="category-nav">

{{ local }}
{{ set_language name="English" }}
{{ set_current_issue }}      
{{ list_sections }}
      <li class="cat-item"><a href="{{ uri options="section" }}" title="View all posts filed under {{ $gimme->section->name }}">{{ $gimme->section->name }}</a></li>
{{ /list_sections }}
{{ /local }}

        </ul>
        
        <div class="fix"></div>
        
        <ul id="page-nav">
        
{{ local }}
{{ set_language name="English" }}
{{ set_issue number="1" }}
{{ set_section number="5" }}        
{{ list_articles }}
        
      <li class="page_item"><a href="{{ uri options="article" }}" title="{{ $gimme->article->name }}">{{ $gimme->article->name }}</a></li>

{{ /list_articles }}      
{{ unset_section }}
     
      <li style="border: medium none;" class="page_item"><a href="{{ uri options="template archive.tpl" }}" title="Archives">Archives</a></li>

{{ /local }}       
      
        </ul>
        
        <div class="fix"></div>        
                
    <div class="credits">
      <p>&copy; 2011 The Journal. All Rights Reserved. Powered by <a href="http://newscoop.sourcefabric.org/" title="Newscoop">Newscoop</a>. Designed by <a href="http://www.woothemes.com/"><img src="{{ url static_file='_img/woothemes.png' }}" alt="Woo Themes" width="87" height="21"></a></p>
    </div>
  </div>

  
</div>

</body>
</html>