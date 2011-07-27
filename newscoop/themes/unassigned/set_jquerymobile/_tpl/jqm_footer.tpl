<div data-role="footer" data-theme="">
<ul>       
{{ local }}
  {{ set_issue number="1" }}
  {{ set_section number="5" }} 
  {{ list_articles }}
    <li><a href="{{ uri options="article" }}">{{ $gimme->article->name }}</a></li>
  {{ /list_articles }}      
  {{ unset_section }}
{{ /local }}       
</ul>
<p>&copy; 2011 {{ $gimme->publication->name }}. Powered by <a href="http://newscoop.sourcefabric.org/" title="Newscoop">Newscoop</a>.</p>
</div><!-- /footer -->