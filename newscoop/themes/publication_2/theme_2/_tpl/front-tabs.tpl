<div id="tabs" class="block">
                
                    <ul class="idTabs wrap tabs">
                       
                        <li><a class="selected" href="#commented">Most Read</a></li>
                         <li><a href="#recentcomments">Recent Comments</a></li> 
                        
                    </ul>
                    <div class="inside">
                                
                        <ul style="display: block;" id="commented">
{{ local }}
{{ set_current_issue }}
{{ list_articles length="5" order="bypopularity desc" constraints="type is news" }}
                       
                            <li><a href="{{ uri options="article" }}">{{ $gimme->article->name }}</a></li>

{{ /list_articles }}
{{ /local }}                            
                        </ul> 
                        
                        <ul style="display: none;" id="recentcomments">
                        
{{ local }}
{{ set_current_issue }}
{{ list_articles length="5" order="byLastComment desc" constraints="type is news" }}
                       
          <li class="recentcomments">{{ list_article_comments length="1" order="bydate desc"}}{{ $gimme->comment->nickname }}{{ /list_article_comments }} on <a href="{{ uri options="article" }}" style="font-style: italic">{{ $gimme->article->name }}</a></li>

{{ /list_articles }}
{{ /local }}                        

                        </ul>
                    </div>
                </div>