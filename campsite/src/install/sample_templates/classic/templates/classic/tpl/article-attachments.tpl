<style>
#attachments {
  border-top: 1px solid #999;
  border-bottom: 1px solid #999; 
  padding: 10px 0;
  margin: 10px 0; 
}

#attachments ul {
  list-style: none;
  padding: 0;
}

#attachments ul li {
  line-height: 50px;
  margin-left: 0;
  padding-left: 50px;
}
</style>

<div id="attachments">
          {{ list_article_attachments }}
          {{ if $campsite->current_list->at_beginning }}
          <h4>{{ if $campsite->language->name == "English" }}Downloads{{ else }}Descargas{{ /if }}:</h4>
          <ul>
          {{ /if }}
            <li style="background: url(/templates/classic/img/icons/file_{{ $campsite->attachment->extension }}.png) no-repeat left center"><a href="{{ uri options="articleattachment" }}">{{ $campsite->attachment->file_name }}, {{ $campsite->attachment->mime_type }}</a> ({{ $campsite->attachment->size_kb }}kb)</li>
          {{ if $campsite->current_list->at_end }}  
          </ul>
          {{ /if }}
          {{ /list_article_attachments }}
</div>          