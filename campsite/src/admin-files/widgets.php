<?php

require_once($GLOBALS['g_campsiteDir']."/db_connect.php");
require_once WWW_DIR . '/classes/Extension/WidgetManager.php';

camp_load_translation_strings("home");
camp_load_translation_strings("api");

$crumbs = array();
$crumbs[] = array(getGS("Widgets"), "");
$breadcrumbs = camp_html_breadcrumbs($crumbs);
echo $breadcrumbs;
?>

<h2><?php putGS('Widgets'); ?></h2>
<p><a href="<?php echo $Campsite['WEBSITE_URL']; ?>/admin/" title="<?php putGS('Go to home'); ?>"><?php putGS('Go to home'); ?></a></p>

<ul id="widgets">
    <?php foreach (WidgetManager::GetAvailable($g_user->getUserId()) as $widget) { ?>
    <li>
        <h3><?php echo $widget->getTitle(); ?></h3>
        <p><a href="#<?php echo $widget->getId(TRUE); ?>" class="add"><?php putGS('Add to dashboard'); ?></a></p>
        <p><?php echo $widget->getDescription(); ?></p>
        <dl class="meta">
            <dt><?php putGS('Author'); ?>:</dt>
            <dd><?php echo $widget->getAuthor(); ?></dd>
            <dt><?php putGS('Version'); ?>:</dt>
            <dd><?php echo $widget->getVersion(); ?></dd>
            <dt><?php putGS('Homepage'); ?>:</dt>
            <dd><a href="<?php echo $widget->getHomepage(); ?>"><?php echo $widget->getHomepage(); ?></a></dd>
        </dl>
    </li>
    <?php } ?>
</ul>

<script type="text/javascript">
$(document).ready(function() {
    var dashboard_id = 1;
    $('a.add').click(function() {
        var a = $(this);
        var id = a.attr('href').slice(1);
        callServer(['WidgetManager', 'AddWidget'], [
            id,
            'dashboard' + dashboard_id,
            ], function(json) {
                flashMessage('<?php putGS('Widget added to dashboard.'); ?>');
                a.hide();
                dashboard_id = dashboard_id + 1;
                if (dashboard_id > 3) {
                    dashboard_id = 1;
                }
                a.closest('li').addClass('ui-state-highlight');
            });
    });
});
</script>

<?php camp_html_copyright_notice(); ?>
</body>
</html>
