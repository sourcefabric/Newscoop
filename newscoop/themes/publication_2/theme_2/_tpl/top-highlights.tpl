         <div id="highlights"><!-- Highlights starts -->
          <h3>Highlights &gt;</h3>           
            <div class="fix"></div>
            
{{ local }}
{{ unset_topic }}
{{ list_articles length="3" ignore_issue="true" ignore_section="true" order="bydate desc" constraints="highlight is on" }}
                      
            <div class="post{{ if $gimme->current_list->index == 3 }} last{{ /if }}">
              <div class="image">
                  <div style="background: {{ if $gimme->article->has_image(2) }}url({{uri options="image 2"}}&ImageRatio=25){{ else }}url({{uri options="image 1"}}&ImageRatio=33){{ /if }} no-repeat center center; width: 135px; height: 75px" class="woo-image thumbnail">&nbsp;</div>
                </div>
                <div class="content">
                  <p><a href="{{ uri options="article" }}" rel="bookmark">{{ if ! $gimme->article->content_accessible }}* {{ /if }}{{ $gimme->article->name }}</a></p>
                    <p class="read_more"><a href="{{ uri options="article" }}" rel="bookmark">MORE +</a></p>
                </div>
            </div>
            
{{ /list_articles }}
{{ /local }}            
                      
            <div class="fix"></div> 
            
            <div class="fix"></div>
            
        </div><!-- Highlights ends --> 