{{ if $gimme->article->content_accessible }}
  <div id="fb-root"></div><script src="http://connect.facebook.net/en_US/all.js#appId=100924830001723&amp;xfbml=1"></script><fb:like href="http://{{ $gimme->publication->site }}{{ uri }}" send="true" width="450" show_faces="true" font=""></fb:like>
  <script src="http://platform.twitter.com/widgets.js" type="text/javascript"></script>
  <a href="http://twitter.com/share" class="twitter-share-button" data-text="{{ $gimme->article->name }}" data-via="{{ $gimme->publication->name }}">Tweet</a>        
{{ /if }}