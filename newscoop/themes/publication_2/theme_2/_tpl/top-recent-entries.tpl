<div class="recent-entries">
           <div class="archives">
                    <h3>Recent Entries</h3>
                         
                    <p>Go to the <a href="{{ local }}{{ unset_section }}{{ uri options="template archive.tpl" }}{{ /local }}">Archives</a> <br />
                  to see more entries</p>
                </div><!-- /.archives -->
                <ul>
                
{{ local }}
{{ list_articles length="3" ignore_issue="true" ignore_section="true" order="bypublishdate desc" constraints="type is news" }}                
                
                    <li>
                        <div class="category"><a href="{{ uri options="section" }}" title="View all posts in {{ $gimme->section->name }}" rel="category tag">{{ $gimme->section->name }}</a></div>
                        <h4><a title="{{ $gimme->article->name }}" href="{{ uri options="article" }}" rel="bookmark">{{ if ! $gimme->article->content_accessible }}* {{ /if }}{{ $gimme->article->name }}</a></h4>
                    </li>

{{ /list_articles }}
{{ /local }}
                    
                </ul>
            </div><!-- /.recent-entries -->
      <div class="fix"></div>