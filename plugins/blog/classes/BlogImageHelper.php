<?php
class BlogImageHelper {   
    private static function GetImageFormats()
    {
        $formats[] = array('width' => 100, 'height' => 100);
        $format_prefs = SystemPref::Get("PLUGIN_BLOG_IMAGE_DERIVATES");
        
        if (strlen($format_prefs)) {
            foreach (explode("\n", $format_prefs) as $format) {
                if (preg_match('/([0-9]*) *[xX] *([0-9]*)/', $format, $matched)) {
                    $formats[] = array('width' => $matched[1], 'height' => $matched[2]);
                }   
            }
        }
        return (array) $formats;
    }

    public static function GetImagePaths($p_object_type, $p_object_id, $p_check_exists=false, $p_as_url=false)
    {
        global $Campsite;
        
        foreach (BlogImageHelper::GetImageFormats() as $format) {
            $width = $format['width'];
            $height = $format['height'];
            $path[$width.'x'.$height] = $Campsite['IMAGE_DIRECTORY']."plugin_blog/$p_object_type/{$width}x{$height}/image_{$p_object_id}.png";
            
            $url[$width.'x'.$height] = $Campsite['IMAGE_BASE_URL']."plugin_blog/$p_object_type/{$width}x{$height}/image_{$p_object_id}.png";

            if ($p_check_exists && !file_exists($path[$width.'x'.$height])) {
                unset($path[$width.'x'.$height]);
                unset($url[$width.'x'.$height]);
            }
        }

        if ($p_as_url) {
            return (array) $url;    
        } else {
            return (array) $path;
        }
    }
    
    public static function TestImage($p_image)
    {
        system("identify {$p_image['tmp_name']}", $return_code);
        
        return $return_code;
    }

    public static function StoreImageDerivates($p_object_type, $p_object_id, $p_image)
    {
        if ($p_image['error'] !== 0 || self::TestImage($p_image) !== 0) {
            return false;   
        }

        foreach (BlogImageHelper::GetImagePaths($p_object_type, $p_object_id) as $dim => $path) {
            list ($width, $height) = explode('x', $dim);
            
            $d_width = $width * 2;
            $d_height = $height * 2;

            if (!file_exists(dirname($path))) {
                $mkdir = '';
                foreach (explode('/', dirname($path)) as $k => $dir) {
                    $mkdir .= '/'.$dir;
                    @mkdir($mkdir, 0775);
                }
            }

            $cmd = "convert -resize {$d_width}x -resize 'x{$d_height}<' -resize 50% -gravity center  -crop {$width}x{$height}+0+0 +repage {$p_image['tmp_name']} $path";
            system($cmd, $return_value);
            
            $all_right = $return_value || $all_right;
            
            if ($return_value ==  0) {
                $success[] = $width.'x'.$height;       
            } else {
                $failed[] = $width.'x'.$height;   
            }
        }
        
        if (function_exists('camp_html_add_msg')) {
            if (is_array($success)) {
                camp_html_add_msg(getGS('Created image derivate(s): $1', implode(', ', $success)), 'ok');
            }
            if (is_array($failed)) {
                camp_html_add_msg(getGS('Failed to create image derivate(s): $1', implode(', ', $failed)), 'error');    
            }
        }

        return $all_right;
    }

    public static function RemoveImageDerivates($p_object_type, $p_object_id)
    {
        foreach (self::GetImagePaths($p_object_type, $p_object_id, true) as $path) {
            unlink($path);
        }
    }   
    
}