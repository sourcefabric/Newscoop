<ul class="paginator">
    {{if isset($paginator->previous)}}
    <li><a href="{{ $view->url(['page' => $paginator->previous]) }}">previous</a></li>
    {{else}}
    <li>previous</li>
    {{/if}}

    {{foreach $paginator->pagesInRange as $page}}
    {{if $paginator->current eq $page}}
    <li class="current"><a href="{{ $view->url(['page' => $page]) }}">{{ $page }}</a></li>
    {{else}}
    <li><a href="{{ $view->url(['page' => $page]) }}">{{ $page }}</a></li>
    {{/if}}   
    {{/foreach}}

    {{if isset($paginator->next)}}
    <li><a href="{{ $view->url(['page' => $paginator->next]) }}">next</a></li>
    {{else}}
    <li>next</li>
    {{/if}}
</ul>
