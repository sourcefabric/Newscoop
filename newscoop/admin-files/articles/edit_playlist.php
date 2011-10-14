<?php if (empty($userIsBlogger)) : ?>
<div class="articlebox" title="<?php putGS('Article Playlist'); ?>">
	<div>
		<div id="playlist" style="margin-left:8px">
    		<div id="playlistArticles" style="display:block; padding-bottom:8px;">

    		</div>
    		<label><?php putGS('Add article to a playlist'); ?></label>
    		<a class="iframe ui-state-default icon-button right-floated"
    			popup-width="600"
        		href="<?php echo camp_html_article_url($articleObj, $f_language_id, "playlist/popup.php"); ?>">
       		<span class="ui-icon ui-icon-pencil"></span><?php putGS('Edit'); ?></a>
		</div>
	</div>
</div>
<?php endif; ?>
