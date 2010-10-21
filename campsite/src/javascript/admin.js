$(document).ready(function() {
    var terms = [];
    $('ul.tree.sortable strong').each(function() {
        terms.push($(this).text());
    });

    $('.autocomplete.topics').autocomplete({
        source: function(request, response) {
            var match = [];
            var re = new RegExp(request.term, "i");
            for (i = 0; i < terms.length; i++) {
                if (terms[i].search(re) >= 0) {
                    match.push(terms[i]);
                }
            }
            response(match);
        }
    });
});
