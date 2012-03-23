    <footer class="row">
      <ul class="clearfix main-nav">
      	 {{ set_current_issue }}    
          {{ list_sections }}       
          <li><a href="{{ url options="section" }}" title="{{ $gimme->section->name }}">{{ $gimme->section->name }}</a></li>
          {{ /list_sections }}      
          <li><a href="{{ $view->url(['controller' => 'user', 'action' => 'index'], 'default') }}" title="Community index">Community</a></li>
      </ul>
      <ul class="clearfix sec-nav">
          {{ list_articles ignore_issue="true" ignore_section="true" constraints="issue is 1 section is 5"}}
          <li><a href="{{ uri options="article" }}" title="{{ $gimme->article->name }}">{{ $gimme->article->name }}</a></li>
          {{ /list_articles }}          
          <li><a href="{{ uri options="template archive.tpl" }}">Archives</a></li>
      </ul>      
    <p>Powered by <a href="http://newscoop.sourcefabric.org/">Newscoop</a>. Designed by <a href="http://www.sourcefabric.org/">Sourcefabric</a>. Last update: March 6th, 2012</p>
    </footer>
