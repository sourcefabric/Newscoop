<?php
/**
 * @package Newscoop
 * @subpackage Soundcloud plugin
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

camp_load_translation_strings("plugin_soundcloud");
global $articleObj, $f_article_number, $f_edit_mode;
?>
<script type="text/javascript">
$(document).ready(function(){

    $('#soundcloud-iframe').fancybox({
        hideOnContentClick: false,
        width: 722,
        height: 800,
        onStart: function() { // check if there are any changes
            return checkChanged();
        },
        onClosed: function(url, params) {
            if ($.fancybox.reload) { // reload if set
                window.location.reload();
            } else if ($.fancybox.error) {
                flashMessage($.fancybox.error, 'error');
            }
        }
    });

    $('a.soundcloud-unlink').click(function(){
        flashMessage(localizer.processing, '', true);
        $('#soundcloud-' + this.id).fadeOut('fast');
        $.get('/<?= $ADMIN ?>/soundcloud/controller.php', {action:'unlink',
                article:<?= $f_article_number ?>,
                track:this.id},
            function(response){
                $('.flash').fadeOut('fast');
            });
        return false;
    });
});
</script>

<div class="articlebox" title="Soundcloud">

<? if (($f_edit_mode == "edit") && $g_user->hasPermission('plugin_soundcloud_browser')): ?>
    <a id="soundcloud-iframe" custom="yes" class="iframe ui-state-default icon-button right-floated" href="<?= "/$ADMIN/soundcloud/attachement.php?article_id=$f_article_number" ?>"><span class="ui-icon ui-icon-plusthick"></span><? putGS('Attach') ?></a>
    <div class="clear"></div>
<? endif ?>


<ul class="block-list">
<? foreach (Soundcloud::getAssignments($f_article_number) as $trackData): ?>
<li id="soundcloud-<?= $trackData['id'] ?>">
    <div><a class="text-link" target="soundcloud" href="<?= $trackData['permalink_url'] ?><?= $trackData['sharing']=='public'?'':'/'.$trackData['secret_token'] ?>"><?= $trackData['title']; ?></a></div>
    <object height="81" width="100%"><param name="movie" value="http://player.soundcloud.com/player.swf?url=<?= urlencode($trackData['secret_uri']) ?>&amp;show_comments=false&amp;auto_play=false&amp;color=08597d"></param>
    <param name="allowscriptaccess" value="always"></param>
    <embed allowscriptaccess="always" height="81" src="http://player.soundcloud.com/player.swf?url=<?= urlencode($trackData['secret_uri']) ?>&amp;show_comments=false&amp;auto_play=false&amp;color=08597d" type="application/x-shockwave-flash" width="100%"></embed>
    </object>
<a id="<?= $trackData['id'] ?>" class="soundcloud-unlink corner-button" href=""><span class="ui-icon ui-icon-closethick"></span></a>
</li>
<? endforeach ?>
</ul>

</div>
