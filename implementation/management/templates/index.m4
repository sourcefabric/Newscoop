INCLUDE_PHP_LIB(<*..*>)
<? todef('Path'); ?><HTML>
<HEAD>
<META HTTP-EQUIV="Refresh" CONTENT="0; URL=<? if ($Path == "") { ?>LOOK_PATH/<? } else { ?><? p($Path); ?><? } ?>">
</HEAD>
</HTML>
