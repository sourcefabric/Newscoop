INCLUDE_PHP_LIB(<*..*>)
<?php  todef('Path'); ?><HTML>
<HEAD>
<META HTTP-EQUIV="Refresh" CONTENT="0; URL=<?php  if ($Path == "") { ?>LOOK_PATH/<?php  } else { ?><?php  p($Path); ?><?php  } ?>">
</HEAD>
</HTML>
