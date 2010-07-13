<div id="pagination">
{{ if $campsite->current_list->at_end && $campsite->current_list->count > $campsite->current_list->length }}
    {{ if $campsite->current_list->has_previous_elements }}
      <a href="{{ uri options="previous_items template classic/search-result.tpl" }}">Previous</a>
    {{ else }}
      Previous
    {{ /if }}
    |
    {{ if $campsite->current_list->has_next_elements }}
      <a href="{{ uri options="next_items template classic/search-result.tpl" }}">Next</a>
    {{ else }}
      Next
    {{ /if }}
{{ /if }}
</div>