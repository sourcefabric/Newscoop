<?php

////////////////////////////////////////////////////////////////////////////////
//                                                                            //
//   Copyright (C) 2006  Phorum Development Team                              //
//   http://www.phorum.org                                                    //
//                                                                            //
//   This program is free software. You can redistribute it and/or modify     //
//   it under the terms of either the current Phorum License (viewable at     //
//   phorum.org) or the Phorum License that was distributed with this file    //
//                                                                            //
//   This program is distributed in the hope that it will be useful,          //
//   but WITHOUT ANY WARRANTY, without even the implied warranty of           //
//   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                     //
//                                                                            //
//   You should have received a copy of the Phorum License                    //
//   along with this program.                                                 //
////////////////////////////////////////////////////////////////////////////////

if(!defined("PHORUM")) return;

// For keeping track of include dependancies, which
// are used to let templates automatically rebuild
// in case an included subtemplate has been changed.
$include_level = 0;
$include_deps  = array();

function phorum_import_template($tplfile, $outfile)
{
    global $include_level, $include_deps;
    $include_level++;

    // Remember that we used this template.
    $include_deps[$tplfile] = $outfile;

    // In case we're handling 0 byte large files, we set $page
    // directly. Running fread($fp, 0) gives a PHP warning.
    if (filesize($tplfile)) {
        $fp=fopen($tplfile, "r");
        $page=fread($fp, filesize($tplfile));
        fclose($fp);
    } else {
        $page = '';
    }

    preg_match_all("/\{[\!\/A-Za-z].+?\}/s", $page, $matches);

    settype($oldloopvar, "string");
    settype($loopvar, "string");
    settype($olddatavar, "string");
    settype($datavar, "string");
    $loopvars = array();

    foreach($matches[0] as $match){
        unset($parts);

        $string=substr($match, 1, -1);

        $string = trim($string);

        // pre-parse pointer variables
        if(strstr($string, "->")){
            $string=str_replace("->", "']['", $string);
        }

        $parts=explode(" ", $string);

        switch(strtolower($parts[0])){

            // Comment
            case "!":

            $repl="<?php // ".implode(" ", $parts)." ?>";
            break;


            case "include":

            $repl = file_get_contents(phorum_get_template($parts[1],1));
            break;

            case "include_once":

            $repl="<?php include_once phorum_get_template('$parts[1]'); ?>";
            break;

            case "include_var": // include a file given by a variable

            $repl="<?php include_once phorum_get_template( \$PHORUM[\"DATA\"]['$parts[1]']); ?>";
            break;

            // A define is used to create vars for the engine to use.
            case "define":

            $repl="<?php \$PHORUM[\"TMP\"]['$parts[1]']='";
            array_shift($parts);
            array_shift($parts);
            foreach($parts as $part){
                $repl.=str_replace("'", "\\'", $part)." ";
            }
            $repl=trim($repl)."'; ?>";
            break;


            // A var is used to create vars for the template.
            case "var":

            $repl="<?php \$PHORUM[\"DATA\"]['$parts[1]']='";
            array_shift($parts);
            array_shift($parts);
            foreach($parts as $part){
                $repl.=str_replace("'", "\\'", $part)." ";
            }
            $repl=trim($repl)."'; ?>";
            break;

            // Run a Phorum hook. The first parameter is the name of the
            // hook. Other parameters will be passed on as arguments for
            // the hook function. On argument will be passed directly to
            // the hook. Multiple arguments will be passed in an array.
            case "hook":

            // Setup hook arguments.
            $hookargs = array();
            for($i = 2; !empty($parts[$i]); $i++) {
                // For supporting the following construct, where the
                // loopvar is passed to the hook in full:
                // {LOOP SOMELIST}
                //   {HOOK some_hook SOMELIST}
                // {/LOOP SOMELIST}
                if (isset($loopvars[$parts[$i]])) {
                    $hookargs[] = "\$PHORUM['TMP']['".addslashes($parts[$i])."']";
                } else {
                    $index = phorum_determine_index($loopvars, $parts[$i]);
                    $hookargs[] = "\$PHORUM['$index']['".addslashes($parts[$i])."']";
                }
            }

            // Build the replacement string.
            $repl = "<?php if(isset(\$PHORUM['hooks']['".addslashes($parts[1])."'])) phorum_hook('".addslashes($parts[1])."'";
            if (count($hookargs) == 1) {
                $repl .= "," . $hookargs[0];
            } elseif (count($hookargs) > 1) {
                $repl .= ",array(" . implode(",", $hookargs) . ")";
            }
            $repl .= ");?>";
            break;

            // starts a loop
            case "loop":

            $loopvars[$parts[1]]=true;
            $index=phorum_determine_index($loopvars, $parts[1]);
            $repl="<?php \$phorum_loopstack[] = isset(\$PHORUM['TMP']['$parts[1]']) ? \$PHORUM['TMP']['$parts[1]']:NULL; if(isset(\$PHORUM['$index']['$parts[1]']) && is_array(\$PHORUM['$index']['$parts[1]'])) foreach(\$PHORUM['$index']['$parts[1]'] as \$PHORUM['TMP']['$parts[1]']){ ?>";
            break;


            // ends a loop
            case "/loop":

            if (!isset($parts[1])) print "<h3>Template warning: Missing argument for /loop statement in file '" . htmlspecialchars($tplfile) . "'</h3>";
            $repl="<?php } if(isset(\$PHORUM['TMP']) && isset(\$PHORUM['TMP']['$parts[1]'])) unset(\$PHORUM['TMP']['$parts[1]']); \$phorum_loopstackitem=array_pop(\$phorum_loopstack); if (isset(\$phorum_loopstackitem)) \$PHORUM['TMP']['$parts[1]'] = \$phorum_loopstackitem;?>";
            unset($loopvars[$parts[1]]);
            break;


            // if and elseif are the same accept how the line starts
            case "if":
            case "elseif":

            // determine if or elseif
            $prefix = (strtolower($parts[0])=="if") ? "if" : "} elseif";

            // are we wanting == or !=
            if(strtolower($parts[1])=="not"){
                $operator="!=";
                $parts[1]=$parts[2];
                if(isset($parts[3])){
                    $parts[2]=$parts[3];
                    unset($parts[3]);
                } else {
                    unset($parts[2]);
                }
            } else {
                $operator="==";
            }

            $index=phorum_determine_index($loopvars, $parts[1]);

            // if there is no part 2, check that the value is set and not empty
            if(!isset($parts[2])){
                if($operator=="=="){
                    $repl="<?php $prefix(isset(\$PHORUM['$index']['$parts[1]']) && !empty(\$PHORUM['$index']['$parts[1]'])){ ?>";
                } else {
                    $repl="<?php $prefix(!isset(\$PHORUM['$index']['$parts[1]']) || empty(\$PHORUM['$index']['$parts[1]'])){ ?>";
                }

                // if it is numeric, a constant or a string, simply set it as is
            } elseif(is_numeric($parts[2]) || defined($parts[2]) || preg_match('!"[^"]*"!', $parts[2])) {
                $repl="<?php $prefix(isset(\$PHORUM['$index']['$parts[1]']) && \$PHORUM['$index']['$parts[1]']$operator$parts[2]){ ?>";

                // we must have a template var
            } else {

                $index_part2=phorum_determine_index($loopvars, $parts[2]);

                // this is a really complicated IF we are building.

                $repl="<?php $prefix(isset(\$PHORUM['$index']['$parts[1]']) && isset(\$PHORUM['$index_part2']['$parts[2]']) && \$PHORUM['$index']['$parts[1]']$operator\$PHORUM['$index_part2']['$parts[2]']) { ?>";

            }

            // reset $prefix
            $prefix="";
            break;


            // create an else
            case "else":

            $repl="<?php } else { ?>";
            break;


            // close an if
            case "/if":

            $repl="<?php } ?>";
            break;

            case "assign":
            if(defined($parts[2]) || is_numeric($parts[2])){
                $repl="<?php \$PHORUM[\"DATA\"]['$parts[1]']=$parts[2]; ?>";
            } else {
                $index=phorum_determine_index($loopvars, $parts[2]);

                $repl="<?php \$PHORUM[\"DATA\"]['$parts[1]']=\$PHORUM['$index']['$parts[2]']; ?>";
            }
            break;


            // this is just for echoing vars from DATA or TMP if it is a loopvar
            default:

            if(defined($parts[0])){
                $repl="<?php echo $parts[0]; ?>";
            } else {

                $index=phorum_determine_index($loopvars, $parts[0]);

                $repl="<?php echo \$PHORUM['$index']['$parts[0]']; ?>";
            }
        }

        $page=str_replace($match, $repl, $page);
    }

    $include_level--;

    // Did we finish processing our top level template? Then write out
    // the compiled template to the cache.
    //
    // For storing the compiled template, we use two files. The first one
    // has some code for checking if one of the dependant files has been
    // updated and for rebuilding the template if this is the case.
    // This one loads the second file, which is the template itself.
    //
    // This two-stage loading is needed to make sure that syntax
    // errors in a template file won't break the depancy checking process.
    // If both were in the same file, the complete file would not be run
    // at all and the user would have to clean out the template cache to
    // reload the template once it was fixed. This way user intervention
    // is never needed.
    if ($include_level == 0)
    {
        // Find the template name for the top level template.
        $pathparts = preg_split('[\\/]', $outfile);
        $fileparts = explode('-', preg_replace('/^.*\//', '', $pathparts[count($pathparts)-1]));
        $this_template = addslashes($fileparts[2]);

        // Determine first and second stage cache filenames.
        $stage1_file = $outfile;
        $fileparts[3] = "toplevel_stage2";
        unset($pathparts[count($pathparts)-1]);
        $stage2_file = implode('/', $pathparts) . '/' . implode('-', $fileparts);

        // Create code for automatic rebuilding of rendered templates
        // in case of changes. This is done by checking if one of the
        // templates in the dependancy list has been updated. If this
        // is the case, all dependant rendered subtemplates are deleted.
        // After that phorum_get_template() is called on the top level
        // template to rebuild all needed templates.

        $check_deps =
            "<?php\n" .
            '$mymtime = @filemtime("' . addslashes($stage1_file) . '");' . "\n" .
            "\$update_count = 0;\n" .
            "\$need_update = (\n";
        foreach ($include_deps as $tpl => $out) {
            $qtpl = addslashes($tpl);
            $check_deps .= "    @filemtime(\"$qtpl\") > \$mymtime ||\n";
        }
        $check_deps = substr($check_deps, 0, -4); // strip trailing " ||\n"
        $check_deps .=
        "\n" .
        ");\n" .
        "if (\$need_update) {\n";
        foreach ($include_deps as $tpl => $out) {
            $qout = addslashes($out);
            $check_deps .= "    @unlink(\"$qout\");\n";
        }
        $check_deps .=
        "    \$tplfile = phorum_get_template(\"$this_template\");\n" .
        "}\n" .
        "include(\"" . addslashes($stage2_file) . "\");\n" .
        "?>\n";

        // Reset dependancy list for the next phorum_import_template() call.
        $include_deps = array();

        // Write out data to the cache.
        phorum_write_templatefile($stage1_file, $check_deps);
        phorum_write_templatefile($stage2_file, $page, true);
    }
    else
    {
        // Write out subtemplate to the cache.
        phorum_write_templatefile($outfile, $page);
    }


}

function phorum_write_templatefile($filename, $content, $is_toplevel = false)
{
    if($fp=fopen($filename, "w")) {
        fputs($fp, "<?php if(!defined(\"PHORUM\")) return; ?>\n");
        if ($is_toplevel) {
            fputs($fp, "<?php \$phorum_loopstack = array() ?>\n");
        }
        fputs($fp, $content);
        if (! fclose($fp)) {
            die("Error on closing $filename. Is your disk full?");
        }
        // Some very unusual thing might happen. On Windows2000 we have seen
        // that the webserver can write a message to the cache directory,
        // but that it cannot read it afterwards. Probably due to 
        // specific NTFS file permission settings. So here we have to make
        // sure that we can open the file that we just wrote.
        $checkfp = fopen($filename, "r");
        if (! $checkfp) {
            die("Failed to write a usable compiled template to $filename. " .
                "The file was was created successfully, but it could not " .
                "be read by the webserver afterwards. This is probably " .
                "caused by the file permissions on your cache directory.");
        }
        fclose($checkfp);
    } else {
        die("Failed to write a compiled template to $filename. This is " .
            "probably caused by the file permissions on your cache " .
            "directory.");
    }
}

function phorum_determine_index($loopvars, $varname)
{
    if(isset($loopvars) && count($loopvars)){
        while(strstr($varname, "]")){
            $varname=substr($varname, 0, strrpos($varname, "]")-1);
            if(isset($loopvars[$varname])){
                return "TMP";
                break;
            }
        }
    }

    return "DATA";
}

?>
