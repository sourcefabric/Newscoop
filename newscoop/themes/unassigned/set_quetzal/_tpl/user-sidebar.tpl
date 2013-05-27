<!-- sidebar -->
<aside class="span4 community-feed">                                
    <section class="widget-map widget-community-feed">
        <h4 class="widget-wrap">{{ #communityFeed# }}</h4>
        <div class="widget-wrap">
            {{ local }}
            {{ set_current_issue }}
            {{ list_articles length="10" order="byLastComment desc" constraints="type is news" }}
            {{ list_article_comments length="1" order="bydate desc"}}
            <div class="comm-entry">
                <p>{{ #newCommentOn# }}</p>
                <a href="{{ uri options="article" }}" class="link-color">{{ $gimme->article->name }}</a><br>
                <time class="timeago" datetime="{{ $gimme->comment->submit_date }}">{{ $gimme->comment->submit_date}}</time>
            </div>
            {{ /list_article_comments }}
            {{ /list_articles }}
            {{ /local }}
        </div>
    </section>
</aside>
