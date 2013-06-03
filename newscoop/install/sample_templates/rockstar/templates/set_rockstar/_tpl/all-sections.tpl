            <section class="grid-6">

{{ local }} 
{{ unset_topic }}
{{ set_current_issue }} 
{{ list_sections length="6" }}             
              <article>
                  <h3><a href="{{ url options="section" }}">{{ $gimme->section->name }}</a></h3>
{{ list_articles ignore_issue="true" length="2" }}                  
                    <div class="article">
                    {{ if $gimme->current_list->index == 1 }}{{ include file="_tpl/img/img_square.tpl" }}{{ /if }}
                    <h4><a href="{{ url options="article" }}">{{ $gimme->article->name }}</a> {{ list_article_topics }}{{ if $gimme->current_list->at_beginning }}<em>/ {{ /if }}<a href="{{ url options="template topic.tpl" }}">{{ $gimme->topic->name }}</a>{{ if $gimme->current_list->at_end }}</em>{{ /if }}{{ /list_article_topics }}</h4>
                      <span class="time">{{ include file="_tpl/relative_date.tpl" date=$gimme->article->publish_date }}{{ if ! $gimme->article->content_accessible }} / <a href="{{ url options="article" }}">{{ #premium# }}</a>{{ /if }}</span>
                    </div>
{{ /list_articles }}
                </article>
{{ /list_sections }}
{{ /local }}                  
            
            </section><!-- / 6 articles grid -->