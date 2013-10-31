<!-- MAIN ARTICLE -->
<div class="span8 article-container">
    <article class="main-article single">                                    
        {{ if $gimme->article->content_accessible }} 
        <header>
            <span class="article-info">
            {{ if $gimme->article->type_name == "news" }}
                <time datetime="{{$gimme->article->publish_date|date_format:"%Y-%m-%dT%H:%MZ"}}">{{ $gimme->article->publish_date|camp_date_format:"%d %M %Y" }}</time> 
                By {{ list_article_authors }} {{ if $gimme->author->user->defined}}<a href="{{ $view->url(['username' => $gimme->author->user->uname], 'user') }}" class="link-color">{{/if}}{{ $gimme->author->name }}{{if $gimme->author->user->defined }}</a>{{/if}} ({{ $gimme->author->type|lower }}) {{ if !$gimme->current_list->at_end }}, {{/if}}{{/list_article_authors}}
            {{ if $gimme->article->has_map }}
            <span class="pull-right visible-desktop">{{ #locations# }}: {{ list_article_locations }}{{ if $gimme->location->enabled }}{{ $gimme->location->name }}{{ if $gimme->current_list->at_end }}{{ else }}, {{ /if }}{{ /if }}{{ /list_article_locations }}</span> <span class="visible-phone">{{ #locations# }}: {{ list_article_locations }}{{ if $gimme->location->enabled }}{{ $gimme->location->name }}{{ if !$gimme->current_list->at_end }}, {{ /if }}{{ /if }}{{ /list_article_locations }}</span>
            {{/if}}
             {{ list_article_topics }}{{ if $gimme->current_list->at_beginning }}<div>{{ #topics# }} {{ /if }}<a class="link-color" href="{{ url options="template topic.tpl" }}">{{ $gimme->topic->name }}</a>{{ if $gimme->current_list->at_end }}</div>{{ else }}, {{ /if }}{{ /list_article_topics }}
            {{ /if }}
            </span>
            <div class="clearfix"></div>
            {{ if $gimme->article->type_name == "news" }}
                {{ include file="_tpl/article-slideshows.tpl" }}
            {{ else }}
                <h1 style="width:100%;">{{ $gimme->article->name }}</h1>
            {{ /if }}
        </header>

        <section class="article-content">
            {{ if $gimme->article->type_name == "news" }}
                {{ assign var="has_slideshow" value=0 }}
                {{ foreach $gimme->article->slideshows as $slideshow }}
                {{ assign var="has_slideshow" value=$has_slideshow+1 }}
                {{ /foreach }}

                {{ if !$has_slideshow > 0}}
                {{ include file="_tpl/img/img_300x300.tpl" where="mobile"}}
                {{ /if }}
            {{ /if }}

            {{ include file="_tpl/_edit-article.tpl" }}{{ $gimme->article->full_text }}

            {{ include file="_tpl/article-attachments.tpl"}}
        </section>

        {{ if $gimme->article->type_name == "news" }}
        <!-- Social Buttons BEGIN -->
        <div class="addthis_toolbox addthis_default_style">

            <!--- Twitter button -->
            <div style="float:left; width:90px;">
            <a href="https://twitter.com/share" class="twitter-share-button" data-dnt="true">Tweet</a>
            </div>
            <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');
            </script>

            <!--- Facebook button -->
            <div id="fb-root"></div><script src="http://connect.facebook.net/en_US/all.js#appId=100924830001723&amp;xfbml=1"></script><fb:like href="http://{{ $gimme->publication->site }}{{ uri }}" send="false" layout="button_count" show_faces="false"></fb:like> 
    
            <!--- Google+ button -->
            <div class="g-plusone" data-size="medium" data-annotation="inline" data-width="120"></div>
            <script type="text/javascript">
              (function() {
                var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
                po.src = 'https://apis.google.com/js/plusone.js';
                var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
              })();
            </script>

        </div>
        <!-- Social Buttons END -->

        {{ include file="_tpl/article-rating.tpl" }}

        {{ include file="_tpl/article-comments.tpl" }}
        
        {{ /if }} {{* end if type content *}}

        {{ else }}
        <header>
            <div class="alert">{{ #infoOnLockedArticles# }}</div>
        </header>
        {{ /if }} {{* end content_accesible *}}

    </article>
</div>
