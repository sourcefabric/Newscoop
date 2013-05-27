<div class="article_rating">
    <h4>Article rating:</h4>
    <div id="{{ $gimme->article->number }}" class="rate_widget">
        <ul class="stars">
            <li class="star_1 ratings_stars"></li>
            <li class="star_2 ratings_stars"></li>
            <li class="star_3 ratings_stars"></li>
            <li class="star_4 ratings_stars"></li>
            <li class="star_5 ratings_stars"></li>
        </ul>
        <p class="total_votes">{{ #voteData# }}</p>
        <p class="rating_error"></p>
    </div>
</div>
<script>
$(document).ready(function() {
    
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


    $('.ratings_stars').hover(
        // Handles the mouseover
        function() {
            $(this).prevAll().andSelf().addClass('ratings_over');
            $(this).nextAll().removeClass('ratings_vote'); 
        },
        // Handles the mouseout
        function() {
            $(this).prevAll().andSelf().removeClass('ratings_over');
            // can't use 'this' because it wont contain the updated data
            set_votes($(this).closest('.rate_widget'));
        }
    );
    
    
    // This actually records the vote
    $('.ratings_stars').bind('click', function() {
        var star = this;
        var widget = $(this).closest('.rate_widget');
        var score = $(star).attr('class').match(/star_(\d+)/)[1];
 
        var clicked_data = {
            f_rating_score : score,
            f_article_number : widget.attr('id')
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

function set_votes(widget) {
    if ($(widget).data('fsr')) {
        var avg = $(widget).data('fsr').whole_avg;
        var votes = $(widget).data('fsr').number_votes;
        var exact = $(widget).data('fsr').dec_avg;
        var error = $(widget).data('fsr').error;

        $(widget).find('.star_' + avg).prevAll().andSelf().addClass('ratings_vote');
        $(widget).find('.star_' + avg).nextAll().removeClass('ratings_vote'); 
        $(widget).find('.total_votes').text(votes + ' {{ #voteS# }} {{ #averageRating# }} ' + exact);
        $(widget).find('.rating_error').text(error);
    }
}
</script>