        <div id="lang">           
            <ul>
{{ list_languages of_publication="true" }}            
                <li style="background: transparent url(http://{{ $gimme->publication->site }}/templates/_img/flags/{{ $gimme->language->code }}.png) no-repeat 5px center"><a href="{{ uri }}">{{ $gimme->language->name }}</a></li>
{{ /list_languages }}                
            </ul>
        </div>