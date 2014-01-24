<?php

$translator = \Zend_Registry::get('container')->getService('translator');
// check permissions
if (!$g_user->hasPermission('CommentEnable')) {
    return;
}
?>

<?php
// add token
echo SecurityToken::FormParameter();

// add hidden inputs
$hiddens = array(
    'f_language_id' => 'language_id',
    'f_article_number' => 'article_id',
    'f_language_selected' => 'language_selected_id',
);
foreach ($hiddens as $name) {
    if (!isset($$name)) {
        $$name = '';
    }

    echo '<input type="hidden" name="', $name;
    echo '" value="', $$name, '" />', "\n";
}
/** @todo Replace this basic template with a doT template from jquery*/
?>
<fieldset id="comment-prototype" class="plain comments-block" style="display:none">
    <input type="hidden" name="comment_id" value="${id}">
    <?php if ($inEditMode): ?>
    
    <?php endif; //inEditMode?>
    <div class="frame clearfix">
      <dl class="inline-list" id="comment-${id}">
        <div class="comment_status" style="display:none;">${status}</div>
        <dt><?php echo $translator->trans('From', array(), 'article_comments'); ?></dt>
        <dd><a href="mailto:${email}">"${name}" &lt;${email}&gt;</a> (${ip})</dd>
        <dt><?php echo $translator->trans('Date'); ?></dt>
        <dd>${time_created}</dd>
        <dt><?php echo $translator->trans('Subject'); ?></dt>
        <dd>
            <?php if ($inEditMode && $g_user->hasPermission('CommentEdit')): ?>
                <input type="text" value="${subject}"></input>
            <?php else: ?>
                ${subject}
            <?php endif; //inEditMode?>
        </dd>
        <dt><?php echo $translator->trans('Comment'); ?></dt>
        <dd>
            <?php if ($inEditMode && $g_user->hasPermission('CommentEdit')): ?>
                <textarea rows="5" cols="60">${message}</textarea>
            <?php else: ?>
                ${message}
            <?php endif; //inEditMode?>
        </dd>
        <?php if (!$inEditMode): ?>
        <dt><?php echo $translator->trans('Status'); ?></dt>
        <dd>
            <span class="comment-state-status comment-state-status-${status}" style="font-size:12px;">&nbsp;</span>
            <span class="comment-recommend-status comment-recommend-status-${recommended_toggle}" style="font-size:12px;">&nbsp;</span>
        </dd>
        <?php else: ?>
        <?php if ($g_user->hasPermission('CommentEdit')) { ?>
        <dt>&nbsp;</dt>
        <?php } ?>
        <?php if ($g_user->hasPermission('CommentModerate')) { ?>
        <dd>
            <ul class="action-list clearfix">
              <li>
              <input type="radio" name="comment_action_${id}" value="pending" class="input_radio" id="inbox_${id}" ${pending_checked}/>
                <label class="inline-style left-floated" for="inbox_${id}"><?php echo $translator->trans('New'); ?></label>
              </li>

              <li>
              <input type="radio" name="comment_action_${id}" value="approved" class="input_radio" id="approved_${id}" ${approved_checked}/>
                <label class="inline-style left-floated" for="approved_${id}"><?php echo $translator->trans('Approved'); ?></label>
              </li>

              <li>
                <input type="radio" name="comment_action_${id}" value="hidden" class="input_radio" id="hidden_${id}" ${hidden_checked}/>
                <label class="inline-style left-floated" for="hidden_${id}"><?php echo $translator->trans('Hidden'); ?></label>
              </li>

              <li>
                <input type="radio" name="comment_action_${id}" value="deleted" class="input_radio" id="deleted_${id}" ${deleted_checked}/>
                <label class="inline-style left-floated" for="deleted_${id}"><?php echo $translator->trans('Delete'); ?></label>
              </li>
            </ul>
        </dd>
        <?php } else { ?>
        <dt><?php echo $translator->trans('Status'); ?></dt>
        <dd>
            <span class="comment-state-status comment-state-status-${status}" style="font-size:12px;">&nbsp;</span>
            <span class="comment-recommend-status comment-recommend-status-${recommended_toggle}" style="font-size:12px;">&nbsp;</span>
        </dd>
        <?php } ?>
        <dd class="buttons">
            <?php if ($inEditMode): ?>
            <?php if (($g_user->hasPermission('CommentEdit')) || ($g_user->hasPermission('CommentModerate'))) { ?>
            <a class="ui-state-default icon-button comment-update"><span class="ui-icon ui-icon-disk"></span><?php echo $translator->trans('Save comment', array(), 'article_comments'); ?></a>
            <?php } ?>
            <?php endif; //inEditMode?>

            <?php if ($g_user->hasPermission('CommentModerate')) { ?>
            <a href="<?php echo $controller->view->url(array(
                'module' => 'admin',
                'controller' => 'comments',
                'action' => 'set-recommended',
            )); ?>/${id}/${recommended_toggle}" class="ui-state-default text-button comment-recommend status-${recommended_toggle}"><?php echo $translator->trans('Recommend', array(), 'article_comments'); ?></a>
            <?php } ?>

            <a href="<?php echo camp_html_article_url($articleObj, $f_language_selected, 'comments/reply.php', '', '&f_comment_id=${id}'); ?>" class="ui-state-default text-button"><?php echo $translator->trans('Reply to comment', array(), 'article_comments'); ?></a>

        </dd>
        <?php endif; //inEditMode?>
      </dl>
    </div>
</fieldset>
<p style="display:none"><?php echo $translator->trans('No comments posted.', array(), 'article_comments'); ?></p>
<form id="comment-moderate" action="../comments/set-status" method="POST"></form>

<script type="text/javascript">
function toggleCommentStatus(commentId) {
    var commentSetting = $('input:radio[name^="f_comment"]:checked').val();
    $('#comment-moderate .comments-block').each(function() {
        if (commentId && commentId == $(this).find('input:hidden').val()) {
            var statusClassMap = { 'hidden': 'hide', 'approved': 'approve', 'pending': 'inbox'};
            var block = $(this);
            var status = $('input:radio:checked', block).val();
            if (status === undefined) {
                status = $('.comment_status', block).html();
            }
            var cclass = 'comment_'+statusClassMap[status];

            // set class
            $('.frame', block).removeClass('comment_inbox comment_hide comment_approve')
                .addClass(cclass);
        }
    });

    //detach deleted
    $('input[value=deleted]:checked', $('#comment-moderate')).each(function() {
        $(this).closest('fieldset').slideUp(function() {
            $(this).detach();
        });
    });
}

function loadComments() {
    var lastCommentId = null;
    var commentsNumber = 10;
    if ($('#comment-moderate .comments-block').length > 0) {
        lastCommentId = $('#comment-moderate .comments-block').length;
    }

    var call_data = {
        "article": "<?php echo $articleObj->getArticleNumber(); ?>",
        "language": "<?php echo $f_language_selected; ?>",
        "iDisplayStart": lastCommentId,
        "iDisplayLength": commentsNumber
    };
    

    var call_url = '../comments/list';

    var res_handle = function(data) {
        //$('#comment-moderate').empty();
        $('fieldset.get-more-comments').remove();
        var hasComment = false;
        for(var i in data.result) {
            hasComment = true;
            var comment = data.result[i];
            if(typeof(comment) == "function") {
                continue;
            }

            var template = $('#comment-prototype').html();
            for(var key in comment) {
                if(key == 'status') {
                    template = template.replace(new RegExp("\\$({|%7B)"+comment[key]+"_checked(}|%7D)","g"),'checked="true"');
                    template = template.replace(new RegExp("\\${[^_]*_checked}","g"),'');
                }
                template = template.replace(new RegExp("\\$({|%7B)"+key+"(}|%7D)","g"),comment[key]);
            }
            $('#comment-moderate').append('<fieldset data-comment-id="'+comment['id']+'" class="plain comments-block">'+template+'</fieldset>');
            toggleCommentStatus(comment['id']);
        }

        var getMoreLink = $('<div style="text-align:center;"><fieldset class="get-more-comments"><input type="button" style="pull-right" class="ui-state-default default-button paginate paginate-next" value="<?php echo $translator->trans('Show more comments', array(), 'article_comments'); ?>" /></fieldset></div>');
        getMoreLink.find('input')
            .click(function(e){
                loadComments();
                e.preventDefault();
            });

        $('#comment-moderate').append(getMoreLink);

        if (data.result.length == 0) {
            $('fieldset.get-more-comments').html('<p><?php echo $translator->trans('There are no more comments', array(), 'article_comments'); ?></p>');
        }

        var referencedComment = $(document.location.hash);
        if (referencedComment.size() == 1) {
            $(window).scrollTop(referencedComment.position().top);
        }

        if(!hasComment)
            $('#no-comments').show();

        $('.comment-state-status').each(function() {
            $(this).html("&nbsp;");
            if ($(this).hasClass('comment-state-status-pending')) {
                $(this).html("<?php echo $translator->trans("New"); ?>");
            }
            if ($(this).hasClass('comment-state-status-approved')) {
                $(this).html("<?php echo $translator->trans("Approved"); ?>");
            }
            if ($(this).hasClass('comment-state-status-hidden')) {
                $(this).html("<?php echo $translator->trans("Hidden"); ?>");
            }
        });

        $('.comment-recommend-status').each(function() {
            if ($(this).hasClass('comment-recommend-status-0')) {
                $(this).html("<?php echo $translator->trans("Recommended", array(), 'article_comments'); ?>");
            }
            else {
                $(this).html("&nbsp;");
            }
        });
        $('.comment-recommend').each(function() {
             if ($(this).hasClass('status-0')) {
                 $(this).html("<?php echo $translator->trans("Unrecommend", array(), 'article_comments'); ?>");
            }
        }).click(function() {
            var link = $(this);
            callServer('ping', [], function(json) {
                $.getJSON(link.attr('href'), {
                }, function(data, textStatus, jqXHR) {
                    if (link.hasClass('status-0')) {
                        link.removeClass('status-0').addClass('status-1');
                        link.html("<?php echo $translator->trans("Recommend", array(), 'article_comments'); ?>");
                        var status = 1;
                    } else {
                        link.removeClass('status-1').addClass('status-0');
                        link.html("<?php echo $translator->trans("Unrecommend", array(), 'article_comments'); ?>");
                        var status = 1;
                    }

                    var href = link.attr('href');
                    link.attr('href', href.substr(0, href.length - 2) + '/'+ status);
                });
            });

            return false;
        });
    };

    callServer(call_url, call_data, res_handle, true);
};

var updateStatus = function(button) {
    var el = $(button).parents('dl').find('input:radio:checked').first();
    var wanted_status = el.val();

    var call_data = {
       "comment": el.attr('id').match(/\d+/)[0],
       "status": wanted_status
    };

    var call_url = '../comments/set-status';

    var res_handle = function(data) {
        //flashMessage('<?php echo $translator->trans('Comments updated.'); ?>');
        toggleCommentStatus(el.attr('id').match(/\d+/)[0]);
        if ('deleted' == wanted_status) {
            loadComments();
        }

    };

    callServer(call_url, call_data, res_handle, true);

    return wanted_status;
};

$('.comment-update').live('click',function(){
    var comment, subject, body;

    <?php if ($g_user->hasPermission('CommentModerate')) { ?>
    var wanted_status = updateStatus(this);
    if ('deleted' == wanted_status) {
        return;
    }
    <?php } ?>

    <?php if (!$g_user->hasPermission('CommentEdit')) { ?>
        return;
    <?php } ?>

    comment = $(this).parents('dl');
    subject = comment.find('input').val();
    body = comment.find('textarea').val();

    var call_data = {
       "id": comment.attr('id').match(/\d+/)[0],
       "subject": subject,
       "body": body
    };

    var call_url = '../comment/update-contents/format/json';

    var res_handle = function(data) {
        flashMessage('<?php echo $translator->trans('Comment updated.', array(), 'article_comments'); ?>');
    };

    callServer(call_url, call_data, res_handle, true);
});

loadComments();
</script>
