<script type="text/javascript">
// Print last modified date
var dateTime = '<?php if ($savedToday) { p(date("H:i:s", $lastModified)); } else { p(date("Y-m-d H:i", $lastModified)); } ?>';
var fullDate = '<?php p(date("Y-m-d H:i:s", $lastModified)); ?>';
document.getElementById('info-text').innerHTML = '<?php putGS('Saved'); ?> ' + ' ' + dateTime;
document.getElementById('date-last-modified').innerHTML = '<?php putGS('Last modified'); ?> ' + ': ' + fullDate;

$(function() {

// datepicker for date
$('.datepicker').datepicker({
    dateFormat: 'yy-mm-dd'
});

// accordion hovers
$('.ui-accordion-header').hover(
    function(){ $(this).removeClass('ui-state-default').addClass('ui-state-hover'); },
    function(){ $(this).removeClass('ui-state-hover').addClass('ui-state-default'); }
);

// hover states on the static widgets
$('.icon-button').hover(
    function() { $(this).addClass('ui-state-hover'); },
    function() { $(this).removeClass('ui-state-hover'); }
);

$('.collapsible').each(function(index) {
    var head = $('> .head', $(this));
    var cookie = 'articlebox-' + index;
    var opened = $.cookie(cookie);
    var expires = { expires: 14 } // 14 days cookie expiration

    // init by cookie
    if (opened != 1) {
        $(this).next().hide();
    } else {
        head.addClass('ui-state-active');
    }

    // toggle
    $(this).click(function() {
        $(this).next().toggle('fast');
        head.toggleClass('ui-state-active');
        if (head.hasClass('ui-state-active')) {
            $.cookie(cookie, 1, expires);
        } else {
            $.cookie(cookie, 0, expires);
        }
        return false;
    });
});

// copy title to hidden field
$('input:text[name=f_article_title]').change(function() {
    $('input:hidden[name=f_article_title]').val($(this).val())
        .closest('form').change();
}).change();
 
// main form submit
$('form#article-main').submit(function() {
    var form = $(this);

    if (!form.hasClass('changed')) {
        return false;
    }

    // ping for connection
    callServer('ping', [], function(json) {
        $.ajax({
            type: 'POST',
            url: '<?php echo $Campsite['WEBSITE_URL']; ?>/admin/articles/post.php',
            data: form.serialize(),
            success: function(data, status, p) {
                if (p.responseText !== undefined) {
                    $("#connection-message").show().html('<?php putGS("Article Saved"); ?>');
                }
            },
            error: function (rq, status, error) {
                if (status == 0 || status == -1) {
                    $("#connection-error").show().html('<?php putGS("Unable to reach Campsite. Please check your internet connection."); ?>');
                }
            }
        });

    }); // /ping

    $(this).removeClass('changed');
    return false;
}).change(function() {
    $(this).addClass('changed');
});
    
// save all buttons
$('.save-button-bar input').click(function() {
    $('form#article-keywords').submit();
    $('form#article-switches').submit();
    $('form#article-main').submit();
    
    if ($(this).attr('id') == 'save_and_close') {
        window.location.href = '<?php echo "/$ADMIN/articles/index.php?f_publication_id=$f_publication_id&f_issue_number=$f_issue_number&f_language_id=$f_language_id&f_section_number=$f_section_number"; ?>';
    }
    
    return false;
});

var authorsList = [
<?php
$allAuthors = Author::GetAllExistingNames();
$quoteStringFn = create_function('&$value, $key',
    '$value = "\"" . camp_javascriptspecialchars($value) . "\"";');
array_walk($allAuthors, $quoteStringFn);
echo implode(",\n", $allAuthors);
?>
];
$(".aauthor").autocomplete({
    source: authorsList
});

// fancybox for popups
$('a.iframe').each(function() {
    $(this).fancybox({
        hideOnContentClick: false,
        width: 1080,
        height: 610,
        onStart: function() { // check if there are any changes
            return checkChanged();
        },
        onClosed: function(url, params) {
            if ($.fancybox.reload) { // reload if set
                window.location.reload();
            }
        }
    });
});

// comments form check for changes
$('form#article-comments').submit(function() {
    if (!checkChanged()) {
        return false;
    }
});

}); // /document.ready

/**
 * Check for unsaved changes in main/boxes forms
 * @return bool
 */
function checkChanged()
{
    if ($('form.changed').size() == 0) {
        return true; // continue
    }

    return confirm('<?php putGS('Your work has not been saved. Do you want to continue and lose your changes?'); ?>');
}
</script>
