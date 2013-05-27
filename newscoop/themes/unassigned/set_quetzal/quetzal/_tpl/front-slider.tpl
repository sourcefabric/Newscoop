<section id="quetzal-slideshow">
    <div class="cycle-slideshow" 
        data-cycle-fx="fade" 
        data-cycle-timeout="5000"                        
        data-cycle-slides="> div.quetzal-slide"
        data-cycle-pager="#no-template-pager"
        data-cycle-pause-on-hover="true"
        data-cycle-pager-template=""
        data-cycle-log="false">
        {{ list_articles length="3" ignore_issue="true" ignore_section="true" order="bydate desc" constraints="highlight is on" }}   
        {{ if $gimme->current_list->at_beginning }}
        <div class="quetzal-slide first" style="background-image: url({{ include file="_tpl/img/img_960x300.tpl" }})"><br class="visible-desktop">
        {{ else }}
        <div class="quetzal-slide" style="background-image: url({{ include file="_tpl/img/img_960x300.tpl" }})"><br class="visible-desktop">
        {{ /if }}
            <div class="slide-title">
                <span class="link-color">{{ $gimme->article->section->name }}</span>
                <h3><a href="{{ uri options='article'}}">{{ $gimme->article->name}}</a></h3>
            </div>
            <div class="slide-description hidden-phone">
                {{ $gimme->article->full_text|truncate:100:"...":true }}
                <div class="slide-time"><time datetime="{{ $gimme->article->publish_date|date_format:"%Y-%m-%dT%H:%MZ" }}">{{ $gimme->article->publish_date|camp_date_format:"%M %e, %Y" }}</time>
                </div>
                <a class="read-more link-color" href="{{ uri options='article'}}">Read more +</a>
            </div>
        </div>
        {{ /list_articles}}
    </div>

    <div id="no-template-pager" class="cycle-pager external quetzal-slideshow-pager pager-default">
        {{ list_articles length="3" ignore_issue="true" ignore_section="true" order="bydate desc" constraints="highlight is on" }}   
        <a href="">{{ include file="_tpl/img/img_68x45.tpl" }}</a>
        {{ /list_articles}}
    </div>

    <!-- POPULAR NEWS -->
    <div class="quetzal-popular-news visible-desktop">
        <h3>{{ #mostPopularNews# }}</h3>
        {{ local }} 
        {{ set_current_issue }} 
        {{ list_articles length="4" order="bypopularity desc" constraints="type is news" }}
        <div class="mgzn-news-box">
            <a href="{{ uri options="article" }}">{{ include file='_tpl/img/img_72x46.tpl'}}</a>
            <div class="mgzn-news-info pull-right">
                <span><b>{{ $gimme->article->section->name }}</b></span><br>
                <a href="{{ uri options="article" }}">{{ $gimme->article->name }}</a>
            </div>
            <div class="clearfix"></div>
        </div>
        {{ /list_articles }} 
        {{ /local }}
    </div>
</section>
