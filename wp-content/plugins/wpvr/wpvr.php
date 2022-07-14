<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://rextheme.com/
 * @since             7.3.6
 * @package           Wpvr
 *
 * @wordpress-plugin
 * Plugin Name:       WP VR
 * Plugin URI:        https://rextheme.com/wpvr/
 * Description:       WP VR - 360 Panorama and virtual tour creator for WordPress is a customized panaroma & virtual builder tool for WordPress Website.
 * Version:           8.0.2
 * Author:            Rextheme
 * Author URI:        http://rextheme.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wpvr
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

require plugin_dir_path(__FILE__) . 'elementor/elementor.php';

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('WPVR_VERSION', '8.0.2');
define('WPVR_FILE', __FILE__);
define("WPVR_PLUGIN_DIR_URL", plugin_dir_url(__FILE__));
define("WPVR_PLUGIN_DIR_PATH", plugin_dir_path(__FILE__));
define("WPVR_PLUGIN_PUBLIC_DIR_URL", plugin_dir_url(__FILE__) . 'public/');
define('WPVR_BASE', plugin_basename(WPVR_FILE));

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wpvr-activator.php
 */
function activate_wpvr()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-wpvr-activator.php';
    Wpvr_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wpvr-deactivator.php
 */
function deactivate_wpvr()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-wpvr-deactivator.php';
    Wpvr_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_wpvr');
register_deactivation_hook(__FILE__, 'deactivate_wpvr');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-wpvr.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    7.3.6
 */
function run_wpvr()
{

    $plugin = new Wpvr();
    $plugin->run();
}
run_wpvr();


/**
 * Array information checker
 * 
 * @param mixed $needle
 * @param mixed $haystack
 * @param bool $strict
 * 
 * @return bool
 * @since 7.3.6
 */
function wpvr_in_array_r($needle, $haystack, $strict = false)
{
    foreach ($haystack as $item) {
        if ((($strict ? $item === $needle : $item == $needle)) || is_array($item) && wpvr_in_array_r($needle, $item, $strict)) {
            return true;
        }
    }
    return false;
}


/**
 * Initialize the plugin tracker
 *
 * @return void
 * @since 7.3.6
 */
function appsero_init_tracker_wpvr() {
    if (!class_exists('Appsero\Client')) {
        require_once __DIR__ . '/appsero/src/Client.php';
    }
    $client = new Appsero\Client( 'cab9761e-b067-4824-9c71-042df5d58598', 'WP VR', __FILE__ );

    // Active insights
    $client->insights()->init();
}

appsero_init_tracker_wpvr();


function wpvr_block()
{
    wp_register_script(
        'wpvr-block',
        plugins_url('build/index.build.js', __FILE__),
        array('wp-blocks', 'wp-element', 'wp-components', 'wp-editor')
    );
    wp_enqueue_style(
        'gutyblocks/guty-block',
        plugins_url('src/view.css', __FILE__),
        array()
    );

    if (function_exists('register_block_type')) {
        register_block_type('wpvr/wpvr-block', array(
            'attributes'      => array(
                'id' => array(
                    'type' => 'string',
                    'default' => '0',
                ),
                'width' => array(
                    'type' => 'string',
                    'default' => '600',
                ),
                'height' => array(
                    'type' => 'string',
                    'default' => '400',
                ),
                'mobile_height' => array(
                    'type' => 'string',
                    'default' => '300',
                ),
                'radius' => array(
                    'type' => 'string',
                    'default' => '0',
                ),
                'content' => array(
                    'type' => 'string',
                    'source' => 'html',
                    'default' => '<script>          </script>'
                ),
            ),
            'editor_script' => 'wpvr-block',
            'render_callback' => 'wpvr_block_render',
        ));
    }
}

add_action('init', 'wpvr_block');

function wpvr_block_render($attributes)
{
    if (isset($attributes['id'])) {
        $id = $attributes['id'];
    } else {
        $id = 0;
    }
    if (isset($attributes['width'])) {
        $width = $attributes['width'];
    }
    if (isset($attributes['height'])) {
        $height = $attributes['height'];
    }
    if (isset($attributes['mobile_height'])) {
        $mobile_height = $attributes['mobile_height'];
    }
    if (isset($attributes['radius'])) {
        $radius = $attributes['radius'] . 'px';
    }

    $postdata = get_post_meta($id, 'panodata', true);
    $panoid = 'pano' . $id;

    if (isset($postdata['streetviewdata'])) {
        if (empty($width)) {
            $width = '600px';
        }
        if (empty($height)) {
            $height = '400px';
        }
        $streetviewurl = $postdata['streetviewurl'];
        $html = '';
        $html .= '<div class="vr-streetview" style="text-align: center; max-width:100%; width:' . $width . 'px; height:' . $height . 'px; margin: 0 auto;">';
        $html .= '<iframe src="' . $streetviewurl . '" frameborder="0" style="border:0; width:100px; height:100%;" allowfullscreen=""></iframe>';
        $html .= '</div>';



        return $html;
    }

    if (isset($postdata['vidid'])) {
        if (empty($width)) {
            $width = '600';
        }
        if (empty($height)) {
            $height = '400';
        }
        $videourl = $postdata['vidurl'];

        $videourl = $postdata['vidurl'];
        $autoplay = 'off';
        if (isset($postdata['autoplay'])) {
            $autoplay = $postdata['autoplay'];
        }
        $loop = 'off';
        if (isset($postdata['loop'])) {
            $loop = $postdata['loop'];
        }

        if (strpos($videourl, 'youtube') > 0 || strpos($videourl, 'youtu') > 0) {
            $explodeid = '';
            $explodeid = explode("=", $videourl);
            $foundid = '';
            $muted = '&mute=1';

            if ($loop == 'on') {
                $loop = '&loop=1';
            } else {
                $loop = '';
            }

            if (strpos($videourl, 'youtu') > 0) {
                $explodeid = explode("/", $videourl);
                $foundid = $explodeid[3] . '?' . $autoplay . $loop;
                $expdata = $explodeid[3];
            } else {
                $foundid = $explodeid[1] . '?' . $autoplay . $loop;
                $expdata = $explodeid[1];
            }
            $html = '';
            $html .= '<div style="text-align:center; max-width:100%; width:' . $width . 'px; height:' . $height . 'px; border-radius: ' . $radius . '; margin: 0 auto;">';

            $html .= '
              <iframe src="https://www.youtube.com/embed/' . $expdata . '?rel=0&modestbranding=1' . $loop . '&autohide=1' . $muted . '&showinfo=0&controls=1' . $autoplay . '&playlist=' . $expdata . '"  width="100%" height="100%" style="border-radius: ' . $radius . ';" frameborder="0" allowfullscreen></iframe>
          ';
            $html .= '</div>';
        } elseif (strpos($videourl, 'vimeo') > 0) {
            $explodeid = '';
            $explodeid = explode("/", $videourl);
            $foundid = '';

            if ($autoplay == 'on') {
                $autoplay = '&autoplay=1&muted=1';
            } else {
                $autoplay = '';
            }

            if ($loop == 'on') {
                $loop = '&loop=1';
            } else {
                $loop = '';
            }

            $foundid = $explodeid[3] . '?' . $autoplay . $loop;
            $html = '';
            $html .= '<div style="text-align:center; max-width:100%; width:' . $width . 'px; height:' . $height . 'px; margin: 0 auto;">';
            $html .= '<iframe src="https://player.vimeo.com/video/' . $foundid . '" width="' . $width . '" height="' . $height . '" style="border-radius: ' . $radius . ';" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
            $html .= '</div>';
        } else {
            $html = '';
            $html .= '<div id="pano' . $id . '" class="pano-wrap" style="max-width:100%; width:' . $width . 'px; height: ' . $height . 'px; border-radius:' . $radius . '; margin: 0 auto;">';
            $html .= '<div style="width:100%; height:100%; border-radius: ' . $radius . ';">' . $postdata['panoviddata'] . '</div>';

            $html .= '
            <style>
                .video-js {
                    border-radius:' . $radius . ';
                }
                .video-js canvas{
                    border-radius:' . $radius . ';
                }
                #pano' . $id . ' .vjs-poster {
                    border-radius: ' . $radius . ';
                }
            </style>
            
            ';

            // $html .= '<script>';
            // $html .= 'videojs(' . $postdata['vidid'] . ', {';
            // $html .= 'plugins: {';
            // $html .= 'pannellum: {}';
            // $html .= '}';
            // $html .= '});';
            // $html .= '</script>';
            $html .= '</div>';

            //video js vr setup //
            $html .= '<script>';
            $html .= '
                (function (window, videojs) {
                    var player = window.player = videojs("' . $postdata['vidid'] . '");
                    player.mediainfo = player.mediainfo || {};
                    player.mediainfo.projection = "equirectangular";
                
                    // AUTO is the default and looks at mediainfo
                    var vr = window.vr = player.vr({ projection: "AUTO", debug: true, forceCardboard: false, antialias: false });
                    }(window, window.videojs));
                
                ';
            $html .= '</script>';
            //video js vr end //
        }
        return $html;
    }

    $control = false;
    if (isset($postdata['showControls'])) {
        $control = $postdata['showControls'];
    }

    if ($control) {
        if (isset($postdata['customcontrol'])) {
            $custom_control = $postdata['customcontrol'];
            if ($custom_control['panupSwitch'] == "on" || $custom_control['panDownSwitch'] == "on" || $custom_control['panLeftSwitch'] == "on" || $custom_control['panRightSwitch'] == "on" || $custom_control['panZoomInSwitch'] == "on" || $custom_control['panZoomOutSwitch'] == "on" || $custom_control['panFullscreenSwitch'] == "on" || $custom_control['gyroscopeSwitch'] == "on" || $custom_control['backToHomeSwitch'] == "on") {
                $control = false;
            }
        }
    }

    $vrgallery = false;
    if (isset($postdata['vrgallery'])) {
        $vrgallery = $postdata['vrgallery'];
    }

    $vrgallery_title = false;
    if (isset($postdata['vrgallery_title'])) {
        $vrgallery_title = $postdata['vrgallery_title'];
    }

    $vrgallery_display = false;
    if (isset($postdata['vrgallery_display'])) {
        $vrgallery_display = $postdata['vrgallery_display'];
    }

    $gyro = false;
    $gyro_orientation = false;
    if (isset($postdata['gyro'])) {
        $gyro = $postdata['gyro'];
        if (isset($postdata['deviceorientationcontrol'])) {
            $gyro_orientation = $postdata['deviceorientationcontrol'];
        }
    }


    $compass = false;
    $audio_right = "5px";
    if (isset($postdata['compass']) || $postdata['compass'] == 'on') {
        $compass = $postdata['compass'] == 'on' || $postdata['compass'] != null ? true : false;
        if ($compass) {
            $audio_right = "60px";
        }
    }

    //===explainer  handle===//
    $explainer_right = "10px";
    if ((isset($postdata['compass']) && $postdata['compass'] == 'on') && (isset($postdata['bg_music']) && $postdata['bg_music'] == 'on')) {
        $explainer_right = "90px";
    } elseif (isset($postdata['compass']) && $postdata['compass'] == 'on') {
        $explainer_right = "60px";
    } elseif (isset($postdata['bg_music']) && $postdata['bg_music'] == "on") {
        $explainer_right = "30px";
    }

    //===explainer  handle===//

    $mouseZoom = true;
    if (isset($postdata['mouseZoom'])) {
        $mouseZoom = $postdata['mouseZoom'];
    }

    $draggable = true;
    if (isset($postdata['draggable'])) {
        $draggable = $postdata['draggable'] == 'on' || $postdata['draggable'] != null ? true : false;
    }

    $diskeyboard = false;
    if (isset($postdata['diskeyboard'])) {
        $diskeyboard = $postdata['diskeyboard'] == 'off' || $postdata['diskeyboard'] == null ? false : true;
    }

    $keyboardzoom = true;
    if (isset($postdata['keyboardzoom'])) {
        $keyboardzoom = $postdata['keyboardzoom'];
    }

    $autoload = false;

    if (isset($postdata['autoLoad'])) {
        $autoload = $postdata['autoLoad'];
    }

    $default_scene = '';
    if (isset($postdata['defaultscene'])) {
        $default_scene = $postdata['defaultscene'];
    }

    $preview = '';
    if (isset($postdata['preview'])) {
        $preview = $postdata['preview'];
    }

    $autorotation = '';
    if (isset($postdata["autoRotate"])) {
        $autorotation = $postdata["autoRotate"];
    }
    $autorotationinactivedelay = '';
    if (isset($postdata["autoRotateInactivityDelay"])) {
        $autorotationinactivedelay = $postdata["autoRotateInactivityDelay"];
    }
    $autorotationstopdelay = '';
    if (isset($postdata["autoRotateStopDelay"])) {
        $autorotationstopdelay = $postdata["autoRotateStopDelay"];
    }

    $scene_fade_duration = '';
    if (isset($postdata['scenefadeduration'])) {
        $scene_fade_duration = $postdata['scenefadeduration'];
    }

    $panodata = '';
    if (isset($postdata['panodata'])) {
        $panodata = $postdata['panodata'];
    }

    $default_zoom_global = 100;
    if (isset($postdata['hfov']) && $postdata['hfov'] != '') {
        $default_zoom_global = $postdata['hfov'];
    }

    $min_zoom_global = 50;
    if (isset($postdata['minHfov']) && $postdata['minHfov'] != '') {
        $min_zoom_global = $postdata['minHfov'];
    }

    $max_zoom_global = 120;
    if (isset($postdata['maxHfov']) && $postdata['maxHfov'] != '') {
        $max_zoom_global = $postdata['maxHfov'];
    }

    $hotspoticoncolor = '#00b4ff';
    $hotspotblink = 'on';
    $default_data = array();
    $default_data = array('firstScene' => $default_scene, 'sceneFadeDuration' => $scene_fade_duration, 'hfov' => $default_zoom_global, 'maxHfov' => $max_zoom_global, 'minHfov' => $min_zoom_global);
    $scene_data = array();

    if (!empty($panodata['scene-list'])) {
        foreach ($panodata['scene-list'] as $panoscenes) {
            $scene_ititle = '';
            if (isset($panoscenes["scene-ititle"])) {
                $scene_ititle = sanitize_text_field($panoscenes["scene-ititle"]);
            }

            $scene_author = '';
            if (isset($panoscenes["scene-author"])) {
                $scene_author = sanitize_text_field($panoscenes["scene-author"]);
            }

            $scene_author_url = '';
            if (isset($panoscenes["scene-author-url"])) {
                $scene_author_url = sanitize_text_field($panoscenes["scene-author-url"]);
            }

            $scene_vaov = 180;
            if (isset($panoscenes["scene-vaov"])) {
                $scene_vaov = (float)$panoscenes["scene-vaov"];
            }

            $scene_haov = 360;
            if (isset($panoscenes["scene-haov"])) {
                $scene_haov = (float)$panoscenes["scene-haov"];
            }

            $scene_vertical_offset = 0;
            if (isset($panoscenes["scene-vertical-offset"])) {
                $scene_vertical_offset = (float)$panoscenes["scene-vertical-offset"];
            }

            $default_scene_pitch = null;
            if (isset($panoscenes["scene-pitch"])) {
                $default_scene_pitch = (float)$panoscenes["scene-pitch"];
            }

            $default_scene_yaw = null;
            if (isset($panoscenes["scene-yaw"])) {
                $default_scene_yaw = (float)$panoscenes["scene-yaw"];
            }

            $scene_max_pitch = '';
            if (isset($panoscenes["scene-maxpitch"])) {
                $scene_max_pitch = (float)$panoscenes["scene-maxpitch"];
            }


            $scene_min_pitch = '';
            if (isset($panoscenes["scene-minpitch"])) {
                $scene_min_pitch = (float)$panoscenes["scene-minpitch"];
            }


            $scene_max_yaw = '';
            if (isset($panoscenes["scene-maxyaw"])) {
                $scene_max_yaw = (float)$panoscenes["scene-maxyaw"];
            }


            $scene_min_yaw = '';
            if (isset($panoscenes["scene-minyaw"])) {
                $scene_min_yaw = (float)$panoscenes["scene-minyaw"];
            }

            $default_zoom = 100;
            if (isset($panoscenes["scene-zoom"]) && $panoscenes["scene-zoom"] != '') {
                $default_zoom = (int)$panoscenes["scene-zoom"];
            } else {
                if ($default_zoom_global != '') {
                    $default_zoom =  (int)$default_zoom_global;
                }
            }


            $max_zoom = 120;
            if (isset($panoscenes["scene-maxzoom"]) && $panoscenes["scene-maxzoom"] != '') {
                $max_zoom = (int)$panoscenes["scene-maxzoom"];
            } else {
                if ($max_zoom_global != '') {
                    $max_zoom =  (int)$max_zoom_global;
                }
            }

            $min_zoom = 120;
            if (isset($panoscenes["scene-minzoom"]) && $panoscenes["scene-minzoom"] != '') {
                $min_zoom = (int)$panoscenes["scene-minzoom"];
            } else {
                if ($min_zoom_global != '') {
                    $min_zoom =  (int)$min_zoom_global;
                }
            }

            $hotspot_datas = array();
            if (isset($panoscenes['hotspot-list'])) {
                $hotspot_datas = $panoscenes['hotspot-list'];
            }

            $hotspots = array();

            foreach ($hotspot_datas as $hotspot_data) {
                $status  = get_option('wpvr_edd_license_status');
                if ($status !== false && $status == 'valid') {
                    if (isset($hotspot_data["hotspot-customclass-pro"]) && $hotspot_data["hotspot-customclass-pro"] != 'none') {
                        $hotspot_data["hotspot-customclass"] = $hotspot_data["hotspot-customclass-pro"];
                        $hotspoticoncolor = $hotspot_data["hotspot-customclass-color-icon-value"];
                    }
                    if (isset($hotspot_data['hotspot-blink'])) {
                        $hotspotblink = $hotspot_data['hotspot-blink'];
                    }
                }
                $hotspot_scene_pitch = '';
                if (isset($hotspot_data["hotspot-scene-pitch"])) {
                    $hotspot_scene_pitch = $hotspot_data["hotspot-scene-pitch"];
                }
                $hotspot_scene_yaw = '';
                if (isset($hotspot_data["hotspot-scene-yaw"])) {
                    $hotspot_scene_yaw = $hotspot_data["hotspot-scene-yaw"];
                }

                $hotspot_type = $hotspot_data["hotspot-type"] !== 'scene' ? 'info' : $hotspot_data["hotspot-type"];
                $hotspot_content = '';

                ob_start();
                do_action('wpvr_hotspot_content', $hotspot_data);
                $hotspot_content = ob_get_clean();

                if (!$hotspot_content) {
                    $hotspot_content = $hotspot_data["hotspot-content"];
                }

                if (isset($hotspot_data["wpvr_url_open"][0])) {
                    $wpvr_url_open = $hotspot_data["wpvr_url_open"][0];
                } else {
                    $wpvr_url_open = "off";
                }

                $hotspot_info = array(
                    'text' => $hotspot_data['hotspot-title'],
                    'pitch' => $hotspot_data['hotspot-pitch'],
                    'yaw' => $hotspot_data['hotspot-yaw'],
                    'type' => $hotspot_type,
                    'cssClass' => $hotspot_data['hotspot-customclass'],
                    'URL' => $hotspot_data['hotspot-url'],
                    "wpvr_url_open" => $wpvr_url_open,
                    "clickHandlerArgs" => $hotspot_content,
                    'createTooltipArgs' => $hotspot_data['hotspot-hover'],
                    "sceneId" => $hotspot_data["hotspot-scene"],
                    "targetPitch" => (float)$hotspot_scene_pitch,
                    "targetYaw" => (float)$hotspot_scene_yaw,
                    'hotspot_type' => $hotspot_data['hotspot-type']
                );

                $hotspot_info['URL'] = ($hotspot_data['hotspot-type'] === 'fluent_form' || $hotspot_data['hotspot-type'] === 'wc_product') ? '' : $hotspot_info['URL'];

                if ($hotspot_data["hotspot-customclass"] == 'none' || $hotspot_data["hotspot-customclass"] == '') {
                    unset($hotspot_info["cssClass"]);
                }
                if (empty($hotspot_data["hotspot-scene"])) {
                    unset($hotspot_info['targetPitch']);
                    unset($hotspot_info['targetYaw']);
                }
                array_push($hotspots, $hotspot_info);
            }

            $device_scene = $panoscenes['scene-attachment-url'];
            $mobile_media_resize = get_option('mobile_media_resize');
            $file_accessible = ini_get('allow_url_fopen');
            if ($mobile_media_resize == "true") {
                if ($file_accessible == "1") {
                    $image_info = getimagesize($device_scene);
                    if ($image_info && $image_info[0] > 4096) {
                        $src_to_id_for_mobile = '';
                        $src_to_id_for_desktop = '';
                        if (wpvr_isMobileDevice()) {
                            $src_to_id_for_mobile = attachment_url_to_postid($panoscenes['scene-attachment-url']);
                            if ($src_to_id_for_mobile) {
                                $mobile_scene = wp_get_attachment_image_src($src_to_id_for_mobile, 'wpvr_mobile');
                                if ($mobile_scene[3]) {
                                    $device_scene = $mobile_scene[0];
                                }
                            }
                        } else {
                            $src_to_id_for_desktop = attachment_url_to_postid($panoscenes['scene-attachment-url']);
                            if ($src_to_id_for_desktop) {
                                $desktop_scene = wp_get_attachment_image_src($src_to_id_for_mobile, 'full');
                                if ($desktop_scene && $desktop_scene[0]) {
                                    $device_scene = $desktop_scene[0];
                                }
                            }
                        }
                    }
                }
            }

            $scene_info = array();

            if ($panoscenes["scene-type"] == 'cubemap') {
                $pano_type = 'cubemap';
                $pano_attachment = array(
                    $panoscenes["scene-attachment-url-face0"],
                    $panoscenes["scene-attachment-url-face1"],
                    $panoscenes["scene-attachment-url-face2"],
                    $panoscenes["scene-attachment-url-face3"],
                    $panoscenes["scene-attachment-url-face4"],
                    $panoscenes["scene-attachment-url-face5"]
                );

                $scene_info = array('type' => $panoscenes['scene-type'], 'cubeMap' => $pano_attachment, "pitch" => $default_scene_pitch, "maxPitch" => $scene_max_pitch, "minPitch" => $scene_min_pitch, "maxYaw" => $scene_max_yaw, "minYaw" => $scene_min_yaw, "yaw" => $default_scene_yaw, "hfov" => $default_zoom, "maxHfov" => $max_zoom, "minHfov" => $min_zoom, "title" => $scene_ititle, "author" => $scene_author, "authorURL" => $scene_author_url, "vaov" => $scene_vaov, "haov" => $scene_haov, "vOffset" => $scene_vertical_offset, 'hotSpots' => $hotspots);
            } else {
                $scene_info = array('type' => $panoscenes['scene-type'], 'panorama' => $device_scene, "pitch" => $default_scene_pitch, "maxPitch" => $scene_max_pitch, "minPitch" => $scene_min_pitch, "maxYaw" => $scene_max_yaw, "minYaw" => $scene_min_yaw, "yaw" => $default_scene_yaw, "hfov" => $default_zoom, "maxHfov" => $max_zoom, "minHfov" => $min_zoom, "title" => $scene_ititle, "author" => $scene_author, "authorURL" => $scene_author_url, "vaov" => $scene_vaov, "haov" => $scene_haov, "vOffset" => $scene_vertical_offset, 'hotSpots' => $hotspots);
            }



            if (isset($panoscenes["ptyscene"])) {
                if ($panoscenes["ptyscene"] == "off") {
                    unset($scene_info['pitch']);
                    unset($scene_info['yaw']);
                }
            }

            if (empty($panoscenes["scene-ititle"])) {
                unset($scene_info['title']);
            }
            if (empty($panoscenes["scene-author"])) {
                unset($scene_info['author']);
            }
            if (empty($panoscenes["scene-author-url"])) {
                unset($scene_info['authorURL']);
            }

            if (empty($scene_vaov)) {
                unset($scene_info['vaov']);
            }

            if (empty($scene_haov)) {
                unset($scene_info['haov']);
            }

            if (empty($scene_vertical_offset)) {
                unset($scene_info['vOffset']);
            }

            if (isset($panoscenes["cvgscene"])) {
                if ($panoscenes["cvgscene"] == "off") {
                    unset($scene_info['maxPitch']);
                    unset($scene_info['minPitch']);
                }
            }
            if (empty($panoscenes["scene-maxpitch"])) {
                unset($scene_info['maxPitch']);
            }

            if (empty($panoscenes["scene-minpitch"])) {
                unset($scene_info['minPitch']);
            }

            if (isset($panoscenes["chgscene"])) {
                if ($panoscenes["chgscene"] == "off") {
                    unset($scene_info['maxYaw']);
                    unset($scene_info['minYaw']);
                }
            }
            if (empty($panoscenes["scene-maxyaw"])) {
                unset($scene_info['maxYaw']);
            }

            if (empty($panoscenes["scene-minyaw"])) {
                unset($scene_info['minYaw']);
            }

            // if (isset($panoscenes["czscene"])) {
            //     if ($panoscenes["czscene"] == "off") {
            //         unset($scene_info['hfov']);
            //         unset($scene_info['maxHfov']);
            //         unset($scene_info['minHfov']);
            //     }
            // }

            $scene_array = array();
            $scene_array = array(
                $panoscenes['scene-id'] => $scene_info
            );

            $scene_data[$panoscenes['scene-id']] = $scene_info;
        }
    }

    $pano_id_array = array();
    $pano_id_array = array('panoid' => $panoid);
    $pano_response = array();
    $pano_response = array('autoLoad' => $autoload, 'showControls' => $control, 'compass' => $compass, 'orientationOnByDefault' => $gyro_orientation, 'mouseZoom' => $mouseZoom, 'draggable' => $draggable, 'disableKeyboardCtrl' => $diskeyboard, 'keyboardZoom' => $keyboardzoom, "preview" => $preview, "autoRotate" => $autorotation, "autoRotateInactivityDelay" => $autorotationinactivedelay, "autoRotateStopDelay" => $autorotationstopdelay, 'default' => $default_data, 'scenes' => $scene_data);
    if (empty($autorotation)) {
        unset($pano_response['autoRotate']);
        unset($pano_response['autoRotateInactivityDelay']);
        unset($pano_response['autoRotateStopDelay']);
    }
    if (empty($autorotationinactivedelay)) {
        unset($pano_response['autoRotateInactivityDelay']);
    }
    if (empty($autorotationstopdelay)) {
        unset($pano_response['autoRotateStopDelay']);
    }

    $response = array();
    $response = array($pano_id_array, $pano_response);
    if (!empty($response)) {
        $response = json_encode($response);
    }
    if (empty($width)) {
        $width = '600';
    }
    if (empty($height)) {
        $height = '400';
    }

    $foreground_color = '#fff';
    $pulse_color = wpvr_hex2rgb($hotspoticoncolor);
    $rgb = wpvr_HTMLToRGB($hotspoticoncolor);
    $hsl = wpvr_RGBToHSL($rgb);
    if ($hsl->lightness > 200) {
        $foreground_color = '#000000';
    } else {
        $foreground_color = '#fff';
    }

    $class = 'myclass';
    $html = 'test';
    $html = '';
    $html .= '<style>';
    if ($width == 'embed') {
        $html .= 'body{
             overflow: hidden;
        }';
    }
    $html .= '#' . $panoid . ' div.pnlm-hotspot-base.fas,
          #' . $panoid . ' div.pnlm-hotspot-base.fab,
          #' . $panoid . ' div.pnlm-hotspot-base.fa,
          #' . $panoid . ' div.pnlm-hotspot-base.far {
              display: block !important;
              background-color: ' . $hotspoticoncolor . ';
              color: ' . $foreground_color . ';
              border-radius: 100%;
              width: 30px;
              height: 30px;
              font-size: 16px;
              line-height: 30px;
              animation: icon-pulse' . $panoid . ' 1.5s infinite cubic-bezier(.25, 0, 0, 1);
          }';
    if ($hotspotblink == 'on') {
        $html .= '@-webkit-keyframes icon-pulse' . $panoid . ' {
                0% {
                    box-shadow: 0 0 0 0px rgba(' . $pulse_color[0] . ', 1);
                }
                100% {
                    box-shadow: 0 0 0 10px rgba(' . $pulse_color[0] . ', 0);
                }
            }
            @keyframes icon-pulse' . $panoid . ' {
                0% {
                    box-shadow: 0 0 0 0px rgba(' . $pulse_color[0] . ', 1);
                }
                100% {
                    box-shadow: 0 0 0 10px rgba(' . $pulse_color[0] . ', 0);
                }
            }';
    }

    $status  = get_option('wpvr_edd_license_status');
    if ($status !== false && $status == 'valid') {
        if (!$gyro) {
            $html .= '#' . $panoid . ' div.pnlm-orientation-button {
                    display: none;
                }';
        }
    } else {
        $html .= '#' . $panoid . ' div.pnlm-orientation-button {
                    display: none;
                }';
    }

    $html .= '</style>';

    if ($width == 'fullwidth') {
        if (wpvr_isMobileDevice()) {
            $html .= '<div id="pano' . $id . '" class="pano-wrap" style="text-align:center; border-radius:' . $radius . '; direction:ltr;" >';
        } else {
            $html .= '<div id="pano' . $id . '" class="pano-wrap vrfullwidth" style=" text-align:center; height: ' . $height . 'px; border-radius:' . $radius . '; direction:ltr;" >';
        }
    } else {
        if (wpvr_isMobileDevice()) {
            $html .= '<div id="pano' . $id . '" class="pano-wrap" style=" text-align:center; max-width:100%; width: ' . $width . 'px; height: ' . $mobile_height . 'px !important; margin: 0 auto; border-radius:' . $radius . '; direction:ltr;">';
        } else {
            $html .= '<div id="pano' . $id . '" class="pano-wrap" style=" text-align:center; max-width:100%; width: ' . $width . 'px; height: ' . $height . 'px; margin: 0 auto; border-radius:' . $radius . '; direction:ltr;">';
        }
    }

    //===company logo===//
    if (isset($postdata['cpLogoSwitch'])) {
        $cpLogoImg = $postdata['cpLogoImg'];
        $cpLogoContent = $postdata['cpLogoContent'];
        if ($postdata['cpLogoSwitch'] == 'on') {
            $html .= '<div id="cp-logo-controls">';
            $html .= '<div class="cp-logo-ctrl" id="cp-logo">';
            if ($cpLogoImg) {
                $html .= '<img src="' . $cpLogoImg . '" alt="Company Logo">';
            }

            if ($cpLogoContent) {
                $html .= '<div class="cp-info">' . $cpLogoContent . '</div>';
            }
            $html .= '</div>';
            $html .= '</div>';
        }
    }
    //===company logo ends===//

    //===Background Tour===//
    if (isset($postdata['bg_tour_enabler'])) {

        $bg_tour_enabler = $postdata['bg_tour_enabler'];
        if ($bg_tour_enabler == 'on') {
            $bg_tour_navmenu = $postdata['bg_tour_navmenu'];
            $bg_tour_title = $postdata['bg_tour_title'];
            $bg_tour_subtitle = $postdata['bg_tour_subtitle'];

            if ($bg_tour_navmenu == 'on') {
                $menuLocations = get_nav_menu_locations();
                $menuID = $menuLocations['primary'];
                $primaryNav = wp_get_nav_menu_items($menuID);

                if ($primaryNav) {
                    $html .= '<div class="wpvr-navbar-container">';
                    foreach ($primaryNav as $primaryNav_key => $primaryNav_value) {
                        if ($primaryNav_value->menu_item_parent == "0") {
                            $html .= '<li>';
                            $html .= '<a href="' . $primaryNav_value->url . '">' . $primaryNav_value->title . '</a>';
                            $html .= '<ul class="wpvr-navbar-dropdown">';
                            foreach ($primaryNav as $pm_key => $pm_value) {
                                if ($pm_value->menu_item_parent == $primaryNav_value->ID) {
                                    $html .= '<li>';
                                    $html .= '<a href="' . $pm_value->url . '">' . $pm_value->title . '</a>';
                                    $html .= '</li>';
                                }
                            }
                            $html .= '</ul>';
                            $html .= '</li>';
                        }
                    }
                    $html .= '</div>';
                }
            }

            $html .= '<div class="wpvr-home-content">';
            $html .= '<div class="wpvr-home-title">' . $bg_tour_title . '</div>';
            $html .= '<div class="wpvr-home-subtitle">' . $bg_tour_subtitle . '</div>';
            $html .= '</div>';
        }
    }
    //===Background Tour End===//

    //===Custom Control===//
    if (isset($custom_control)) {
        if ($custom_control['panZoomInSwitch'] == "on" || $custom_control['panZoomOutSwitch'] == "on" || $custom_control['gyroscopeSwitch'] == "on" || $custom_control['backToHomeSwitch'] == "on") {
            $html .= '<div id="zoom-in-out-controls' . $id . '" class="zoom-in-out-controls">';

            if ($custom_control['backToHomeSwitch'] == "on") {
                $html .= '<div class="ctrl" id="backToHome' . $id . '"><i class="' . $custom_control['backToHomeIcon'] . '" style="color:' . $custom_control['backToHomeColor'] . ';"></i></div>';
            }

            if ($custom_control['panZoomInSwitch'] == "on") {
                $html .= '<div class="ctrl" id="zoom-in' . $id . '"><i class="' . $custom_control['panZoomInIcon'] . '" style="color:' . $custom_control['panZoomInColor'] . ';"></i></div>';
            }

            if ($custom_control['panZoomOutSwitch'] == "on") {
                $html .= '<div class="ctrl" id="zoom-out' . $id . '"><i class="' . $custom_control['panZoomOutIcon'] . '" style="color:' . $custom_control['panZoomOutColor'] . ';"></i></div>';
            }

            if ($custom_control['gyroscopeSwitch'] == "on") {
                $html .= '<div class="ctrl" id="gyroscope' . $id . '"><i class="' . $custom_control['gyroscopeIcon'] . '" style="color:' . $custom_control['gyroscopeColor'] . ';"></i></div>';
            }

            $html .= '</div>';
        }
        //===zoom in out Control===//

        if ($custom_control['panupSwitch'] == "on" || $custom_control['panDownSwitch'] == "on" || $custom_control['panLeftSwitch'] == "on" || $custom_control['panRightSwitch'] == "on" || $custom_control['panFullscreenSwitch'] == "on") {

            //===Custom Control===//
            $html .= '<div class="controls" id="controls' . $id . '">';

            if ($custom_control['panupSwitch'] == "on") {
                $html .= '<div class="ctrl pan-up" id="pan-up' . $id . '"><i class="' . $custom_control['panupIcon'] . '" style="color:' . $custom_control['panupColor'] . ';"></i></div>';
            }

            if ($custom_control['panDownSwitch'] == "on") {
                $html .= '<div class="ctrl pan-down" id="pan-down' . $id . '"><i class="' . $custom_control['panDownIcon'] . '" style="color:' . $custom_control['panDownColor'] . ';"></i></div>';
            }

            if ($custom_control['panLeftSwitch'] == "on") {
                $html .= '<div class="ctrl pan-left" id="pan-left' . $id . '"><i class="' . $custom_control['panLeftIcon'] . '" style="color:' . $custom_control['panLeftColor'] . ';"></i></div>';
            }

            if ($custom_control['panRightSwitch'] == "on") {
                $html .= '<div class="ctrl pan-right" id="pan-right' . $id . '"><i class="' . $custom_control['panRightIcon'] . '" style="color:' . $custom_control['panRightColor'] . ';"></i></div>';
            }

            if ($custom_control['panFullscreenSwitch'] == "on") {
                $html .= '<div class="ctrl fullscreen" id="fullscreen' . $id . '"><i class="' . $custom_control['panFullscreenIcon'] . '" style="color:' . $custom_control['panFullscreenColor'] . ';"></i></div>';
            }
            $html .= '</div>';
        }

        //===explainer button===//
        if ($custom_control['explainerSwitch'] == "on") {
            $html .= '<div class="explainer_button" id="explainer_button_' . $id . '" style="right:' . $explainer_right . '">';
            $html .= '<div class="ctrl" id="explainer_target_' . $id . '"><i class="' . $custom_control['explainerIcon'] . '" style="color:' . $custom_control['explainerColor'] . ';"></i></div>';
            $html .= '</div>';
        }

        //===explainer button===//
    }
    //===Custom Control===//

    if ($vrgallery) {
        //===Carousal setup===//
        $html .= '<div id="vrgcontrols' . $id . '" class="vrgcontrols">';

        $html .= '<div class="vrgctrl' . $id . ' vrbounce">';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '<div id="sccontrols' . $id . '" class="scene-gallery vrowl-carousel owl-theme">';
        if (isset($panodata["scene-list"])) {
            foreach ($panodata["scene-list"] as $panoscenes) {
                $scene_key = $panoscenes['scene-id'];
                if ($vrgallery_title == 'on') {
                    $scene_key_title = $panoscenes['scene-ititle'];
                    // $scene_key_title = $panoscenes['scene-id'];
                } else {
                    $scene_key_title = "";
                }

                if ($panoscenes['scene-type'] == 'cubemap') {
                    $img_src_url = $panoscenes['scene-attachment-url-face0'];
                } else {
                    $img_src_url = $panoscenes['scene-attachment-url'];
                }


                $src_to_id = attachment_url_to_postid($img_src_url);
                $thumbnail_array = wp_get_attachment_image_src($src_to_id, 'thumbnail');
                if ($thumbnail_array) {
                    $thumbnail = $thumbnail_array[0];
                } else {
                    $thumbnail = $img_src_url;
                }

                $html .= '<ul style="width:150px;"><li title="Double click to view scene">' . $scene_key_title . '<img class="scctrl" id="' . $scene_key . '_gallery_' . $id . '" src="' . $thumbnail . '"></li></ul>';
            }
        }
        $html .= '</div>';
        $html .= '
        <div class="owl-nav wpvr_slider_nav">
        <button type="button" role="presentation" class="owl-prev wpvr_owl_prev">
            <div class="nav-btn prev-slide"><i class="fa fa-angle-left"></i></div>
        </button>
        <button type="button" role="presentation" class="owl-next wpvr_owl_next">
            <div class="nav-btn next-slide"><i class="fa fa-angle-right"></i></div>
        </button>
        </div>
        ';
        //===Carousal setup end===//
    }

    if (isset($postdata['bg_music'])) {
        $bg_music = $postdata['bg_music'];
        $bg_music_url = $postdata['bg_music_url'];
        $autoplay_bg_music = $postdata['autoplay_bg_music'];
        $loop_bg_music = $postdata['loop_bg_music'];
        $bg_loop = '';
        if ($loop_bg_music == 'on') {
            $bg_loop = 'loop';
        }

        if ($bg_music == 'on') {
            $html .= '<div id="adcontrol' . $id . '" class="adcontrol" style="right:' . $audio_right . '">';
            $html .= '<audio class="vrAudioDefault" id="vrAudio' . $id . '" data-autoplay="' . $autoplay_bg_music . '"  onended="audionEnd' . $id . '()" ' . $bg_loop . '>
                    <source src="' . $bg_music_url . '" type="audio/mpeg">
                    Your browser does not support the audio element.
                  </audio>
                  <button onclick="playPause' . $id . '()" class="ctrl audio_control" data-play="' . $autoplay_bg_music . '" id="audio_control' . $id . '"><i id="vr-volume' . $id . '" class="wpvrvolumeicon' . $id . ' fas fa-volume-up" style="color:#fff;"></i></button>
                  ';
            $html .= '</div>';
        }
    }

    //===Explainer video section===//
    $explainerContent = "";
    if (isset($postdata['explainerContent'])) {
        $explainerContent = $postdata['explainerContent'];
    }
    $html .= '<div class="explainer" id="explainer' . $id . '" style="display: none">';
    $html .= '<span class="close-explainer-video"><i class="fa fa-times"></i></span>';
    $html .= '' . $explainerContent . '';
    $html .= '</div>';
    //===Explainer video section End===//

    $html .= '<div class="wpvr-hotspot-tweak-contents-wrapper" style="display: none">';
    $html .= '<i class="fa fa-times cross" data-id="' . $id . '"></i>';
    $html .= '<div class="wpvr-hotspot-tweak-contents-flex">';
    $html .= '<div class="wpvr-hotspot-tweak-contents">';
    ob_start();
    do_action('wpvr_hotspot_tweak_contents', $scene_data);
    $hotspot_content = ob_get_clean();
    $html .= $hotspot_content;
    $html .= '</div>';
    $html .= '</div>';
    $html .= '</div>';

    $html .= '<div class="custom-ifram-wrapper" style="display: none;">';
    $html .= '<i class="fa fa-times cross" data-id="' . $id . '"></i>';
    $html .= '<div class="custom-ifram-flex">';
    $html .= '<div class="custom-ifram">';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '</div>';

    //script started

    $html .= '<script>';
    if (isset($postdata['bg_music'])) {
        if ($bg_music == 'on') {
            $html .= '
            var x' . $id . ' = document.getElementById("vrAudio' . $id . '");

            var playing' . $id . ' = false;

              function playPause' . $id . '() {

                if (playing' . $id . ') {
                  jQuery("#vr-volume' . $id . '").removeClass("fas fa-volume-up");
                  jQuery("#vr-volume' . $id . '").addClass("fas fa-volume-mute");
                  x' . $id . '.pause();
                  jQuery("#audio_control' . $id . '").attr("data-play", "off");
                  playing' . $id . ' = false;

                }
                else {
                  jQuery("#vr-volume' . $id . '").removeClass("fas fa-volume-mute");
                  jQuery("#vr-volume' . $id . '").addClass("fas fa-volume-up");
                  jQuery("#audio_control' . $id . '").attr("data-play", "on");
                  x' . $id . '.play();
                  playing' . $id . ' = true;
                }
              }

              function audionEnd' . $id . '() {
                playing' . $id . ' = false;
                jQuery("#vr-volume' . $id . '").removeClass("fas fa-volume-up");
                jQuery("#vr-volume' . $id . '").addClass("fas fa-volume-mute");
                jQuery("#audio_control' . $id . '").attr("data-play", "off");
              }
              ';

            if ($autoplay_bg_music == 'on') {
                $html .= '
                document.getElementById("pano' . $id . '").addEventListener("click", musicPlay' . $id . ');
                function musicPlay' . $id . '() {
                    playing' . $id . ' = true;
                    document.getElementById("vrAudio' . $id . '").play();
                    document.getElementById("pano' . $id . '").removeEventListener("click", musicPlay' . $id . ');
                }
                ';
            }
        }
    }
    $html .= 'jQuery(document).ready(function() {';
    $html .= 'var response = ' . $response . ';';
    $html .= 'var scenes = response[1];';
    $html .= 'if(scenes) {';
    $html .= 'var scenedata = scenes.scenes;';
    $html .= 'for(var i in scenedata) {';
    $html .= 'var scenehotspot = scenedata[i].hotSpots;';
    $html .= 'for(var i = 0; i < scenehotspot.length; i++) {';
    $html .= 'if(scenehotspot[i]["clickHandlerArgs"] != "") {';
    $html .= 'scenehotspot[i]["clickHandlerFunc"] = wpvrhotspot;';
    $html .= '}';
    if (wpvr_isMobileDevice() && get_option('dis_on_hover') == "true") {
    } else {
        $html .= 'if(scenehotspot[i]["createTooltipArgs"] != "") {';
        $html .= 'scenehotspot[i]["createTooltipFunc"] = wpvrtooltip;';
        $html .= '}';
    }
    $html .= '}';
    $html .= '}';
    $html .= '}';
    $html .= 'var panoshow' . $id . ' = pannellum.viewer(response[0]["panoid"], scenes);';

    $html .= 'panoshow' . $id . '.on("load", function (){
          if (jQuery("#pano' . $id . '").children().children(".pnlm-panorama-info:visible").length > 0) {
               jQuery("#controls' . $id . '").css("bottom", "55px");
           }
           else {
             jQuery("#controls' . $id . '").css("bottom", "5px");
           }
        });';


    $html .= '
        if (scenes.autoRotate) {
          panoshow' . $id . '.on("load", function (){
           setTimeout(function(){ panoshow' . $id . '.startAutoRotate(scenes.autoRotate, 0); }, 3000);
          });
          panoshow' . $id . '.on("scenechange", function (){
           setTimeout(function(){ panoshow' . $id . '.startAutoRotate(scenes.autoRotate, 0); }, 3000);
          });
        }
        ';
    $html .= 'var touchtime = 0;';
    if ($vrgallery) {
        if (isset($panodata["scene-list"])) {
            foreach ($panodata["scene-list"] as $panoscenes) {
                $scene_key = $panoscenes['scene-id'];
                $scene_key_gallery = $panoscenes['scene-id'] . '_gallery_' . $id;
                $img_src_url = $panoscenes['scene-attachment-url'];
                // $html .= 'document.getElementById("' . $scene_key_gallery . '").addEventListener("click", function(e) { ';
                // $html .= 'if (touchtime == 0) {';
                // $html .= 'touchtime = new Date().getTime();';
                // $html .= '} else {';
                // $html .= 'if (((new Date().getTime()) - touchtime) < 800) {';
                // $html .= 'panoshow' . $id . '.loadScene("' . $scene_key . '");';
                // $html .= 'touchtime = 0;';
                // $html .= '} else {';
                // $html .= 'touchtime = new Date().getTime();';
                // $html .= '}';
                // $html .= '}';
                // $html .= '});';
                $html .= '
                jQuery(document).on("click","#' . $scene_key_gallery . '",function() {
                    panoshow' . $id . '.loadScene("' . $scene_key . '");
                });
                ';
            }
        }
    }

    //===Custom Control===//
    if (isset($custom_control)) {
        if ($custom_control['panupSwitch'] == "on") {
            $html .= 'document.getElementById("pan-up' . $id . '").addEventListener("click", function(e) {';
            $html .= 'panoshow' . $id . '.setPitch(panoshow' . $id . '.getPitch() + 10);';
            $html .= '});';
        }

        if ($custom_control['panDownSwitch'] == "on") {
            $html .= 'document.getElementById("pan-down' . $id . '").addEventListener("click", function(e) {';
            $html .= 'panoshow' . $id . '.setPitch(panoshow' . $id . '.getPitch() - 10);';
            $html .= '});';
        }

        if ($custom_control['panLeftSwitch'] == "on") {
            $html .= 'document.getElementById("pan-left' . $id . '").addEventListener("click", function(e) {';
            $html .= 'panoshow' . $id . '.setYaw(panoshow' . $id . '.getYaw() - 10);';
            $html .= '});';
        }

        if ($custom_control['panRightSwitch'] == "on") {
            $html .= 'document.getElementById("pan-right' . $id . '").addEventListener("click", function(e) {';
            $html .= 'panoshow' . $id . '.setYaw(panoshow' . $id . '.getYaw() + 10);';
            $html .= '});';
        }

        if ($custom_control['panZoomInSwitch'] == "on") {
            $html .= 'document.getElementById("zoom-in' . $id . '").addEventListener("click", function(e) {';
            $html .= 'panoshow' . $id . '.setHfov(panoshow' . $id . '.getHfov() - 10);';
            $html .= '});';
        }

        if ($custom_control['panZoomOutSwitch'] == "on") {
            $html .= 'document.getElementById("zoom-out' . $id . '").addEventListener("click", function(e) {';
            $html .= 'panoshow' . $id . '.setHfov(panoshow' . $id . '.getHfov() + 10);';
            $html .= '});';
        }

        if ($custom_control['panFullscreenSwitch'] == "on") {
            $html .= 'document.getElementById("fullscreen' . $id . '").addEventListener("click", function(e) {';
            $html .= 'panoshow' . $id . '.toggleFullscreen();';
            $html .= '});';
        }

        if ($custom_control['backToHomeSwitch'] == "on") {
            $html .= 'document.getElementById("backToHome' . $id . '").addEventListener("click", function(e) {';
            $html .= 'panoshow' . $id . '.loadScene("' . $default_scene . '");';
            $html .= '});';
        }

        if ($custom_control['gyroscopeSwitch'] == "on") {
            $html .= 'document.getElementById("gyroscope' . $id . '").addEventListener("click", function(e) {';
            $html .= '
                if (panoshow' . $id . '.isOrientationActive()) {
                  panoshow' . $id . '.stopOrientation();
                }
                else {
                  panoshow' . $id . '.startOrientation();
                }

              ';
            $html .= '});';
        }
    }

    $angle_up = '<i class="fa fa-angle-up"></i>';
    $angle_down = '<i class="fa fa-angle-down"></i>';
    $sin_qout = "'";

    //===Explainer Script===//
    $html .= '
    jQuery(document).on("click","#explainer_button_' . $id . '",function() {
        jQuery("#explainer' . $id . '").slideToggle();
        // jQuery(".explainer").slideToggle();
      });

      jQuery(document).on("click",".close-explainer-video",function() {
        jQuery(this).parent(".explainer").hide();
      });
      jQuery(document).on("click","#pano' . $id . '",function(event) {
        var isActiveModal = event.target.closest(".custom-ifram-wrapper");
        var isForm = event.target.closest(".wpvr-hotspot-tweak-contents");
        if( isActiveModal == null && isForm == null){
             jQuery(".custom-ifram-wrapper").hide();
             jQuery(this).removeClass("show-modal");
             jQuery(".wpvr-hotspot-tweak-contents-wrapper").hide("show-modal");
        }else if(isForm != null){
            jQuery(this).addClass("show-modal");
        }
      });

    ';
    //===Explainer Script End===//

    if ($vrgallery_display) {
        if (!$autoload) {
            $html .= '
          jQuery(document).ready(function($){
              jQuery("#sccontrols' . $id . '").hide();
              jQuery(".vrgctrl' . $id . '").html(' . $sin_qout . $angle_up . $sin_qout . ');
              jQuery("#sccontrols' . $id . '").hide();
              jQuery(".wpvr_slider_nav").hide();
          });
          ';

            $html .= '
            var slide' . $id . ' = "down";
            jQuery(document).on("click","#vrgcontrols' . $id . '",function() {

              if (slide' . $id . ' == "up") {
                jQuery(".vrgctrl' . $id . '").empty();
                jQuery(".vrgctrl' . $id . '").html(' . $sin_qout . $angle_up . $sin_qout . ');
                slide' . $id . ' = "down";
              }
              else {
                jQuery(".vrgctrl' . $id . '").empty();
                jQuery(".vrgctrl' . $id . '").html(' . $sin_qout . $angle_down . $sin_qout . ');
                slide' . $id . ' = "up";
              }
              jQuery(".wpvr_slider_nav").slideToggle();
              jQuery("#sccontrols' . $id . '").slideToggle();
            });
            ';
        } else {
            $html .= '
          jQuery(document).ready(function($){
            jQuery("#sccontrols' . $id . '").show();
            jQuery(".wpvr_slider_nav").show();
              jQuery(".vrgctrl' . $id . '").html(' . $sin_qout . $angle_down . $sin_qout . ');
          });
          ';

            $html .= '
          var slide' . $id . ' = "down";
          jQuery(document).on("click","#vrgcontrols' . $id . '",function() {

            if (slide' . $id . ' == "up") {
              jQuery(".vrgctrl' . $id . '").empty();
              jQuery(".vrgctrl' . $id . '").html(' . $sin_qout . $angle_down . $sin_qout . ');
              slide' . $id . ' = "down";
            }
            else {
              jQuery(".vrgctrl' . $id . '").empty();
              jQuery(".vrgctrl' . $id . '").html(' . $sin_qout . $angle_up . $sin_qout . ');
              slide' . $id . ' = "up";
            }
            jQuery(".wpvr_slider_nav").slideToggle();
            jQuery("#sccontrols' . $id . '").slideToggle();
          });
          ';
        }
    } else {
        $html .= '
          jQuery(document).ready(function($){
            jQuery("#sccontrols' . $id . '").hide();
            jQuery(".wpvr_slider_nav").hide();
              jQuery(".vrgctrl' . $id . '").html(' . $sin_qout . $angle_up . $sin_qout . ');
          });
          ';

        $html .= '
          var slide' . $id . ' = "down";
          jQuery(document).on("click","#vrgcontrols' . $id . '",function() {

            if (slide' . $id . ' == "up") {
              jQuery(".vrgctrl' . $id . '").empty();
              jQuery(".vrgctrl' . $id . '").html(' . $sin_qout . $angle_up . $sin_qout . ');
              slide' . $id . ' = "down";
            }
            else {
              jQuery(".vrgctrl' . $id . '").empty();
              jQuery(".vrgctrl' . $id . '").html(' . $sin_qout . $angle_down . $sin_qout . ');
              slide' . $id . ' = "up";
            }
            jQuery(".wpvr_slider_nav").slideToggle();
            jQuery("#sccontrols' . $id . '").slideToggle();
          });
          ';
    }

    if (!$autoload) {
        $html .= '

          jQuery(document).ready(function(){
                jQuery("#controls' . $id . '").hide();
                jQuery("#zoom-in-out-controls' . $id . '").hide();
                jQuery("#adcontrol' . $id . '").hide();
                jQuery("#pano' . $id . '").find(".pnlm-panorama-info").hide();
          });

          ';

        if ($vrgallery_display) {
            $html .= 'var load_once = "true";';
            $html .= 'panoshow' . $id . '.on("load", function (){
                    if (load_once == "true") {
                      load_once = "false";
                      jQuery("#sccontrols' . $id . '").slideToggle();
                    }
            });';
        }

        $html .= 'panoshow' . $id . '.on("load", function (){
              jQuery("#controls' . $id . '").show();
              jQuery("#zoom-in-out-controls' . $id . '").show();
              jQuery("#adcontrol' . $id . '").show();
              jQuery("#pano' . $id . '").find(".pnlm-panorama-info").show();
          });';
    }
    if (isset($postdata['previewtext'])) {
        $html .= '
        jQuery("#pano' . $id . '").children(".pnlm-ui").find(".pnlm-load-button p").text("' . $postdata['previewtext'] . '")
        ';
    } else {
        $html .= '
        jQuery("#pano' . $id . '").children(".pnlm-ui").find(".pnlm-load-button p").text("Click To Load Panorama")
        ';
    }
    $html .= '});';
    $html .= '</script>';
    //script end
    return $html;
}


function wpvr_hex2rgb($colour)
{
    if (isset($colour[0]) && $colour[0] == '#') {
        $colour = substr($colour, 1);
    }
    if (strlen($colour) == 6) {
        list($r, $g, $b) = array($colour[0] . $colour[1], $colour[2] . $colour[3], $colour[4] . $colour[5]);
    } elseif (strlen($colour) == 3) {
        list($r, $g, $b) = array($colour[0] . $colour[0], $colour[1] . $colour[1], $colour[2] . $colour[2]);
    } else {
        return false;
    }
    $r = hexdec($r);
    $g = hexdec($g);
    $b = hexdec($b);
    return array($r . ', ' . $g . ', ' . $b);
}

function wpvr_HTMLToRGB($htmlCode)
{
    $r = 0;
    $g = 0;
    $b = 0;
    if (isset($htmlCode[0]) && $htmlCode[0] == '#') {
        $htmlCode = substr($htmlCode, 1);
    }

    if (strlen($htmlCode) == 3) {
        $htmlCode = $htmlCode[0] . $htmlCode[0] . $htmlCode[1] . $htmlCode[1] . $htmlCode[2] . $htmlCode[2];
    }

    if (isset($htmlCode[0]) && isset($htmlCode[1])) {
        $r = hexdec($htmlCode[0] . $htmlCode[1]);
    }
    if (isset($htmlCode[2]) && isset($htmlCode[3])) {
        $g = hexdec($htmlCode[2] . $htmlCode[3]);
    }
    if (isset($htmlCode[4]) && isset($htmlCode[5])) {
        $b = hexdec($htmlCode[4] . $htmlCode[5]);
    }

    return $b + ($g << 0x8) + ($r << 0x10);
}

function wpvr_RGBToHSL($RGB)
{
    $r = 0xFF & ($RGB >> 0x10);
    $g = 0xFF & ($RGB >> 0x8);
    $b = 0xFF & $RGB;

    $r = ((float)$r) / 255.0;
    $g = ((float)$g) / 255.0;
    $b = ((float)$b) / 255.0;

    $maxC = max($r, $g, $b);
    $minC = min($r, $g, $b);

    $l = ($maxC + $minC) / 2.0;

    if ($maxC == $minC) {
        $s = 0;
        $h = 0;
    } else {
        if ($l < .5) {
            $s = ($maxC - $minC) / ($maxC + $minC);
        } else {
            $s = ($maxC - $minC) / (2.0 - $maxC - $minC);
        }
        if ($r == $maxC) {
            $h = ($g - $b) / ($maxC - $minC);
        }
        if ($g == $maxC) {
            $h = 2.0 + ($b - $r) / ($maxC - $minC);
        }
        if ($b == $maxC) {
            $h = 4.0 + ($r - $g) / ($maxC - $minC);
        }

        $h = $h / 6.0;
    }

    $h = (int)round(255.0 * $h);
    $s = (int)round(255.0 * $s);
    $l = (int)round(255.0 * $l);

    return (object) array('hue' => $h, 'saturation' => $s, 'lightness' => $l);
}

add_action('rest_api_init', 'wpvr_rest_data_route');
function wpvr_rest_data_route()
{
    register_rest_route('wpvr/v1', '/panodata/', array(
        'methods' => 'GET',
        'callback' => 'wpvr_rest_data_set',
        'permission_callback' => 'wpvr_rest_route_permission'
    ));
}

function wpvr_rest_route_permission()
{
    return true;
}

function wpvr_rest_data_set()
{
    $query = new WP_Query(array(
        'post_type' => 'wpvr_item',
        'posts_per_page' => -1,
    ));

    $wpvr_list = array();
    $list_none = array('value' => 0, 'label' => 'None');
    array_push($wpvr_list, $list_none);
    while ($query->have_posts()) {
        $query->the_post();
        $title = get_the_title();
        $post_id = get_the_ID();
        $title = $title . ' : ' . $post_id;
        $list_ob = array('value' => $post_id, 'label' => $title);
        array_push($wpvr_list, $list_ob);
    }
    return $wpvr_list;
}

function wpvr_isMobileDevice()
{
    return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
}

function wpvr_directory()
{
    $upload = wp_upload_dir();
    $upload_dir = $upload['basedir'];
    $upload_dir_temp = $upload_dir . '/wpvr/temp/';
    if (!is_dir($upload_dir_temp)) {
        wp_mkdir_p($upload_dir_temp, 0700);
    }
}

add_action('admin_init', 'wpvr_directory');


function wpvr_add_role_cap()
{
    $editor_active = get_option('wpvr_editor_active');

    $author_active = get_option('wpvr_author_active');

    $admin = get_role('administrator');
    $admin->add_cap('publish_wpvr_tour');
    $admin->add_cap('edit_wpvr_tours');
    $admin->add_cap('read_wpvr_tour');
    $admin->add_cap('edit_wpvr_tour');
    $admin->add_cap('edit_wpvr_tours');
    $admin->add_cap('publish_wpvr_tours');
    $admin->add_cap('publish_wpvr_tour');
    $admin->add_cap('delete_wpvr_tour');
    $admin->add_cap('edit_other_wpvr_tours');
    $admin->add_cap('delete_other_wpvr_tours');

    if ($editor_active == "true") {
        $editor = get_role('editor');
        if ($editor) {
            $editor->add_cap('publish_wpvr_tour');
            $editor->add_cap('edit_wpvr_tours');
            $editor->add_cap('read_wpvr_tour');
            $editor->add_cap('edit_wpvr_tour');
            $editor->add_cap('edit_wpvr_tours');
            $editor->add_cap('publish_wpvr_tours');
            $editor->add_cap('publish_wpvr_tour');
            $editor->add_cap('delete_wpvr_tour');
            $editor->add_cap('edit_other_wpvr_tours');
            $editor->add_cap('delete_other_wpvr_tours');
        }
    } else {
        $editor = get_role('editor');
        if ($editor) {
            $editor->remove_cap('publish_wpvr_tour');
            $editor->remove_cap('edit_wpvr_tours');
            $editor->remove_cap('read_wpvr_tour');
            $editor->remove_cap('edit_wpvr_tour');
            $editor->remove_cap('edit_wpvr_tours');
            $editor->remove_cap('publish_wpvr_tours');
            $editor->remove_cap('publish_wpvr_tour');
            $editor->remove_cap('delete_wpvr_tour');
            $editor->remove_cap('edit_other_wpvr_tours');
            $editor->remove_cap('delete_other_wpvr_tours');
        }
    }

    if ($author_active == "true") {
        $author = get_role('author');
        if ($author) {
            $author->add_cap('read_wpvr_tour');
            $author->add_cap('edit_wpvr_tour');
            $author->add_cap('edit_wpvr_tours');
            $author->add_cap('publish_wpvr_tours');
            $author->add_cap('publish_wpvr_tour');
            $author->add_cap('delete_wpvr_tour');
        }
    } else {
        $author = get_role('author');
        if ($author) {
            $author->remove_cap('read_wpvr_tour');
            $author->remove_cap('edit_wpvr_tour');
            $author->remove_cap('edit_wpvr_tours');
            $author->remove_cap('publish_wpvr_tours');
            $author->remove_cap('publish_wpvr_tour');
            $author->remove_cap('delete_wpvr_tour');
        }
    }
}

add_action('admin_init', 'wpvr_add_role_cap', 999);

function wpvr_role_management_from_post_type($args, $post_type)
{
    if ('wpvr_item' !== $post_type) {
        return $args;
    }

    $editor_active = get_option('wpvr_editor_active');
    $author_active = get_option('wpvr_author_active');
    $user = wp_get_current_user();

    if ($editor_active == "true") {
        if (in_array('editor', (array) $user->roles)) {
            $args['show_in_menu'] = true;
        }
    }

    if ($author_active == "true") {
        if (in_array('author', (array) $user->roles)) {
            $args['show_in_menu'] = true;
        }
    }

    return $args;
}
add_filter('register_post_type_args', 'wpvr_role_management_from_post_type', 10, 2);

function wpvr_cache_admin_notice()
{
    $option = get_option('wpvr_warning');
    if (!$option) {
?>
        <div class="notice notice-warning" id="wpvr-warning" style="position: relative;">
            <p><?php _e('Since you have updated the plugin, please clear the browser cache for smooth functioning. Follow these steps if you are using <a href="https://support.google.com/accounts/answer/32050?co=GENIE.Platform%3DDesktop&hl=en" target="_blank">Google Chrome</a>, <a href="https://support.mozilla.org/en-US/kb/how-clear-firefox-cache" target="_blank">Mozilla Firefox</a>, <a href="https://clear-my-cache.com/en/apple-mac-os/safari.html" target="_blank">Safai</a> or <a href="https://support.microsoft.com/en-us/help/10607/microsoft-edge-view-delete-browser-history" target="_blank">Microsoft Edge</a>', 'wpvr'); ?></p>
            <button type="button" id="wpvr-dismissible" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
        </div>
<?php
    }
}
// add_action('admin_notices', 'wpvr_cache_admin_notice');

//===Oxygen widget===//
add_action('plugins_loaded', function () {
    if (!class_exists('OxyEl')) {
        return;
    }
    require_once __DIR__ . '/oxygen/oxy-manager.php';
});

add_action('init', 'wpvr_mobile_media_handle');
function wpvr_mobile_media_handle()
{
    add_image_size('wpvr_mobile', 4096, 2048); //mobile
}


add_action(
    /**
     * @param $api \VisualComposer\Modules\Api\Factory
     */
    'vcv:api',
    function ($api) {
        $elementsToRegister = [
            'wpvrelement',
        ];
        $pluginBaseUrl = rtrim(plugins_url(basename(__DIR__)), '\\/');
        /** @var \VisualComposer\Modules\Elements\ApiController $elementsApi */
        $elementsApi = $api->elements;
        foreach ($elementsToRegister as $tag) {
            $manifestPath = __DIR__ . '/vc/' . $tag . '/manifest.json';
            $elementBaseUrl = $pluginBaseUrl . '/vc/' . $tag;
            $elementsApi->add($manifestPath, $elementBaseUrl);
        }
    }
);

function wpvr_redirect_after_activation( $plugin ) {
    if ( $plugin == plugin_basename( __FILE__ ) ) {
        $url = admin_url( 'admin.php?page=wpvr-setup-wizard' );
        $url = esc_url( $url, FILTER_SANITIZE_URL );
        exit( wp_safe_redirect( $url ) );
    }
}
add_action( 'activated_plugin', 'wpvr_redirect_after_activation' );
