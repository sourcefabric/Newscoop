GeoPopups = {};

// preparing image html tag for a point popup
GeoPopups.set_image_tag = function(attrs, geo_obj)
{
    attrs.m_image = "";

    var img_src = attrs.m_image_source;
    if (!img_src) {img_src = "";}
    if (0 < img_src.length)
    {
        var img_value = "<img src='" + img_src + "'";
        var img_height = attrs.m_image_height;
        if (undefined !== img_height) {img_value += " height='" + img_height + "'";}
        var img_width = attrs.m_image_width;
        if (undefined !== img_width) {img_value += " width='" + img_width + "'";}
        img_value += " />";

        attrs.m_image = img_value;
    }
};

// preparing video html tag for a point popup
GeoPopups.set_embed_tag = function(attrs, geo_obj)
{
    attrs.m_embed = "";
    attrs.m_embed_width = 0;
    attrs.m_embed_height = 0;

    var vid_id = attrs.m_video_id;
    var vid_type = attrs.m_video_type;
    if (!vid_id) {vid_id = "";}
    if (!vid_type) {vid_type = "none";}

    var vid_define = null;
    if ("none" != vid_type)
    {
        vid_define = geo_obj.popup_video_props[vid_type];
    }

    if ((0 < vid_id.length) && vid_define)
    {
        var vid_src = vid_define["source"];
        if (!vid_src) {vid_src = "";}

        var vid_poster = "";
        if ("flv" == vid_type)
        {
            if (vid_id.match(/\.flv$/))
            {
                vid_poster = vid_id.replace(/\.flv$/, ".png");
            }
            else
            {
                vid_poster = vid_id + ".png";
                vid_id = vid_id + ".flv";
            }
        }

        var vid_value = vid_src.replace(/%%id%%/g, vid_id);
        var vid_value = vid_value.replace(/%%ps%%/g, vid_poster);

        var vid_height = attrs.m_video_height;
        if ((!vid_height) || ("" == vid_height)) {vid_height = vid_define["height"];}
        var vid_width = attrs.m_video_width;
        if ((!vid_width) || ("" == vid_width)) {vid_width = vid_define["width"];}

        var vid_path = vid_define["path"];
        if (!vid_path) {vid_path = "";}

        vid_value = vid_value.replace(/%%h%%/g, vid_height);
        vid_value = vid_value.replace(/%%w%%/g, vid_width);

        var emptify_server_part = false;
        var full_url_starts = ["http://", "https://", "ftp://", "ftps://"];
        var full_url_starts_count = full_url_starts.length;
        for (var uind = 0; uind < full_url_starts_count; uind++)
        {
            var one_url_start = full_url_starts[uind];
            if (one_url_start == vid_id.substring(0, one_url_start.length)) {emptify_server_part = true; break;}
        }
        if (emptify_server_part)
        {
            vid_path = "";
        }

        vid_value = vid_value.replace(/%%path%%/g, vid_path);

        attrs.m_embed = vid_value;
        attrs.m_embed_height = parseInt(vid_height);
        attrs.m_embed_width = parseInt(vid_width);

    }

};

GeoPopups.strings = {};
//GeoPopups.strings.empty_label_show = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
GeoPopups.strings.empty_label_show = "&nbsp;" + "Fill in location label" + "&nbsp;";
//GeoPopups.strings.empty_label_show = GeoPopups.strings.empty_label_show.replace(/\s/gi, "&nbsp;");


GeoPopups.show_inline_label_view = function(geo_obj, rank) {
    $('#geo_edit_label_inline').addClass('map_hidden');
    $('#geo_show_label_inline').removeClass('map_hidden');
    //alert('to label');

    var edit_elm = document.getElementById ? document.getElementById('geo_edit_label_inline') : null;
    if (edit_elm) {
        var label_value = edit_elm.value;
        geo_obj.store_point_label(label_value, rank);
        var show_elm = document.getElementById ? document.getElementById('label_inner_edit_value') : null;
        label_value_show = label_value;
        if ("" == label_value) {
            label_value_show = GeoPopups.strings.empty_label_show;
        }
        show_elm.innerHTML = label_value_show;
    }

};

GeoPopups.show_inline_label_change = function(geo_obj, rank, value) {
    if ("" == value) {
        $('#label_inner_edit_value').addClass('map_text_lack');
        $('#geo_edit_label_inline').addClass('map_text_lack');
    } else {
        $('#label_inner_edit_value').removeClass('map_text_lack');
        $('#geo_edit_label_inline').removeClass('map_text_lack');
    }
    //alert(rank);
}

GeoPopups.show_inline_label_edit = function(geo_obj, rank, focus) {
    $('#geo_edit_label_inline').removeClass('map_hidden');
    $('#geo_show_label_inline').addClass('map_hidden');

    //if (true || focus) {
        var edit_elm = document.getElementById ? document.getElementById('geo_edit_label_inline') : null;
        if (edit_elm) {
            edit_elm.focus();
        }
        //setTimeout("var elm = document.getElementById(\"geo_edit_label_inline\"); elm.focus();", 2000);
    //}
};

// preparing html content for a popup
GeoPopups.create_popup_content = function(feature, geo_obj) {
    var none_info = {'inner_html': "", 'min_width': 0, 'min_height': 0};

    if (!feature) {return none_info;}

    var attrs = feature.attributes;
    if (!attrs) {return none_info;}

    var editing = false;
    if (geo_obj.editing) {
        editing = true;
    }

    var rank = attrs.m_rank;

    var pop_text = "";
    {
        var pop_link = attrs.m_link;
        var pop_title = "" + feature.attributes.m_title;
        pop_title = pop_title.replace(/&/gi, "&amp;");
        pop_title = pop_title.replace(/>/gi, "&gt;");
        pop_title = pop_title.replace(/</gi, "&lt;");

        if (editing) {
            var pop_title_show = pop_title;
            var label_class_add = "";
            if ("" == pop_title) {
                label_class_add = "map_text_lack";
                pop_title_show = GeoPopups.strings.empty_label_show;
            }

            pop_text += "<input id='geo_edit_label_inline' class='map_hidden " + label_class_add + "' type='text' value='" + pop_title + "' onBlur=\"GeoPopups.show_inline_label_view(" + geo_obj.obj_name + ", " + rank + "); return false;\" onKeyUp='GeoPopups.show_inline_label_change(" + geo_obj.obj_name + ", " + rank + ", this.value); return true;'>";
            pop_text += "<h3 class='popup_title inline_editable' id='geo_show_label_inline'>";
            //pop_text += "<a href='#' class='link icon-link' onClick=\"GeoPopups.show_inline_label_edit(" + geo_obj.obj_name + ", " + rank + "); return false;\"; return false;\"><span class='icon ui-icon-pencil'></span></a>";

            pop_link = "#";
            pop_text += "<a href=\"" + pop_link + "\" target=\"_blank\" onClick=\"GeoPopups.show_inline_label_edit(" + geo_obj.obj_name + ", " + rank + ", true); return false;\">";
            pop_text += "<span id='label_inner_edit_value' class='" + label_class_add + "'>" + pop_title_show; + "</span>";

            pop_text += "</a>";
            pop_text += "</h3>";

        } else {

            pop_text += "<h3 class='popup_title' id='geo_show_label_inline'>";

            if (0 < pop_link.length) {
                pop_text += "<a href=\"" + pop_link + "\" target=\"_blank\">";
            }
            pop_text += pop_title;
            if (0 < pop_link.length) {
                pop_text += "</a>";
            }
            pop_text += "</h3>";

        }

    }

    var with_embed = false;
    {
        if (feature.attributes.m_image)
        {
            pop_text += "<div class='popup_image'>" + feature.attributes.m_image + "</div>";
        }
        if (feature.attributes.m_embed)
        {
            pop_text += "<div class='popup_video'>" + feature.attributes.m_embed + "</div>";
            with_embed = true;
        }
    }

    if (attrs.m_direct)
    {
        var content = attrs.m_content;
        if (!content) {content = "";}
        pop_text += "<div class='popup_content'>" + content + "</div>";
    }
    else
    {
        var plain_text = feature.attributes.m_text;
        plain_text = plain_text.replace(/&/gi, "&amp;");
        plain_text = plain_text.replace(/>/gi, "&gt;");
        plain_text = plain_text.replace(/</gi, "&lt;");
        plain_text = plain_text.replace(/\r\n/gi, "</p><p>");
        plain_text = plain_text.replace(/\n/gi, "</p><p>");
        plain_text = plain_text.replace(/\r/gi, "</p><p>");

        pop_text += "<div class='popup_text'><p>" + plain_text + "</p></div>";
    }

    if (attrs.m_backlinks) {
        pop_text += "<div class='article_backlinks'>" + geo_obj.display_strings.articles + ": ";
        var bl_count = attrs.m_backlinks.length;
        for (var bl_ind = 0; bl_ind < bl_count; bl_ind++) {
            var curr_backlink = attrs.m_backlinks[bl_ind];
            if (0 < bl_ind) {pop_text += ", ";}
            pop_text += "<a href=\"" + curr_backlink + "\" target=\"_blank\">" + (bl_ind + 1) + "</a>";
        }

        pop_text += "</div>";
    }
    else if (attrs.m_backlink) {
        pop_text += "<div class='article_backlinks'> " + geo_obj.display_strings.articles + ": <a href=\"" + attrs.m_backlink + "\" target=\"_blank\">" + "1" + "</a></div>";
    }

    var min_width = geo_obj.popup_width;
    var min_height = geo_obj.popup_height;
    if (with_embed) {
        var min_width_embed = feature.attributes.m_embed_width + 100;
        var min_height_embed = feature.attributes.m_embed_height + 100;
        if (min_width_embed > min_width) {min_width = min_width_embed;}
        if (min_height_embed > min_height) {min_height = min_height_embed;}
    }

    return {'inner_html': pop_text, 'min_width': min_width, 'min_height': min_height};
};

