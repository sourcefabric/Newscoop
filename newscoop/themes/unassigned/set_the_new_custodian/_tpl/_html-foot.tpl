  <!-- JavaScript at the bottom for fast page loading -->

  <!-- scripts concatenated and minified via ant build script-->
  <script src="{{ url static_file='_js/plugins.js' }}"></script>
  <script src="{{ url static_file='_js/script.js' }}"></script>
  <script src="{{ url static_file='_js/libs/bootstrap-transition.js' }}"></script>
  <script src="{{ url static_file='_js/libs/bootstrap-collapse.js' }}"></script>
  {{ if $gimme->article->defined }}
      <script src="{{ url static_file='_js/article-rating.js' }}"></script>
  {{ /if }}
  <script type="text/javascript">
		$(".collapse").collapse()
  </script>
  
  <!-- end scripts-->


  <!--[if lt IE 7 ]>
    <script src="{{ url static_file='_js/libs/dd_belatedpng.js' }}"></script>
    <script>DD_belatedPNG.fix("img, .png_bg"); // Fix any <img> or .png_bg bg-images. Also, please read goo.gl/mZiyb </script>
  <![endif]-->


  <!-- mathiasbynens.be/notes/async-analytics-snippet Change UA-XXXXX-X to be your site's ID -->
  <script>
    var _gaq=[["_setAccount","UA-XXXXX-X"],["_trackPageview"]];
    (function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];g.async=1;
    g.src=("https:"==location.protocol?"//ssl":"//www")+".google-analytics.com/ga.js";
    s.parentNode.insertBefore(g,s)}(document,"script"));
  </script>

</body>
</html>