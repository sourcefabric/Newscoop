
{{ include file="_tpl/_html-head.tpl" }}


    {{ include file="_tpl/header.tpl" }}

<div id="page" class="container">

  <!-- Content -->
  <section id="content">


{{ render file="_tpl/section_cont.tpl" params=$gimme->url->get_parameter("ls-art0") }}

    </section>
    <!-- End Content -->


  </div>

<script>
var section_number = {{$gimme->section->number}};
var lang = '{{ $gimme->language->code }}';
</script>

<script src="{{ url static_file='_js/infiniteScroll/progress.min.js' }}" type="text/javascript"></script>
  <script src="{{ url static_file='_js/under_backbone.js' }}" type="text/javascript"></script>
  <script src="{{ url static_file='_js/infiniteScroll/model_collection.js' }}" type="text/javascript"></script>
  <script src="{{ url static_file='_js/infiniteScroll/views.js' }}" type="text/javascript"></script>
  <script src="{{ url static_file='_js/infiniteScroll/app.js' }}" type="text/javascript"></script>

  {{ include file="_tpl/footer.tpl" }}
  {{ include file="_tpl/_html-foot.tpl" }}
