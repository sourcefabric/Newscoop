            <section class="grid-2">
                
            	<article>
                	<h2>{{ #mostRead# }}</h2>
                    <ul class="article-list">
{{ local }}
{{ set_current_issue }}
{{ list_articles length="5" ignore_section="true" order="bypopularity desc" constraints="type is news" }}
                     <li><h4><a href="{{ url options="article" }}">{{ $gimme->article->name }} ({{ $gimme->article->reads }})</a></h4></li>

{{ /list_articles }}
{{ /local }}                     
                    </ul>
                </article>
                
            	<article>
                	<h2>{{ #communityFeed# }}</h2>
                    <ul class="comments-list">

{{ list_community_feeds length="5" }}                    

        {{ $created=$gimme->community_feed->created }}
        {{ $user=$gimme->community_feed->user }}

        {{ if $gimme->community_feed->type == 'user-register' && $user->uname }}
        <li class="registered"><span class="time"><b>{{ include file="_tpl/relative_date.tpl" date=$created }} /</b> <a{{ if $user->is_active }} href="{{ $view->url(['username' => $user->uname], 'user') }}"{{ /if }}>{{ $user->first_name }} {{ $user->last_name }}</a> registered</span></li>
        {{ elseif $gimme->community_feed->type == 'comment-recommended' && $gimme->community_feed->comment->article }}
        <li class="commented"><span class="time"><b>{{ include file="_tpl/relative_date.tpl" date=$created }} /</b> {{ #newCommentOn# }} <a href="{{ $gimme->community_feed->comment->article->url }}">{{ $gimme->community_feed->comment->article->title }}</a></span></li>
        {{ /if }}

{{ /list_community_feeds }}

                    </ul>
                </article>
            
            </section><!-- / grid-2 -->
