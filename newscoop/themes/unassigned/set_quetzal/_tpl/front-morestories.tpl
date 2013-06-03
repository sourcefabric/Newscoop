			<section class="sections ninecol" id="topsections">
			
            {{ list_playlist_articles length="4" name="Front page" }}
            {{ if $gimme->current_list->index gt 1 }}
            
              <section>
              <h3>{{ $gimme->section->name }}</h3>
                <ul>
                    <li>
                        {{ include file="_tpl/img/img_150x150.tpl" }}
                        <div class="ninecol last">
                        <h4><a href="{{ url options="article"}}">{{ $gimme->article->name }}</a></h4>
                        <p class="article-info"><em>{{ #publishedOn# }} {{ $gimme->article->publish_date|camp_date_format:"%M %e, %Y" }} {{ #in# }} <a href="{{ uri options="section" }}">{{ $gimme->section->name }}</a></em><span class="right">{{ include file="_tpl/article-icons.tpl" }}</span></p>
                        <p>{{ include file="_tpl/_edit-article.tpl" }}{{ $gimme->article->deck }}</p>
                        </div>
                    </li>
                  {{ list_related_articles }}
                    <li>
                      <h5><a href="{{ uri options="article" }}">{{ $gimme->article->name }}</a>{{ include file="_tpl/article-icons.tpl" }}</h5>
                    </li>
                  {{ /list_related_articles }}
                </ul>
              </section>
            
            {{ /if }}            
            {{ /list_playlist_articles }}
            
			</section><!-- /.sections -->            