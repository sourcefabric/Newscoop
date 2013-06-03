<!-- sidebar -->
<aside class="span4" id="sidebar">
    
    <!-- ADVERTISEMENTS -->
    <section class="advertisements visible-desktop">
        <a href="http://www.sourcefabric.org/" target="_blank"><img src="{{ url static_file='_img/sourcefabric-336x280.png' }}"></a>
    </section>

    <!-- TABS SIDEBAR -->
    {{ if $gimme->section->name != "Dialogue" }}
    <section class="sidebar-widget-tabs visible-desktop">
        <ul class="nav nav-tabs">
            <li class="active"> <a href="#last-comments" data-toggle="tab">{{ #latestComments# }}</a> </li>
            <li><a href="#poll" data-toggle="tab">{{ #pollTitle# }}</a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="last-comments">
                {{list_article_comments length="3" ignore_article="true" order="byDate desc"}}
                {{if $gimme->comment->content }}
                <div class="comment-box">
                    <div>
                        <a href="{{uri}}#comments">{{$gimme->comment->content|truncate:120}}</a>
                    </div>
                    <div class="comment-info">
                        <time class="timeago link-color" datetime="{{ $gimme->comment->submit_date}}">{{ $gimme->comment->submit_date }},</time> {{ #by# }}
                                    {{ if $gimme->comment->user->identifier }}
                                        <a href="http://{{ $gimme->publication->site }}/user/profile/{{ $gimme->comment->user->uname|urlencode }}">{{ $gimme->comment->user->uname }}</a>
                                    {{ else }}
                                        {{ $gimme->comment->nickname }} ({{ #anonymous# }})
                                    {{ /if }}
                    </div>
                </div>
                <hr>
                {{/if}}
                {{ /list_article_comments }}                
            </div>
            <div class="tab-pane" id="poll">
            {{ include file="_tpl/sidebar_poll.tpl" }}                                           
            </div>
        </div>
    </section>
    {{/if}}
</aside>  
