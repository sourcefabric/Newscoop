<div class="ui-body ui-body-c">
  <h3>More from...</h3>
  <div data-role="collapsible-set">
    {{ list_article_authors }}{{ strip }}
      <div data-role="collapsible" data-collapsed="true" data-theme="c">
        <h4>{{ $gimme->author->name }}</h4>
        <!--img style="max-width: 33%; margin: 0 10px 10px 0; float: left;" src="{{ $gimme->author->picture->imageurl }}" /-->
        <small><strong>{{ $gimme->author->name }}</strong>: {{ $gimme->author->biography->text }}</small>
        <ul data-role="listview" data-inset="true">
          {{ list_articles length="5" ignore_issue="true" ignore_section="true" order="bypublishdate desc" constraints="author is __current type is news" }}
            <li>{{ strip }}
              <a href="{{ uri options="article" }}">
              <p><strong>{{ $gimme->article->name }}</strong></p>
              <p>{{$gimme->article->deck}}</p>
              </a>
            {{ /strip }}</li>
          {{ /list_articles }}    
        </ul>
        <p style="display: block; clear: both;">
        </p>   
        <br clear="all" /> 
      </div><!-- collapsible -->
    {{ /strip }}{{ /list_article_authors }}
  </div><!-- collapsible-set -->
</div><!--body-->