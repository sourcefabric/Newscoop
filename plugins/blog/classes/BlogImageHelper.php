<?php
class BlogImageHelper {   
    private static function GetImageFormats()
    {
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
            return $url;    
        } else {
            return $path;
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
        }

        return $return_value;
    }

    public static function RemoveImageDerivates($p_object_type, $p_object_id)
    {
        foreach (self::GetImagePaths($p_object_type, $p_object_id, true) as $path) {
            unlink($path);
        }
    }   
    
}