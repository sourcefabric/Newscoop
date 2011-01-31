<div id="top"> 
      <div id="top-meta">
          <div class="date">{{$smarty.now|camp_date_format:"%M %e, %Y"}}</div>
            <div class="rss">
                <a href="http://{{ $gimme->publication->site }}/?tpl=1133">Subscribe RSS Feed</a>
            </div>

{{ include file="set_thejournal/_tpl/top-search-box.tpl" }}

        </div><!-- /#top-meta -->
         
{{ include file="set_thejournal/_tpl/top-highlights.tpl" }}
                 
        <div id="header">
            
            <div class="logo">
              <a href="http://{{ $gimme->publication->site }}" title="The Journal"><img src="/templates/set_thejournal/_img/logo.png" alt=""></a>
            </div>
                       
{{ include file="set_thejournal/_tpl/top-recent-entries.tpl" }}
            
        </div>
        
{{ include file="set_thejournal/_tpl/top-nav.tpl" }}        

    </div><!-- /#top -->