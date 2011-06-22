<?php
camp_load_translation_strings("plugins");
camp_load_translation_strings("api");

require_once($GLOBALS['g_campsiteDir']."/classes/Input.php");

if (!$g_user->hasPermission('plugin_manager')) {
    camp_html_display_error(getGS("You do not have the right to manage plugins."));
    exit;
}

if (Input::Get('save')) {
	if (!SecurityToken::isValid()) {
		camp_html_display_error(getGS('Invalid security token!'));
		exit;
	}

	$p_plugins = Input::Get('p_plugins', 'array');
    $p_enabled = Input::Get('p_enabled', 'array');


    // delete from DB those which were uninstalled
    foreach (CampPlugin::getAll() as $CampPlugin) {
        if (!$p_plugins[$CampPlugin->getName()]) {
            $CampPlugin->delete();
        }
    }


    foreach ($p_plugins as $plugin => $oldversion) {
        $CampPlugin = new CampPlugin($plugin);   // installed version, if exists
        $currentVersion = $CampPlugin->getFsVersion();

        if ($p_enabled[$plugin]) {
            if ($CampPlugin->exists()) {
                if ($CampPlugin->getDbVersion() != $currentVersion) {
                    // update plugin
                    $CampPlugin->delete();
                    $CampPlugin->create($plugin, $currentVersion);
                    $CampPlugin->update();
                    $CampPlugin->enable();

                } else {
                    // just enable plugin
                    $CampPlugin->enable();
                }
            } else {
                // install + enable not previously installed plugin
                $CampPlugin->create($plugin, $currentVersion);
                $CampPlugin->install();
                $CampPlugin->enable();
            }
        } else {
            $CampPlugin->disable();
        }
    }

    $redirector = $this->getHelper('redirector');
    $redirector->gotoSimple('manage.php', 'plugins', 'admin');
}

if (Input::Get('upload_package')) {
    $file = $_FILES['package'];
    if ($Plugin = CampPlugin::extractPackage($file['tmp_name'], $log)) {
        $success = getGS('The plugin $1 was sucessfully installed.', $Plugin->getName());
    } else {
        $error = $log;
    }
    //$Plugin->enable();
}

if (Input::Get('p_uninstall')) {
    $Plugin = new CampPlugin(Input::Get('p_plugin', 'string'));
    $Plugin->uninstall();
}

$plugins = CampPlugin::GetEnabled(true);
foreach ($plugins as $plugin) {
    camp_load_translation_strings("plugin_".$plugin->getName());
}

if( count($infos = CampPlugin::GetPluginsInfo()) > 0 ) {
	// check if update was needed
	CampPlugin::GetPluginsInfo(false, true);
	if ($needsUpdate = CampPlugin::GetNeedsUpdate()) {
	    camp_html_add_msg(getGS("Some plugins have to be updated. Please press the save button."));
	}
} else {
	camp_html_add_msg(getGS("You have no installed plugins."));
}


$crumbs = array();
$crumbs[] = array(getGS("Plugins"), "");
$crumbs[] = array(getGS("Manage"), "");
echo camp_html_breadcrumbs($crumbs);

camp_html_display_msgs();
?>
<P>
<FORM name="plugin_upload" action="/<?php echo $ADMIN; ?>/plugins/manage.php" method='POST' enctype='multipart/form-data'>
<?php echo SecurityToken::FormParameter(); ?>
<table cellpadding="0" cellspacing="0" class="action_buttons" style="padding-bottom: 5px;">
  <tr>
    <td>
      <IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/add.png" BORDER="0" alt="<?php  putGS('Add new image'); ?>">
      <?php putGS('Upload Plugin'); ?>
      <input type="file" name="package" class="button">
    </td>
    <td valign="bottom">&nbsp;<input type="submit" name="upload_package" value="<?php putGS('Upload') ?>" class="button"></td>
  </tr>
</table>
</FORM>

<?php
if ( isset( $success ) ) {
    ?>
    <table cellpadding="0" cellspacing="0" class="action_buttons" style="padding-bottom: 5px;">
      <tr>
        <td class="info_message" ><?php echo $success ?></td>
      </tr>
   </table>
   <?php
} elseif( isset( $error ) ) {
    ?>
    <table cellpadding="0" cellspacing="0" class="action_buttons" style="padding-bottom: 5px;">
      <tr>
        <td class="error_message" ><?php echo $error ?></td>
      </tr>
   </table>
   <?php
}
?>

<P>
<?php if (count($infos) > 0) { ?>
<FORM name="plugins_enabled" action="/<?php echo $ADMIN; ?>/plugins/manage.php">
<?php echo SecurityToken::FormParameter(); ?>
<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="3" class="table_list" width="95%">
    <TR class="table_list_header">
        <TD ALIGN="LEFT" VALIGN="TOP"><B><?php  putGS("Name"); ?></B></TD>
        <TD ALIGN="LEFT" VALIGN="TOP"><B><?php  putGS("Version"); ?></B></TD>
        <TD ALIGN="LEFT" VALIGN="TOP"><B><?php  putGS("Description"); ?></B></TD>
        <TD align="center" VALIGN="TOP"><B><?php  putGS("Enabled"); ?></B></TD>
        <TD align="center" VALIGN="TOP"><B><?php  putGS("Uninstall"); ?></B></TD>
    </TR>
    <?php
    $color=0;
    foreach ($infos as $info) {
        $checked = '';
        if (CampPlugin::isPluginEnabled($info['name'])) {
            $checked = 'checked="checked"';
        }
        ?>
        <TR <?php  if ($color) { $color=0; ?>class="list_row_even"<?php  } else { $color=1; ?>class="list_row_odd"<?php  } ?>>
            <TD width="100px">
                <?php  p($info['label']); ?>
            </TD>

            <td width="100px">
                <?php p($info['version']) ?>
            </td>

            <TD width="*">
                <?php  p($info['description']); ?>&nbsp;
            </TD>

            <TD  width="80px" align="center">
                <input type="hidden" name="p_plugins[<?php p(htmlspecialchars($info['name']))?>]" value="<?php p(htmlspecialchars($info['version'])) ?>">

                <input type="checkbox" name="p_enabled[<?php p(htmlspecialchars($info['name']))?>]" <?php p($checked) ?>>
            </TD>

            <TD  width="80px" align="center">
               <a href="/<?php echo $ADMIN; ?>/plugins/manage.php?p_plugin=<?php p(htmlspecialchars($info['name']))?>&amp;p_uninstall=1&amp;<?php echo SecurityToken::URLParameter(); ?>" onClick="return confirm('<?php putGS('Please confirm the plugin $1 uninstall. All plugin data will be deleted!', $info['name']) ?>')">
                 <IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"] ?>/delete.png" BORDER="0" ALT="<?php putGS('Delete plugin')?>" TITLE="<?php putGS('Delete plugin') ?>">
               </a>
            </TD>
        </TR>
    <?php
    }
    ?>
    <tr class="table_list_header">
        <td colspan="5" align="center">
            <input type="submit" name="save" value="<?php putGS('Save') ?>" class="button">
        </td>
    </tr>
</table>
</form>
<?php } else { ?>
    <BLOCKQUOTE><ul>
    <LI><?php  putGS('No plugins found.'); ?></LI>
    </ul></BLOCKQUOTE>
<?php } ?>
<?php camp_html_copyright_notice(); ?>
