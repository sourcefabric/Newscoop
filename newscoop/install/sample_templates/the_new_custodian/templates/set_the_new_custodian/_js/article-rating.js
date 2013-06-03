<script type='text/javascript'>
 
$(document).ready(function() {
    
   // gets the initial rating
    $('.rate_widget').each(function(i) {
        var widget = this;
        var out_data = {
            f_article_number : $(widget).attr('id')
        };
        $.post(
            '/rating/show',
            out_data,
            function(INFO) {
                $(widget).data( 'fsr', INFO );
                set_votes(widget);
            },
            'json'
        );
    });
 
 
    // Handles the mouseover
    $('.ratings_stars').hover(
        function() {
            $(this).prevAll().andSelf().addClass('ratings_over');
            $(this).nextAll().removeClass('ratings_vote'); 
        },
        // Handles the mouseout
        function() {
            $(this).prevAll().andSelf().removeClass('ratings_over');
            // can't use 'this' because it wont contain the updated data
            set_votes($(this).parent());
        }
    );
 
    // actually records the vote
    $('.ratings_stars').bind('click', function() {
        var star = this;
        var widget = $(this).parent();
        var score = $(star).attr('class').match(/star_(\d+)/)[1];
 
        var clicked_data = {
            f_rating_score : score,
            f_article_number : $(star).parent().attr('id')
        };
        $.post(
            '/rating/save',
            clicked_data,
            function(INFO) {
                widget.data( 'fsr', INFO );
                set_votes(widget);
            },
            'json'
        ); 
    });
});
 
// binds the rating data returned by the rating controller 
// to the widget elements
function set_votes(widget) {
 
    var avg = $(widget).data('fsr').whole_avg;
    var votes = $(widget).data('fsr').number_votes;
    var exact = $(widget).data('fsr').dec_avg;
    var error = $(widget).data('fsr').error;
 
    $(widget).find('.star_' + avg).prevAll().andSelf().addClass('ratings_vote');
    $(widget).find('.star_' + avg).nextAll().removeClass('ratings_vote'); 
    $(widget).find('.total_votes').text( votes + ' votes recorded (' + exact + ' rating)' );
    $(widget).find('.rating_error').text( error );
}
 
</script>