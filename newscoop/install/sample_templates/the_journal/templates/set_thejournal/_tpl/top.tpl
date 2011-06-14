<style type="text/css">
.logintop {
  font-size: 11px;
  padding: 0 0 0 15px;
  float: left;
  text-align: right;
  width: 600px;
  position: relative;
  top: -18px;
}

.logintop form p.fields {
  padding: 0;
}
  
.logintop form p.fields input {
  color: #666666;
  font-size: 11px;
  font-style:italic;
  font-weight: normal;
  width: 100px;
  background: none;
  border: 1px solid #ccc;
  padding-left: 5px;
}
</style>

<div id="top"> 
      <div id="top-meta">
          <div class="date">{{$smarty.now|camp_date_format:"%M %e, %Y"}}</div>
          
            <div class="rss">
                <a href="http://{{ $gimme->publication->site }}/?tpl=1133">Subscribe RSS Feed</a>
            </div>

{{ include file="_tpl/top-login.tpl" }}

        </div><!-- /#top-meta -->
         
{{ include file="_tpl/top-highlights.tpl" }}
                 
        <div id="header">
            
            <div class="logo">
              <a href="http://{{ $gimme->publication->site }}" title="The Journal"><img src="{{ url static_file='_img/logo.png' }}" alt=""></a>
            </div>
                       
{{ include file="_tpl/top-recent-entries.tpl" }}
            
        </div>
        
{{ include file="_tpl/top-nav.tpl" }}        

    </div><!-- /#top -->
