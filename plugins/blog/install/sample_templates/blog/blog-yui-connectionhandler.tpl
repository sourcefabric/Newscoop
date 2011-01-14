var node_id = {{ $smarty.request.node_id }};

{{ if $smarty.request.f_blog_id }}

    var entries = {
    {{ local }}
    {{ set_language name=german }}
    {{ list_blogentries name="entries_list" length="10" }}
        '{{$gimme->blogentry->identifier }}' : '{{ $gimme->blogentry->title|escape:quotes }}',
    {{ /list_blogentries }}
    {{ /local }}
    }

{{ elseif $smarty.request.f_blogentry_id }}

    var comments = {
    {{ local }}
    {{ set_language name=german }}
    {{ list_blogcomments name="comments_list" length="10" }}
        '{{$gimme->blogcomment->identifier }}' : '{{ $gimme->blogcomment->title|escape:quotes }}',
    {{ /list_blogcomments }}
    {{ /local }}
    }

{{ elseif $smarty.request.f_blogcomment_id }}

    var comment = '{{ $gimme->blogcomment->content|regex_replace:"/\r?\n/":'<br>'|escape:quotes }}';
    
{{ else }}

    var blogs = {
    {{ local }}
    {{ set_language name=german }}
    {{ list_blogs name="blogs_list" length="10" }}
        '{{$gimme->blog->identifier }}' : '{{ $gimme->blog->title|escape:quotes }}',
    {{ /list_blogs }}
    {{ /local }}
    }

{{ /if }}
