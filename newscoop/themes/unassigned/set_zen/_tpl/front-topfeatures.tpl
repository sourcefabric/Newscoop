<div class="row features features-body">
  {{ local }}
  {{ unset_topic }}
  {{ list_articles length="3" ignore_issue="true" ignore_section="true" order="bydate desc" constraints="highlight is on" }}
      <div class="twocol thumbnail">
          <a href="{{ uri options="article" }}">
          <img src="{{ if $gimme->article->has_image(2) }}{{uri options="image 2 width 200"}}{{ else }}{{uri options="image 1 width 200" }}{{ /if }}" alt="{{ $gimme->article->name }}">
          </a>
      </div>
      <div class="twocol {{ if $gimme->current_list->index == 3 }} last{{ /if }}">
          <h4><a href="{{ uri options="article" }}" rel="bookmark">{{ include file="_tpl/article-icons.tpl" }}{{ $gimme->article->name }}</a></h4>
{{ include file="_tpl/link-readmore.tpl" }}
      </div>
  {{ /list_articles }}
  {{ /local }}
</div><!--.row--><!-- .features --> 