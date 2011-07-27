<div class="row" id="footer">
  <div id="footerinner">
    <ul id="sections">
      {{ local }}
      {{ set_current_issue }}      
      {{ list_sections }}
      <li class="cat-item"><a href="{{ uri options="section" }}" title="View all posts in {{ $gimme->section->name }}">{{ $gimme->section->name }}</a></li>
      {{ /list_sections }}
      {{ /local }}
    </ul>

    <ul id="pages">
      {{ list_articles ignore_issue="true" ignore_section="true" constraints="issue is 1 section is 5"}}
      <li class="page_item"><a href="{{ uri options="article" }}" title="{{ $gimme->article->name }}">{{ $gimme->article->name }}</a></li>
      {{ /list_articles }}      
      {{ unset_section }}
      <li class="page_item"><a href="{{ uri options="template archive.tpl" }}" title="Archives">Archives</a></li>
      <li class="page_item"><a href="http://{{ $gimme->publication->site }}/admin">Log in</a></li>
      <li class="page_item"><a href="http://{{ $gimme->publication->site }}/?tpl=1283" title="Syndicate this site using RSS 2.0"><abbr title="Really Simple Syndication">RSS</abbr> Feed</a></li>
      <li style="border: medium none;" class="page_item"><a href="http://newscoop.sourcefabric.org/" title="Powered by Newscoop, open source, free and multilingual enterprise publishing system for news websites">Newscoop</a></li>
    </ul>
    <br />
    <div class="credits">
      &copy; 2011 {{ $gimme->publication->name }}. All Rights Reserved. Powered by <a href="http://newscoop.sourcefabric.org/" title="Newscoop">Newscoop</a>.
    </div>
  </div><!-- id="footerinner"-->
</div><!--row-->
