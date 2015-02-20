

  {{ if $gimme->current_list->count > $gimme->current_list->length }}

  {{ $page=intval($gimme->url->get_parameter($gimme->current_list_id())) }}
  {{ $list_id=$gimme->current_list_id() }}

  <nav class="pagination">

   {{ unset_article }}

   {{ if $gimme->current_list->has_previous_elements }}

    <a href="{{ url options="template search.tpl previous_items" }}" class="arrow arrow_left" title="">{{'previous'|translate}}
   </a>


   {{ /if }}

   <div class="numbers">
    <ul>


   {{ list_search_results columns="10"   ignore_issue="true"  }}
   {{ if $gimme->current_list->count > 10 }}

   {{ if $gimme->current_list->column == 1 }}
   {{ if $gimme->current_list->row*10-10 == $page }}
   <li class="current active"><a>{{ $gimme->current_list->row }}</a></li>
   {{ elseif  $gimme->current_list->row*10-10 < $page+(5*10) &&  $gimme->current_list->row*10-10 > $page-(5*10) }}
   {{ unset_article }}
   <li><a href="{{ uri options="template search.tpl" }}?{{$list_id}}={{ $gimme->current_list->row*10-10 }}&tpid={{$gimme->topic->identifier}}">{{ $gimme->current_list->row }} </a></li>

   {{ /if }}
   {{ /if }}

     {{ /if }}
     {{ /list_search_results }}

</ul>
</div>


     {{ if $gimme->current_list->has_next_elements }}
     {{ unset_article }}

     <a href="{{ uri options="template search.tpl next_items" }}" class="arrow arrow_right" title="">{{'next'|translate}}</a>
     {{ /if }}


   </nav>

   {{ /if }}







