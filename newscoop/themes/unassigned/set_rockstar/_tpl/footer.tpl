      <div id="footer">
            
            <div id="bottom">
            
              <ul class="clearfix">
                  <li>
                      <ul>
         {{ set_current_issue }}    
          {{ list_sections }}       
          <li><a href="{{ url options="section" }}" title="{{ $gimme->section->name }}">{{ $gimme->section->name }}</a></li>
          {{ /list_sections }}                      
                      </ul>
                    </li>
                    <li>
                      <p>Rockstar Magazine<br />
                        Rockstreet 69 / 12345 Rocktown<br />
                        .+49 12 345 678 910</p>
                        <p><a href="#">email@rockstar-magazine.com</a></p>
                    </li>
                    <li>
                      <ul>
          {{ unset_topic }}
          {{ list_articles ignore_issue="true" ignore_section="true" constraints="issue is 1 section is 5"}}
          <li><a href="{{ uri options="article" }}" title="{{ $gimme->article->name }}">{{ $gimme->article->name }}</a></li>
          {{ /list_articles }}
                        </ul>
                    </li>
                    <li>
                      <p>&copy; {{ $gimme->publication->name }} {{ $smarty.now|camp_date_format:"%Y" }} - {{ #poweredBy# }}</p>
                        <ul>
          {{ list_articles ignore_issue="true" ignore_section="true" constraints="issue is 1 section is 15 type is link"}}
          <li><a href="{{ $gimme->article->url_address }}" title="{{ $gimme->article->name }}">{{ $gimme->article->name }}</a></li>
          {{ /list_articles }}                        
                        </ul>
                    </li>
                </ul>
            
            </div>
        
        </div>