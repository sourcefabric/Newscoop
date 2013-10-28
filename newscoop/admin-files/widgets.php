<?php

require_once dirname(dirname(__FILE__)) . '/db_connect.php';
require_once dirname(dirname(__FILE__)) . '/classes/Extension/WidgetManager.php';
$translator = \Zend_Registry::get('container')->getService('translator');

echo camp_html_breadcrumbs(array(
    array($translator->trans('Dashboard', array(), 'home'), $Campsite['WEBSITE_URL'] . '/admin/home.php'),
    array($translator->trans('Widgets', array(), 'home'), ''),
));
?>

<div class="links">
    <a href="<?php echo $Campsite['WEBSITE_URL']; ?>/admin/" title="<?php echo $translator->trans('Go to dashboard', array(), 'home'); ?>"><?php echo $translator->trans('Go to dashboard', array(), 'home'); ?></a>
</div>

<ul id="widgets">
    <?php foreach (WidgetManager::GetAvailable() as $widget) { ?>
    <li>
        <h3><?php echo $widget->getTitle(); ?></h3>
        <p><a href="#<?php echo $widget->getExtension()->getId(); ?>" class="add"><?php echo $translator->trans('Add to dashboard', array(), 'home'); ?></a>&nbsp;</p>
        <p><?php echo $translator->trans($widget->getDescription()); ?></p>
        <?php $widget->renderMeta(); ?>
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
                flashMessage('<?php echo $translator->trans('Widget added to dashboard.', array(), 'home'); ?>');
                a.hide();
                dashboard_id = dashboard_id + 1;
                if (dashboard_id > 2) {
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
