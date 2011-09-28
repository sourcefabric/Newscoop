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
<form name="search_articles" action="{{ uri }}" method="post"
class="group" id="search-form" onSubmit="return asSubmit()">
   <input type="hidden" name="tpl" value="1341" />
   <input type="text" name="f_search_keywords" id="search-field"
value="" placeholder="Webcode, Geocode, Stichworte" />
   <button>Go</button>
   {{*<input type="submit" name="f_search_articles" value=">>>"
id="search-button" />     *}}
   <input type="hidden" name="f_search_articles" />
 </form>