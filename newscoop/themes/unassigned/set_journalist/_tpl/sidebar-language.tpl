        <div id="lang">           
            <ul>
{{ list_languages of_publication="true" }}            
                <li style="background: transparent url({{ if $gimme->language->code == "en" }}{{ url static_file='_img/flags/en.png' }}{{ /if }}{{ if $gimme->language->code == "es" }}{{ url static_file='_img/flags/es.png' }}{{ /if }}{{ if $gimme->language->code == "ru" }}{{ url static_file='_img/flags/ru.png' }}{{ /if }}{{ if $gimme->language->code == "de" }}{{ url static_file='_img/flags/de.png' }}{{ /if }}) no-repeat 5px center"><a href="{{ uri }}">{{ $gimme->language->name }}</a></li>
{{ /list_languages }}                
            </ul>
        </div>