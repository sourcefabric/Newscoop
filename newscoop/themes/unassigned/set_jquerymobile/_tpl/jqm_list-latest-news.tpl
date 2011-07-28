    <ul data-role="listview" data-inset="true">
      <li data-role="list-divider">Latest News</li>
  {{ list_articles length="5" constraints="type is news" order="byPublishDate desc" ignore_issue="true" ignore_section="true"}}
      <li>{{ strip }}
        <a href="http://{{ $gimme->publication->site }}{{ uri options="article" }}">
          {{* if $gimme->current_list->index == 1 *}} 
          <img style="background: {{ if $gimme->article->has_image(2) }}url({{uri options="image 2 width 150 height 150"}}){{ else }}url({{uri options="image 1"}} width 150 height 150"){{ /if }} no-repeat center center; width: 80px; height: 80px"  />
          {{* /if *}}
          <h3>{{* if ! $gimme->article->content_accessible }}(subscribers only){{ /if *}}
          {{ $gimme->article->name }}</h3>
          <p>{{ $gimme->article->deck }}</p>
        </a>
      {{ /strip }}</li>                                                    
  {{ /list_articles }}
    </ul>