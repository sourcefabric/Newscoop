            <section id="highlights" class="threecol last">
              <h3>{{ #highlights# }}</h3>
                <ul>

{{ list_articles length="4" ignore_issue="true" ignore_section="true" order="bydate desc" constraints="highlight is on" }}                
                    <li>
                        {{ include file="_tpl/img/img_250x167.tpl" where="highlights" }}
                        <h4>{{ include file="_tpl/_edit-article.tpl" }}<a href="{{ uri options="article" }}">{{ $gimme->article->name }}</a>{{ include file="_tpl/article-icons.tpl" }}</h4>
                    </li>
{{ /list_articles }}
                                      
                </ul>
            </section><!-- /#highlights -->