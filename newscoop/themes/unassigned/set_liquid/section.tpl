
{{ include file="_tpl/_html-head.tpl" }}


    {{ include file="_tpl/header.tpl" }}

<div id="page" class="container">

  <!-- Content -->
  <section id="content">


{{ render file="_tpl/section_standard_cont.tpl" params=$gimme->url->get_parameter("ls-art0") }}

    </section>
    <!-- End Content -->


  </div>


  {{ include file="_tpl/footer.tpl" }}
  {{ include file="_tpl/_html-foot.tpl" }}
