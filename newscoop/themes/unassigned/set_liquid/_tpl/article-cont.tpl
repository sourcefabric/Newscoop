

   <div class="row article_content">
     <article class="news_item span8 ">


      {{* This is to check if article is divided into pages *}}
      {{ assign var="showStuff" value=0 }}
      {{ if !($gimme->article->subtitles_count(full_text) gt 1) || ($gimme->article->subtitles_count(full_text) gt 1 && $gimme->article->current_subtitle_no(full_text) == 0) }}
      {{ assign var="showStuff" value=1 }}
      {{ /if }}

      {{if $showStuff}}
      {{ image rendition="article" }}

      <div class="thumbnail content_text">

       <img src="{{ $image->src }}"  alt="{{ $image->caption }} (photo: {{ $image->photographer }})" alt="" />
       <h6 class="caption ">{{ $image->caption }}</h6>
     </div>
     {{/image}}
     {{/if}}

     <div class="content content_text">
       <h6 class="info">{{ $gimme->article->publish_date|camp_date_format:"%d.%m.%Y, %H:%i" }}</h6>
        <h2 class="title">{{$gimme->article->name}}</h2>
        <h6 class="topics">
          {{ list_article_topics }}<a href="{{ url options="template topic.tpl" }}">{{ $gimme->topic->name }}</a>{{ if $gimme->current_list->at_end }}</p>{{ else }}, {{ /if }}
          {{ /list_article_topics }}

        </h6>
        {{ include file="_tpl/_edit-article.tpl" }}


        {{ if !$gimme->article->content_accessible }}
          <p>{{'infoOnLockedArticles'|translate}}</p>
        {{ else }}



        {{ $bodyAr=explode("</p>", $gimme->article->full_text, 2) }}


        {{ $bodyAr[0] }}
      </p>


       {{ $bodyAr[1] }}




                        {{if $showStuff}}

                        {{ include file="_tpl/article-slideshow.tpl" }}

                        {{/if}}


                        {{* article pagination *}}
                        {{ if $gimme->article->subtitles_count(full_text) gt 1 }}
                        {{ if !($smarty.get['st-full_text'] == "all") }}

                        <div class="bottom_nav pagination">

                          {{ list_subtitles field_name="full_text" }}
                          {{ if $gimme->current_list->at_beginning }}

                          {{ if $gimme->article->full_text->has_previous_subtitles }}
                          <a href="{{ url options="previous_subtitle full_text" }}" class="arrow arrow_left" title="">{{'previous'|translate}}
                          </a>


                          {{ /if }}

                          <div class="numbers">
                           <ul>

                            {{ /if }}

                            <li{{ if ($gimme->article->current_subtitle_no(full_text)+1) == $gimme->current_list->index }} class="current"{{ /if }}><a href="{{ url }}">{{ $gimme->current_list->index }}</a></li>

                            {{ if $gimme->current_list->at_end }}
                          </ul>
                        </div>
                        {{ if $gimme->article->full_text->has_next_subtitles }}
                        <a href="{{ url options="next_subtitle full_text" }}" class="arrow arrow_right" title="">{{'next'|translate}}</a>
                        {{ /if }}
                        {{ /if }}
                        {{ /list_subtitles }}
                      </div>
                      {{ /if }}
                      {{ /if }}
                      {{* /article pagination *}}




                    <script>
                    jQuery(document).ready(function() {

                      Socialite.load(".social_buttons");

                    });

                    </script>


                    </div>

                      <div class="rating_social">


                          <script src="{{ url static_file='_js/socialite.min.js' }}" type="text/javascript"></script>



                          <div class="social_buttons float_right">
                            <ul>
                             <li class="float_right margin_left_5">
                               <a href="http://twitter.com/share" class="socialite twitter-share" data-text="{{$gimme->article->name|strip_tags|escape}}" data-url="{{url options="article"}}" data-count="vertical"  rel="nofollow" target="_blank"><span class="vhidden">Share on Twitter</span></a>
                             </li>
                             <li class="float_right margin_left_5">
                              <a href="https://plus.google.com/share?url={{url options="article"}}" class="socialite googleplus-one" data-size="tall" data-href="{{url options="article"}}" rel="nofollow" target="_blank"><span class="vhidden">Share on Google+</span></a>
                            </li>
                            <li class="float_right margin_left_5">
                              <a href="http://www.facebook.com/sharer.php?u={{url options="article"}}&amp;t={{$gimme->article->name|strip_tags|escape}}" class="socialite facebook-like" data-href="{{url options="article"}}" data-send="false" data-layout="box_count" data-width="60" data-show-faces="false" rel="nofollow" target="_blank"><span class="vhidden">Share on Facebook</span></a>
                            </li>
                          </ul>

                        </div>
                        {{ include file="_tpl/article-rating.tpl" }}

                      </div>

                    <span class="clear"></span>

                    {{ include file="_tpl/article-comments.tpl" }}



                    {{ /if }}

                </article>
                <aside class="span4 news_item">


                       {{list_article_authors }}

                       {{if $gimme->current_list->at_beginning}}
                       <div class="article_authors">
                        <h3>{{'writtenBy'|translate}}</h3>
                        {{/if}}
                        <div class="author_item row">

                       {{ if $gimme->author->user->image(60, 80) }}

                       <a href="{{ $view->url(['username' => $gimme->author->user->uname], 'user') }}" class="author_image span1">
                        <img alt="{{ $gimme->author->user->uname|escape }}" src="{{ $gimme->author->user->image(60, 80) }}"  />
                      </a>
                      {{ elseif $gimme->author->picture->imageurl }}

                      <img src="{{ $gimme->author->picture->imageurl }}" alt="{{ $gimme->author->name }}" width="60" class="author_image span1"  />

                      {{ /if }}
                      <div class="span3 author_info">
                      {{ if $gimme->author->user->defined }}
                          <a href="{{ $view->url(['username' => $gimme->author->user->uname], 'user') }}">
                      {{ /if }}
                        {{ if $gimme->author->biography->first_name }}
                            {{ $gimme->author->biography->first_name }}
                            {{$gimme->author->biography->last_name }}
                        {{ else }}
                            {{ $gimme->author->name }}
                        {{ /if }}

                        {{ if $gimme->author->user->defined }}
                      </a>
                      {{ /if }}
                      <p>
                        {{$gimme->author->biography->text}}
                      </p>
                    </div>
                    </div>

                    {{if $gimme->current_list->at_end}}
                  </div>
                    {{/if}}

                      {{/list_article_authors}}


                        <img style="margin: 20px auto 0 auto; display:block;" src="{{url static_file="_img/ads/300x250.png"}}" alt="Sourcefabric"/>


                   {{ if $gimme->article->has_map }}


                   <div class="article_side_image margin_top_20 ">
                    {{ map show_original_map="false" show_reset_link="false" show_locations_list="false" width="240" height="214" }}
                  </div>
                  {{ /if }}


                  {{ if $gimme->article->has_attachments }}
                  <div class="margin_top_20 article_side_attachments">


                   <h3>{{'attachments'|translate}}</h3>
                   {{ list_article_attachments }}


                   <div class="attachment margin_top_20">
                   {{ if ($gimme->attachment->extension == mp3) || ($gimme->attachment->extension == oga) }}




                   <audio controls>
                     <source src="{{ url options="articleattachment" }}" type="{{ $gimme->attachment->mime_type }}">
                     </audio>





                     {{ elseif $gimme->attachment->extension == ogv || $gimme->attachment->extension == ogg || $gimme->attachment->extension == mp4 || $gimme->attachment->extension == webm }}

                     <video id="video_{{ $gimme->current_list->index }}" class="video-js vjs-default-skin" controls preload="auto" width="100%" data-setup='{ "loop": "false" }'>

                       <source src="{{ url options="articleattachment" }}" type='{{ $gimme->attachment->mime_type }}' />
                       </video>





                       {{ else }}


                      {{'download'|translate}} <a href="{{ url options="articleattachment" }}" >{{ $gimme->attachment->file_name }} ({{ $gimme->attachment->size_kb }}kb)</a>

                       {{ /if }}
                     </div>
                       {{ /list_article_attachments }}
                     </div>
                     {{ /if }}




                     {{ list_related_articles }}
                     {{if $gimme->current_list->at_beginning}}
                     <div class="related_articles margin_top_20">
                      <h3 >{{'relatedArticles'|translate}}</h3>
                      <ul>
                        {{/if}}
                        <li><a href="{{url options="article"}}">{{$gimme->article->name}}</a></li>

                        {{if $gimme->current_list->at_end}}
                      </ul>
                    </div>
                    {{/if}}
                    {{/list_related_articles }}

                </aside>

              </div>

