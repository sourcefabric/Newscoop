           <div id="also">
              
               <div id="also-header">
                  <h3>Also in this site...</h3>
                    <div class="carousel-nav">
                      <img style="opacity: 0;" class="back" src="{{ url static_file='_img/carousel_back_button.gif' }}" alt="Previous Posts">
                      <img style="opacity: 1;" class="next" src="{{ url static_file='_img/carousel_next_button.gif' }}" alt="Next Posts">
                    </div>
                    <div class="fix"></div>
                </div>
                <div id="categories-crop">  
                    <div style="margin-left: 0px;" id="categories-slider">
                               
{{ local }}
{{ set_current_issue }}
{{ list_articles length="12" ignore_issue="true" ignore_section="true" order="bypublishdate desc" constraints="onfrontpage is off onsection is off highlight is off" }}
                               
                        <div class="panel">
                            <p class="category"><a href="{{ uri options="section" }}" title="View all posts in {{ $gimme->section->name }}" rel="category tag">{{ $gimme->section->name }}</a></p>
                            <div class="panel-image"><a title="{{ $gimme->article->name }}" href="{{ uri options="article" }}"><div style="background: {{ if $gimme->article->has_image(2) }}url({{uri options="image 2"}}&ImageRatio=40){{ else }}url({{uri options="image 1"}}&ImageRatio=40){{ /if }} no-repeat center center; width: 147px; height: 144px" class="woo-image thumbnail">&nbsp;</div></a></div>
                            <h3><a href="{{ uri options="article" }}">{{ if ! $gimme->article->content_accessible }}* {{ /if }}{{ $gimme->article->name }}</a></h3>
                            <div class="fix"></div>
                         </div>
                              
{{ /list_articles }}
{{ /local }}                                                       
                                                 
                </div>
                </div>
                
            </div><!-- also ends -->
