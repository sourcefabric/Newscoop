<nav class=" pagination">
    {{if isset($paginator->previous)}}

   <a href="{{ $view->url(['page' => $paginator->previous]) }}" class="arrow arrow_left" title="">{{'previous'|translate}}</a>
    {{/if}}

    <ul>
    {{foreach $paginator->pagesInRange as $page}}
    {{if $paginator->current eq $page}}
    <li class="current"><a>{{ $page }}</a></li>
    {{else}}
    <li><a href="{{ $view->url(['page' => $page]) }}">{{ $page }}</a></li>
    {{/if}}
    {{/foreach}}
</ul>
    {{if isset($paginator->next)}}


    <a href="{{ $view->url(['page' => $paginator->next]) }}" class="arrow arrow_right" title="">{{'next'|translate}}</a>
    {{/if}}
</nav>
