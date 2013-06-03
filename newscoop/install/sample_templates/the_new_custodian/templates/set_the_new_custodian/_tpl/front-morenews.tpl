        <section id="morenews" class="row clearfix">
        <h2>{{ #moreNews# }}</h2>

            {{ list_sections length="3" constraints="number greater 30" }}

            <div class="fourcol{{ if $gimme->current_sections_list->at_end }} last{{ /if }}">            
              <h3>{{ $gimme->section->name }}</h3>
                  {{ list_articles length="2" ignore_issue="true" order="bypublishdate desc" }}
                  <section class="sixcol{{ if $gimme->current_list->at_end }} last{{ /if }}">
                    {{ include file="_tpl/img/img_250x167.tpl" where="front-bottom" }}                 
                    <h4><a href="{{ uri options="article" }}">{{ $gimme->article->name }}</a>{{ include file="_tpl/article-icons.tpl" }}</h4>
                    <p>{{ include file="_tpl/_edit-article.tpl" }}{{ $gimme->article->deck }}</p>
                </section>
                {{ /list_articles }}               
            </div>
            
            {{ /list_sections }}
            
        </section><!-- /#morenews -->
