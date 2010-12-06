<?php
camp_load_translation_strings('media_archive');
camp_load_translation_strings('api');

require_once LIBS_DIR . '/ImageList/ImageList.php';
require_once LIBS_DIR . '/MediaList/MediaList.php';

$crumbs = array();
$crumbs[] = array(getGS('Content'), "");
$crumbs[] = array(getGS('Media Archive'), "");
$breadcrumbs = camp_html_breadcrumbs($crumbs);
echo $breadcrumbs;
?>

<div id="archive">
<ul>
    <li><a href="#images"><?php putGS('Image archive'); ?></a></li>
    <li><a href="#attachments"><?php putGS('Attachments archive'); ?></a></li>
</ul>

<div id="images">

<?php if ($g_user->hasPermission('AddImage')) { ?>
<p class="actions">
    <a href="add.php"><img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/add.png" alt="<?php  putGS('Add new image'); ?>"> <?php putGS('Add new image'); ?></a>
</p>
<?php } ?>

<?php camp_html_display_msgs(); ?>

<?php
    $list = new ImageList;
    $list->setSearch(TRUE);
    $list->render();
?>

</div><!-- /#images -->

<div id="attachments">
    <?php
        $list = new MediaList;
        $list->setColVis(TRUE);
        $list->setSearch(TRUE);
        $list->render();
    ?>
</div><!-- /#attachments -->

</div><!-- /#archive -->
<script type="text/javascript">
<!--
    $('#archive').tabs()
        .css('border', 'none');
//-->
</script>

<?php camp_html_copyright_notice(); ?>
</body>
</html>
