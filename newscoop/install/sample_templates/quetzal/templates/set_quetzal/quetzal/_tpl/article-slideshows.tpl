{{ foreach $gimme->article->slideshows as $slideshow }}
<h1 class="full-width">{{ $gimme->article->name }}</h1>
          <h6>{{ $slideshow->headline }}</h6>
          <div id="gallery">
          	{{ assign var="style" value='true' }}
{{ assign var="counter" value=0 }}              
{{ foreach $slideshow->items as $item }}      
{{ assign var="counter" value=$counter+1 }}
              <img src="{{ $item->image->src }}" data-title="{{ $item->caption }}" />                
{{ /foreach }}

          </div>
          <!-- Gallery vendor plugin -->
          <script>
            Galleria.loadTheme('{{ url static_file='_js/vendor/galleria/themes/classic/galleria.classic.min.js'}}');
            Galleria.run('#gallery');
          </script>
{{foreachelse}}
            {{ include file="_tpl/img/img_300x300.tpl"}}
            <h1>{{ $gimme->article->name }}</h1>
{{ /foreach }}

