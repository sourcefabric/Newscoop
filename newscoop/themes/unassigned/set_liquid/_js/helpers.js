 function youtube_parser(url) {
     var regExp = /^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#\&\?]*).*/;
     var match = url.match(regExp);
     if (match && match[7].length == 11) {
         return match[7];
     } else {
         return false;
     }
 }

 function vimeo_parser(url) {

     var regExp = /http:\/\/(www\.)?vimeo.com\/(\d+)($|\/)/;

     var match = url.match(regExp);

     if (match) {
         return match[2];


     } else {
         return false;
     }

 }

 function vimeo_thumb(vNumber) {
    var res;
    $.ajax({
         url: 'http://vimeo.com/api/v2/video/' + vNumber + '.json',
         success: function(result) {

             res = result[0].thumbnail_large;
         },
         error: function(result) {
             res  = false;
         },
         async:false
     });
    return res;

 }


