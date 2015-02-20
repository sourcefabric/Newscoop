

<script>
if(galleryLinksContainer===undefined){
 var galleryLinksContainer = [];
 var galleryLinksOriginalContainer = [];
 var galleryLinks = [];
 var galleryLinksOriginal = [];
 var videoNumber = false;


}
</script>

{{ assign var="i" value=0 }}
{{ foreach $gimme->article->slideshows as $slideshow name=slideshowlist }}

<script>
galleryLinks = [];
galleryLinksOriginal = [];
</script>


{{ foreach $slideshow->items as $item name=insideslideshow }}
{{ if $smarty.foreach.insideslideshow.first}}
<div class="image-slideshow">
 <a class="fullscreenButton hidden-phone" data-gallery="{{ $i }}"></a>
 <div id="blueimp-image-carousel_{{ $i }}" class="blueimp-gallery blueimp-gallery-carousel blueimp-gallery-controls">
   <div class="slides"></div>

   <a class="prev">‹</a>
   <a class="next">›</a>
   <ol class="indicator"></ol>
</div>
<p class="slide-caption"></p>
</div>


<script>
{{ /if }}

{{ if $item->is_image }}



galleryLinksOriginal.push({

   title: '{{$item->caption|escape }}',
   href: '/get_img?ImageWidth=1200&ImageHeight=800&ImageId={{ $item->image->id }}',
   type: 'image/jpeg'

});

galleryLinks.push({

   title: '{{$item->caption|escape }}',
   href: '{{ $item->image->src }}',
   type: 'image/jpeg'

});



{{ else }}




videoNumber = youtube_parser("{{ $item->video->url }}");
//youtube
if( videoNumber ){
   galleryLinks.push({
    title: '{{$item->caption|escape }}',
    href: '{{$item->video->url}}',
    type: 'text/html',
    youtube: videoNumber,
    poster: 'http://img.youtube.com/vi/'+videoNumber+'/0.jpg'

});

   galleryLinksOriginal.push({
    title: '{{$item->caption|escape }}',
    href: '{{$item->video->url}}',
    type: 'text/html',
    youtube: videoNumber,
    poster: 'http://img.youtube.com/vi/'+videoNumber+'/0.jpg'

});

}else{



    videoNumber = vimeo_parser("{{ $item->video->url }}");

    //vimeo
    if (videoNumber){



            var vimeoObj = new Object();
            vimeoObj.title = '{{$item->caption|escape }}';
            vimeoObj.href = '{{$item->video->url}}';
            vimeoObj.type = 'text/html';
            vimeoObj.vimeo = videoNumber;
            vimeoObj.poster = vimeo_thumb(videoNumber);



            galleryLinks.push(vimeoObj);

            galleryLinksOriginal.push(vimeoObj);






    }



}











{{ /if }}

{{ if $smarty.foreach.insideslideshow.last }}

galleryLinksContainer.push(galleryLinks);
galleryLinksOriginalContainer.push(galleryLinksOriginal);
</script>

{{/if}}
{{ /foreach }}





{{ /foreach }}