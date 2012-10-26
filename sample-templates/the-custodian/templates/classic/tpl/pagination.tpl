<div id="pagination">
{{ if $gimme->current_list->at_end && $gimme->current_list->count > $gimme->current_list->length }}
    {{ if $gimme->current_list->has_previous_elements }}
      <a href="{{ uri options="previous_items template classic/search-result.tpl" }}">Previous</a>
    {{ else }}
      Previous
    {{ /if }}
    |
    {{ if $gimme->current_list->has_next_elements }}
      <a href="{{ uri options="next_items template classic/search-result.tpl" }}">Next</a>
    {{ else }}
      Next
    {{ /if }}
{{ /if }}
</div>