<?php if(!defined("PHORUM")) return; ?>

<script type="text/javascript">

/* ------------------------------------------------------------------------
 * Javascript functions for Smiley tools.
 * ------------------------------------------------------------------------ 
 */

var smileys_state = -1;
var smileys_count = 0;
var loaded_count = 0;
var loadingobj;

function toggle_smileys()
{
    // On the first request to open the smiley help, load all smiley images.
    if (smileys_state == -1) 
    {
        // Load smiley images.
        <?php
        $smileys_count = 0;
        $c = '';
        foreach ($PHORUM["mod_smileys"]["smileys"] as $id => $smiley) {
            if (! $smiley["active"] || $smiley["is_alias"] || $smiley["uses"] == 1) continue;
            $smileys_count ++;
            $src = htmlspecialchars($prefix . $smiley['smiley']);
            $c.="document.getElementById('smiley-button-{$id}').src='$src';\n";
        }
        print "smileys_count = $smileys_count;\n$c\n"; 
        ?>

        smileys_state = 0;
    }

    // Toggle smiley panel.
    smileys_state = ! smileys_state;
    if (smileys_state) show_smileys(); else hide_smileys();
}

function show_smileys()
{
    // We wait with displaying the smiley help until all smileys are loaded.
    if (loaded_count < smileys_count) return false;

    document.getElementById('phorum_mod_editor_tools_smileys').style.display = 'block';
    document.getElementById('phorum_mod_editor_tools_smileys_dots').style.display = 'none';
    return false;
}

function hide_smileys()
{
    document.getElementById('phorum_mod_editor_tools_smileys').style.display = 'none';
    document.getElementById('phorum_mod_editor_tools_smileys_dots').style.display = 'inline';
    return false;
}

function phorum_mod_smileys_insert_smiley(string) 
{
    var area = document.getElementById("phorum_textarea");
    string = unescape(string);
    
    if (area) 
    {
        if (area.createTextRange) /* MSIE */
        {
            area.focus(area.caretPos);
            area.caretPos = document.selection.createRange().duplicate();
            curtxt = area.caretPos.text;
            area.caretPos.text = string + curtxt;
        } 
        else /* Other browsers */
        {
            var pos = area.selectionStart;              
            area.value = 
                area.value.substring(0,pos) + 
                string +
                area.value.substring(pos);
            area.focus();
            area.selectionStart = pos + string.length;
            area.selectionEnd = area.selectionStart;
        }
    } else {
        alert('There seems to be a technical problem. The textarea ' +
              'cannot be found in the page. ' +
              'The textarea should have id="phorum_textarea" in the ' +
              'definition for this feature to be able to find it. ' +
              'If you are not the owner of this forum, then please ' +
              'alert the forum owner about this.');
    }
}

function phorum_mod_smileys_load_smiley (imgobj)
{
    loadingobj = document.getElementById('phorum_mod_editor_tools_smileys_loading');

    // Another smiley image was loaded. If we have loaded all
    // smiley images, then show the smileys panel.
    if (imgobj.src != '') {
        loaded_count ++;
        imgobj.onload = '';
        if (loaded_count == smileys_count) {
            loadingobj.style.display = 'none';
            show_smileys();
        } else {
            // Visual feedback for the user while loading the images.
            loadingobj.style.display = 'inline';
            loadingobj.innerHTML = "("
              + "<?php print $PHORUM["DATA"]["LANG"]["LoadingSmileys"]; ?> "
              + Math.floor(loaded_count/smileys_count*100) + "%)";
        }
    }
}

</script>
