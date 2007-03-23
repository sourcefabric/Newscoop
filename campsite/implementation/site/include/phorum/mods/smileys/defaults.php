<?php
// A simple helper script that will setup initial module
// settings in case one of these settings is missing.

if(!defined("PHORUM") && !defined("PHORUM_ADMIN")) return;

if (! isset($PHORUM['mod_smileys'])           ||
    ! isset($PHORUM['mod_smileys']['prefix']) ||
    ! isset($PHORUM['mod_smileys']['smileys'])) {
    require_once("./mods/smileys/smileyslib.php");
    $PHORUM['mod_smileys'] = phorum_mod_smileys_initsettings();
}

?>
