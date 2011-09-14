<ul>

{{if isset($paginator->previous)}}
    {{ $link_data.page = $paginator->previous }}
    <li><a href="{{ $view->url($link_data, $link_name) }}">previous</a></li>
{{else}}
    <li>previous</li>
{{/if}}

{{foreach $paginator->pagesInRange as $page}}
    
    {{if $paginator->current eq $page}}
        <li class="current"><a href="{{ $view->url($link_data, $link_name) }}">{{ $page }}</a></li>
    {{else}}
        <li><a href="{{ $view->url($link_data, $link_name) }}">{{ $page }}</a></li>
    {{/if}}   
{{/foreach}}

{{if isset($paginator->next)}}
    {{ $link_data.page = $paginator->next }}
    <li><a href="{{ $view->url($link_data, $link_name) }}">next</a></li>
{{else}}
    <li>next</li>
{{/if}}

</ul>