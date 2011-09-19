    <div id="advert_300x250" class="wrap widget">
    {{ if $gimme->template->name == 'article.tpl' }}
        <a href="http://www.sourcefabric.org"><img src="{{ url static_file='_misc/300x250-airtime.png' }}" alt="advert" /></a>
    {{ else }}
        <a href="http://www.sourcefabric.org"><img src="{{ url static_file='_misc/300x250-newscoop.png' }}" alt="advert" /></a>
    {{ /if }}
  </div>
