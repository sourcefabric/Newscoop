


{{ include file="_tpl/_html-head.tpl" }}


    {{ include file="_tpl/header.tpl" }}



<div id="page" class="container">


 <div id="blueimp_fullscreen" class="blueimp-gallery blueimp-gallery-controls">
     <div class="slides"></div>

     <a class="prev">‹</a>
     <a class="next">›</a>
     <a class="close">×</a>
     <ol class="indicator"></ol>
     <div class="caption"></div>
 </div>

 <!-- Content -->
 <section id="content">

   <article class="row article_content">
     <div class="news_item span12">



      {{ image rendition="article" }}

      <div class="thumbnail content_text">

       <img src="{{ $image->src }}"  alt="{{ $image->caption }} (photo: {{ $image->photographer }})" alt="" />
       <h6 class="caption span2">{{ $image->caption }}</h6>
     </div>
     {{/image}}


     <div class="content content_text">


      <h6 class="info">{{ $gimme->article->publish_date|camp_date_format:"%d.%m.%Y, %H:%i" }}</h6>

        <h2 class="title">{{$gimme->article->name}}</h2>
        <h6 class="topics">
          {{ list_article_topics }}<a href="{{ url options="template topic.tpl" }}">{{ $gimme->topic->name }}</a>{{ if $gimme->current_list->at_end }}</p>{{ else }}, {{ /if }}
          {{ /list_article_topics }}

        </h6>
        {{ include file="_tpl/_edit-article.tpl" }}
        <em>{{ $gimme->article->teaser }}</em>




        {{ if $gimme->article->content_accessible }}






     <div class="span4 article_side_element align_left debate_author">
      {{ list_article_authors }}
                          {{ if $gimme->current_list->index == "1" }}

                                  <figure>
                                <img rel="resizable" alt="{{ $gimme->author->name }}" src="{{ $gimme->author->picture->imageurl }}" width=97 height=97 />
                                  </figure>
                                  <h5>{{'proArgumentsBy'|translate}}</h5>
                                  <p>{{ if $gimme->author->user->defined }}<a href="{{ $view->url(['username' => $gimme->author->user->uname], 'user') }}">{{ /if }}{{ $gimme->author->name }}{{ if $gimme->author->user->defined }}</a>{{ /if }}</p>
                                  <p class="debate_biography">{{ $gimme->author->biography->text|strip_tags:false|truncate:200 }}</p>

                           {{ /if }}
                           {{ /list_article_authors }}
     </div>

     <h3 class="debate_headline">PRO: {{ $gimme->article->pro_title }}</h3>
                     {{ $gimme->article->pro_text }}


     <div class="span4 article_side_element align_right debate_author">
       {{ list_article_authors }}
                                {{ if $gimme->current_list->index == "2" }}

                                        <figure>
                                      {{ if $gimme->author->user->defined || $gimme->author->picture->imageurl }}
                                      <img rel="resizable" alt="{{ $gimme->author->name }}" src="{{ $gimme->author->picture->imageurl }}" width=97 height=97 />
                                      {{ /if }}
                                        </figure>
                                        <h5>{{'contraArgumentsBy'|translate}}</h5>
                                        <p>{{ if $gimme->author->user->defined }}<a href="{{ $view->url(['username' => $gimme->author->user->uname], 'user') }}">{{ /if }}{{ $gimme->author->name }}{{ if $gimme->author->user->defined }}</a>{{ /if }}</p>
                                        <p class="debate_biography">{{ $gimme->author->biography->text|strip_tags:false|truncate:200 }}</p>

                                 {{ /if }}
                                 {{ /list_article_authors }}
     </div>


                     <h3 class="debate_headline" >CONTRA: {{ $gimme->article->contra_title }}</h3>
                     {{ $gimme->article->contra_text }}





                        {{ include file="_tpl/article-slideshow.tpl" }}


                        {{ include file="_tpl/debate-voting.tpl" }}




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

                    <span class="clear"></span>

                    <script>
                    jQuery(document).ready(function() {

                      Socialite.load(".social_buttons");

                    });

                    </script>

                    {{ include file="_tpl/article-comments.tpl" }}

                    {{ else }}
                                <p>{{'infoOnLockedArticles'|translate}}</p>
                    {{ /if }}
                  </div>

                </div>
              </article>


              {{ render file="_tpl/box-most_tabs.tpl"  issue=off section=off cache=600 }}


            </section>
            <!-- End Content -->













          </div>


          {{ include file="_tpl/footer.tpl" }}
          {{ include file="_tpl/_html-foot.tpl" }}