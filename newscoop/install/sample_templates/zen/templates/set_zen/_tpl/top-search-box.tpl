<script type="text/javascript">
function isWebcode(value) {
    var webcode =/^@[a-zA-Z]{5,6}$/;
    if (value.search(webcode) == -1) {
        return false;
    } else {
        return true;
    }
}
function asSubmit() {
    var searchValue = document.getElementById('search-field').value;
    if (isWebcode(searchValue) == true) {
        var location = 'http://{{ $gimme->publication->site }}/' + searchValue;
        window.location = location;
        return false;
    } else {
        return true;
    }
}
</script>

{{ search_form html_code="return asSubmit()" template="search.tpl" submit_button="Go" }}
   <input type="text" name="f_search_keywords" id="search-field"
value="" placeholder="Webcode, Geocode, Stichworte" />
{{ /search_form }}
