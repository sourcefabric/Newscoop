{{ include file="_tpl/_html-head.tpl" }}
<script src="{{$view->baseUrl()}}/js/jquery/jquery.mustache.js"></script>
{{literal}}
<script>
            $(document).ready(function () {

                $('.category').click(function () {
                    var catId = $(this).attr('alt');
                    init(catId);
                });

                var cat_template = $('#category-tmpl').text();


                $.getJSON("{{/literal}}{{$view->baseUrl()}}{{literal}}/notice-rest/category", {},function (data) {
                        var rendered_html = $.mustache(cat_template, data.data);
                        $('#category-output-target').html(rendered_html);

                })
                .success(function () {

                })
                .error(function () {
                alert('There was problem loading.');
                })
                .complete(function () {
                });

                    function init(id) {
                    var template = $('#notice-tmpl').text();

                    var req = $.getJSON("{{/literal}}{{$view->baseUrl()}}{{literal}}/notice-rest/index?q=" + id, {},
                    function (data) {
                    var rendered_html = $.mustache(template, data.data);
                    $('#output-target').html(rendered_html);

                    })
                    .success(function () {

                    })
                    .error(function () {
                    alert('There was problem loading.');
                    })
                    .complete(function () {
                    });

                    }

        init('');

$("a.all_of_kind").live("click", function(event) {
    $(this).parent().siblings().find('a.active').removeClass('active');
    var category_ids = new Array();
        $('a.active').each(function(index) {
                category_ids[index] = $(this).attr('id');
        });
$(this).addClass('active');

init(category_ids.join('/'));
return false; // "capture" the click

});

$("a.notice_category").live("click", function(event) {

   $(this).parent().siblings().find('a.active').removeClass('active');
   $(this).addClass('active');


// get notices in category
var category_ids = new Array();
$('a.active').each(function(index) {
    category_ids[index] = $(this).attr('id');
});


init(category_ids.join('/'));

    return false; // "capture" the click
});
        });
</script>

<script id="category-tmpl" type="text/html">
 {{#categories}}
<div class="category" style="width:600px;height:40px;padding:20px;">
        <h4>{{title}}</h4>
        <ul>
<li style="display:inline-block;">
        <a href="#" id="" class="all_of_kind">
Alle
</a>
</li>
        {{#children}}
        <li style="display:inline-block;">
        <a href="#" id="{{id}}" class="notice_category">
            {{title}}
        </a>
        </li>
        {{/children}}
        </ul>
</div>

{{/categories}}
</script>

<script id="notice-tmpl" type="text/html">
    <h3>Notices</h3>
    {{#notices}}
    <div class="notice" style="width:300px;height:150px; float:left;padding:20px;border:1px solid;">
        <h4>{{title}}</h4>
        <h5>{{firstname}} {{lastname}}</h5>

        <div>{{body}}</div>
        <ul>
            {{#categories}}
            <li>{{title}}</li>
            {{/categories}}
        </ul>
    </div>

    {{/notices}}
</script>
{{/literal}}
<style type="text/css" scoped>
a.active{
text-decoration: underline;
}
</style>
<body id="sectionpage">

<div id="container">

{{ include file="_tpl/header.tpl" }}

<div class="row clearfix" role="main">

    <div id="maincol" class="eightcol clearfix">

        <div id="category-output-target">
        loading
        </div>
        <div id="output-target">
            <a>Loading notices?</a>

        </div>
    </div>
    <!-- /#maincol -->

    <div id="sidebar" class="fourcol last">

    {{ include file="_tpl/sidebar-loginbox.tpl" }}

    {{ include file="_tpl/sidebar-most.tpl" }}

    {{ include file="_tpl/sidebar-community-feed.tpl" }}

    {{ include file="_tpl/_banner-sidebar.tpl" }}

    </div>
    <!-- /#sidebar -->

</div>

{{ include file="_tpl/footer.tpl" }}

</div> <!-- /#container -->

{{ include file="_tpl/_html-foot.tpl" }}