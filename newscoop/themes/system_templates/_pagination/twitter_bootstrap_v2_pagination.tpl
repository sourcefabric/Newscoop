{{ dynamic }}
{{ math equation="ceil(totalCount / numItemsPerPage)" assign=pageCountCeil numItemsPerPage=$data['numItemsPerPage'] totalCount=$data['totalCount'] }}
{{ $pageCount =  $pageCountCeil|intval }}

{{ $current = $data['current'] }}
{{ math equation="abs(3)" assign=pageRange }}
{{ math equation="ceil(pageRange/2)" assign=delta pageRange=$pageRange }}

{{ if $pageCount < $current }}
    {{ $current = $pageCount }}
    {{ $currentPageNumber = $current }}
{{ /if }}

{{ if ($pageRange > $pageCount) }}
    {{ $pageRange = $pageCount }}
{{ /if }}

{{ if ($current - $delta) > ($pageCount - $pageRange) }}
    {{ $pages = range($pageCount - $pageRange + 1, $pageCount) }}
{{ else }}
    {{ if $current - $delta < 0 }}
        {{ $delta = $current }}
    {{ /if }}

    {{ $offset = $current - $delta }}
    {{ $pages = range($offset + 1, $offset + $pageRange) }}
{{ /if }}

{{ math equation="floor(pageRange/2)" assign=proximity pageRange=$pageRange }}
{{ $startPage = $current - $proximity }}

{{ $endPage = ($current + $proximity) }}

{{ if $startPage < 1 }}
    {{ math equation="min(endPage + (1 - startPage), pageCount)" startPage=$startPage endPage=$endPage pageCount=$pageCount assign=endPage }}
    {{ $startPage = 1 }}
{{ /if }}

{{ if ($endPage > $pageCount) }}
    {{ math equation="max(startPage - (endPage - pageCount), 1)" startPage=$startPage endPage=$endPage pageCount=$pageCount assign=startPage }}
    {{ $endPage = $pageCount }}
{{ /if }}

{{ if $pageCount > 1 }}
    <div class="pagination">
        <ul>
        {{ if ($current - 1) > 0 }}
            <li>
                <a href="{{ generate_url route=$data['route'] parameters=$data['route_params']|array_merge:[$data['pageParameterName'] => ($data['current'] - 1)] }}">&laquo;&nbsp;{{ 'Previous'|translate:'messages' }}</a>
            </li>
        {{ else }}
            <li class="disabled">
                <span>&laquo;&nbsp;{{ 'Previous'|translate:'messages' }}</span>
            </li>
        {{ /if }}

        {{ if $startPage > 1 }}
            <li>
                <a href="{{ generate_url route=$data['route'] parameters=$data['route_params']|array_merge:[$data['pageParameterName'] => 1] }}">1</a>
            </li>
            {{ if $startPage == 3 }}
                <li>
                    <a href="{{ generate_url route=$data['route'] parameters=$data['route_params']|array_merge:[$data['pageParameterName'] => 2] }}">2</a>
                </li>
            {{ elseif $startPage != 2 }}
            <li class="disabled">
                <span>&hellip;</span>
            </li>
            {{ /if }}
        {{ /if }}

        {{ foreach from=$pages item=page }}
            {{ if $page != $current }}
                <li>
                    <a href="{{ generate_url route=$data['route'] parameters=$data['route_params']|array_merge:[$data['pageParameterName'] => $page] }}">{{ $page }}</a>
                </li>
            {{ else }}
                <li class="active">
                    <span>{{ $page }}</span>
                </li>
            {{ /if }}
        {{ /foreach }}

        {{ if $pageCount > $endPage }}
            {{ if $pageCount > ($endPage + 1) }}
                {{ if $pageCount > ($endPage + 2) }}
                    <li class="disabled">
                        <span>&hellip;</span>
                    </li>
                {{ else }}
                    <li>
                        <a href="{{ generate_url route=$data['route'] parameters=$data['route_params']|array_merge:[$data['pageParameterName'] => ($pageCount -1)] }}">{{ $pageCount -1 }}</a>
                    </li>
                {{ /if }}
            {{ /if }}
            <li>
                <a href="{{ generate_url route=$data['route'] parameters=$data['route_params']|array_merge:[$data['pageParameterName'] =>$pageCount] }}">{{ $pageCount }}</a>
            </li>
        {{ /if }}

        {{ if ($current +1) <= $pageCount }}
            <li>
                <a href="{{ generate_url route=$data['route'] parameters=$data['route_params']|array_merge:[$data['pageParameterName'] => $current +1] }}">{{ 'Next'|translate:'messages' }}&nbsp;&raquo;</a>
            </li>
        {{ else }}
            <li class="disabled">
                <span>{{ 'Next'|translate:'messages' }}&nbsp;&raquo;</span>
            </li>
        {{ /if }}
        </ul>
    </div>
{{ /if }}
{{ /dynamic }}