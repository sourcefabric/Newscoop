<div class="span8 more-news-tabs">
    <!-- MORE NEWS TABS -->
    <div class="hidden-phone">
        <ul class="nav nav-tabs">
            {{ assign var=currentsection value=$gimme->section->number}}
            {{ if $currentsection }}
            {{ assign var=constraints value="number not $currentsection"}}
            {{ else }}
            {{ assign var=constraints value=""}}
            {{ /if }}
        
            <h4>{{ #moreNews# }} </h4>
            {{ list_sections constraints=$constraints}}
            {{ if $gimme->current_list->at_beginning }}
            <li class="active">
                <a href="#tab{{ $gimme->current_list->index }}" data-toggle="tab">{{ $gimme->section->name}}</a>
            </li>
            {{else}}
            <li><a href="#tab{{ $gimme->current_list->index }}" data-toggle="tab">{{ $gimme->section->name}}</a></li>
            {{/if}}
            {{/list_sections}}
        </ul>
        <div class="tabWrap">
            <div class="tab-content">
                {{ list_sections constraints=$constraints}}
                {{ if $gimme->current_list->at_beginning }}
                <div class="tab-pane active" id="tab{{ $gimme->current_list->index }}">
                {{else}}
                <div class="tab-pane" id="tab{{ $gimme->current_list->index }}">
                {{/if}}
                    {{ list_articles length="4" order="byPublishDate desc" }}
                    <div class="article-content">
                        {{ include file='_tpl/img/img_70x45.tpl'}}
                        <div class="article-excerpt pull-left">
                            <a href="{{ uri options="article"}}" class="title">
                                {{ $gimme->article->name}}
                            </a>               
                                {{ $gimme->article->full_text|truncate:100:"...":true }}
                        </div>
                        <div class="clearfix"></div>
                        <div class="article-links">
                            <hr>
                            <a href="{{ url options="article"}}#comments" class="comments-link">{{ $gimme->article->comment_count }} {{ #comments# }}</a> | 
                            <a href="{{ uri options="article"}}" class="link-color">{{ #readMore# }}</a>
                            <span class="article-date pull-right">
                                <time datetime="{{ $gimme->article->publish_date|date_format:"%Y-%m-%dT%H:%MZ" }}">{{ $gimme->article->publish_date|camp_date_format:"%M %e, %Y" }}</time>
                            </span>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                    {{ /list_articles }}
                </div>
                {{/list_sections}}
            </div>
        </div>
    </div>
</div>
