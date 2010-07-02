<div id="footer"><div class="block-label" id="block-title-mostpopular">Most popular:</div>
<div id="block-articles-mostpopular">
<div id="block-articles-mostpopularinner">
{{ list_articles length="5" order="byPopularity desc" ignore_section="true" }}
<div id="block-articles-mostpopular-item" class="block-articles-mostpopular-item-{{ $campsite->section->number }}">
<div id="block-articles-mostpopular-iteminner" class="block-articles-mostpopular-iteminner-{{ $campsite->section->number }}">
<a href="{{ uri }}" class="block-mostpopular-link">
<div id="block-mostpopular-section" class="block-mostpopular-section-{{ $campsite->section->number }}">{{ $campsite->section->name }}</div>
<div id="block-mostpopular-title">{{ $campsite->article->name }}</div>
<div id="block-mostpopular-deck">{{ $campsite->article->deck }}</div>
<!--div class="block-mostpopular-link" id="block-mostpopular-counter">({{ $campsite->article->reads }} reads)</div-->
</a>
</div><!-- id="block-articles-mostpopular-iteminner" -->
</div><!-- id="block-articles-mostpopular-item" -->
{{ /list_articles }}
</div><!-- id="block-articles-mostpopularinner" -->
</div><!-- id="block-articles-mostpopular" -->
<div id="footerlinks">
<div id="footerlinksinner">
<center>
          <a href="{{ uri options="issue" }}">Home<!-- home --></a>
      {{ list_sections name="sections" constraints="number greater_equal 10 number smaller_equal 120" }}
          <a href="{{ uri }}">{{ $campsite->section->name }}</a>
      {{ /list_sections }}
</center>
</div><!-- id="footerlinksinner" -->
</div><!-- id="footerlinksinner" -->
</div>
