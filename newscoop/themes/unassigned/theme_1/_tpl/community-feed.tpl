{{* This will generate list of latest activity of community members *}}

{{ list_community_feeds length="10" }}

{{ if $gimme->current_list->at_beginning }}
<div class="community_ticker hidden-phone" >
    <h3>{{'communityFeed'|translate}}</h3>
        <ul>
{{ /if }}


        {{ $user=$gimme->community_feed->user }}

        {{ if $gimme->community_feed->type == 'user-register' && $user->uname }}
        <li class="registered">{{ include file="_tpl/relative_date.tpl" date=$gimme->community_feed->user->created }} <a{{ if $user->is_active }} href="{{ $view->url(['username' => $user->uname], 'user') }}"{{ /if }}>{{ $user->first_name }} {{ $user->last_name }}</a> {{'registered'|translate}}</li>
        {{ elseif $gimme->community_feed->type == 'comment-recommended' && $gimme->community_feed->comment->article }}
        <li class="commented">{{ include file="_tpl/relative_date.tpl" date=$gimme->community_feed->comment->submit_date }} {{'newCommentOn'|translate}} <a href="{{ $gimme->community_feed->comment->article->url }}">{{ $gimme->community_feed->comment->article->name }}</a></li>
        {{ /if }}

{{ if $gimme->current_list->at_end }}
        </ul>
</div>
{{ /if }}

{{ /list_community_feeds }}