<?php       
foreach (CampPlugin::getPluginInfos() as $info) {
    if (CampPlugin::isPluginEnabled($info['name'])) {
        foreach ($info['permissions'] as $permission => $label) {
            $rights[$info['label']][$permission] = getGS($label);
        }
    }   
}
?>