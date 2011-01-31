GeoPopups = {};

// preparing image html tag for a point popup
GeoPopups.set_image_tag = function(attrs)
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
GeoPopups.set_embed_tag = function(attrs)
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
        vid_define = this.popup_video_props[vid_type];
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

// preparing html content for a popup
GeoPopups.create_popup_content = function(feature, geo_obj) {
    var none_info = {'inner_html': "", 'min_width': 0, 'min_height': 0};

    if (!feature) {return none_info;}

    var attrs = feature.attributes;
    if (!attrs) {return none_info;}

    var pop_text = "";
    {
        var pop_link = attrs.m_link;
        var pop_title = "" + feature.attributes.m_title;
        pop_title = pop_title.replace(/&/gi, "&amp;");
        pop_title = pop_title.replace(/>/gi, "&gt;");
        pop_title = pop_title.replace(/</gi, "&lt;");

        pop_text += "<div class='popup_title'>";
        if (0 < pop_link.length) {
            pop_text += "<a href=\"" + pop_link + "\" target=\"_blank\">";
        }
        pop_text += pop_title;
        if (0 < pop_link.length) {
            pop_text += "</a>";
        }
        pop_text += "</div>";
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

