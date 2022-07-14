<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
/**
 * Responsible for managing Scene tab on Setup meta box
 *
 * @link       http://rextheme.com/
 * @since      8.0.0
 *
 * @package    Wpvr
 * @subpackage Wpvr/admin/classes
 */

class WPVR_Scene {

    /**
     * Instance of WPVR_Hotspot class
     * 
     * @var object
     * @since 8.0.0
     */
    protected $hotspot;

    /**
     * Instance of WPVR_Format class
     * 
     * @var object
     * @since 8.0.0
     */
    protected $format;

    /**
     * Instance of WPVR_Validator class
     * 
     * @var object
     * @since 8.0.0
     */
    private $validator;

    /**
     * Number of scene or hotspot item
     * 
     * @var integer
     * @since 8.0.0
     */
    protected $data_limit;

    /**
     * Pro version license status
     * 
     * @var string
     * @since 8.0.0
     */
    protected $status;
    

    function __construct()
    {
        $this->hotspot   = new WPVR_Hotspot();
        $this->format    = new WPVR_Format();
        $this->validator = new WPVR_Validator();

        $this->status = apply_filters( 'check_pro_license_status', $this->status );

        if ($this->status !== false && $this->status == 'valid') {
            $this->data_limit = 999999999;
        } else {
            $this->data_limit = 5;
        }
    }


    /**
     * Render Scene Settings Content
     * 
     * @param array $postdata
     * 
     * @return void
     * @since 8.0.0
     */
    public function render_scene($postdata)
    {
      ob_start();
        ?>
        
          <!-- Scene and Hotspot repeater -->
          <div class="scene-setup rex-pano-sub-tabs" data-limit="<?= $this->data_limit + 1;?>">
            <?php $this->render_scene_repeater_list($postdata); ?>
          </div>

        <?php 
      ob_end_flush();
    }


    /**
     * Render scene setup data repeater list
     * 
     * @param array $postdata
     * 
     * @return void
     * @since 8.0.0
     */
    private function render_scene_repeater_list($postdata)
    {
      ob_start();
      ?>
      <nav class="rex-pano-tab-nav rex-pano-nav-menu scene-nav">
        <?php $this->render_nav_menu($postdata); // Will render scene navigation bar ?> 
      </nav>
      
      <div data-repeater-list="scene-list" class="rex-pano-tab-content">

        <!-- Default empty repeater -->
        <div data-repeater-item class="single-scene rex-pano-tab" data-title="0" id="scene-0">
            <?php $this->render_default_repeater_item(); ?>
        </div>
        <!-- Empty repeater end -->

          <?php $s = 1; $firstvalue = reset($postdata['panodata']["scene-list"]);
          foreach ($postdata['panodata']["scene-list"] as $pano_scene) { ?>

          <div data-repeater-item  class="single-scene rex-pano-tab <?php if($pano_scene['scene-id'] == $firstvalue['scene-id']) { echo 'active'; }; ?>" data-title="1" id="scene-<?php echo $s;?>">
              <?php $this->render_repeater_item_with_panodata($pano_scene, $s); ?>
          </div>
      
          <?php $s++; } ?>
      </div>
      <?php 
      ob_end_flush();
    }


    /**
     * Render scene nav menu 
     * 
     * @param array $postdata
     * 
     * @return void
     * @since 8.0.0
     */
    private function render_nav_menu($postdata)
    {
      ob_start();
      ?>
      <ul>
        <?php $i = 1; $firstvalue = reset($postdata['panodata']["scene-list"]);
        foreach ($postdata['panodata']["scene-list"] as $pano_scene) { ?>

          <li class="<?php if ($pano_scene['scene-id'] == $firstvalue['scene-id']) {echo 'active';};?>">
            <span data-index="<?php echo $i;?>" data-href="#scene-<?php echo $i;?>">
              <i class="fa fa-image"></i>
            </span>
          </li>

        <?php $i++; } ?>
        <li class="add" data-repeater-create><span><i class="fa fa-plus-circle"></i></span></li>
      </ul>
      <?php
      ob_end_flush();
    }


    /**
     * Render repeater item for default scene
     * 
     * @param int $data_limit
     * 
     * @return void
     * @since 8.0.0
     */
    private function render_default_repeater_item()
    {
      ob_start();
      ?>
      <div class="active_scene_id"><p></p></div>
      <div class="scene-content">
          <?php $this->render_default_repeater_item_scene_content(); ?>
      </div>

      <!-- hotspot setup -->
      <div class="hotspot-setup rex-pano-sub-tabs" data-limit="<?= $this->data_limit;?>">
          <?php $this->hotspot->render_hotspot($s = 0, $h =1)?>
      </div>
      <button data-repeater-delete type="button" title="Delete Scene" class="delete-scene"><i class="far fa-trash-alt"></i></button>
      <?php
      ob_end_flush();
    }


    /**
     * Render repeater items while scene has panaromic data
     * 
     * @param array $pano_scene
     * @param int $s scene number increment var
     * 
     * @return void
     * @since 8.0.0
     */
    private function render_repeater_item_with_panodata($pano_scene, $s)
    {
      ob_start();
      ?>
      <div class="active_scene_id"><p></p></div>
      <div class="scene-content">
          <!-- 
            - Render repeater item scene content 
            - If scene has panaromic data 
          -->
          <?php $this->render_repeater_scene_content_with_data($pano_scene); ?>
      </div>
      <!-- 
        - Render repeater item hotspot content 
      -->
      <?php $this->render_repeater_item_hotspot_content($pano_scene, $s); ?>
  
      <button data-repeater-delete type="button" title="Delete Scene" class="delete-scene"><i class="far fa-trash-alt"></i></button>
      <?php
      ob_end_flush();
    }


    /**
     * Render scene content for default repeater item
     * 
     * @return void
     * @since 8.0.0
     */
    private function render_default_repeater_item_scene_content()
    {
      ob_start();
      ?>

      <h6 class="title"><i class="fa fa-cog"></i> Scene Setting </h6>

      <div class="scene-left">
          <?php WPVR_Meta_Field::render_scene_left_fields_empty_panodata(); ?>
      </div>

      <div class="scene-right">
          <?php do_action( 'wpvr_pro_scene_empty_right_fields' ) ?>
      </div>

      <?php
      ob_end_flush();
    }


    /**
     * Render repeater item scene content is scene has panaromic data
     * 
     * @param mixed $dscene
     * @param mixed $scene_id
     * @param mixed $scene_photo
     * 
     * @return void
     * @since 8.0.0
     */
    private function render_repeater_scene_content_with_data($pano_scene)
    {
      ob_start();
      ?>
      <h6 class="title"><i class="fa fa-cog"></i> Scene Setting </h6>

      <div class="scene-left">
          <?php WPVR_Meta_Field::render_scene_left_fields_with_panodata($pano_scene) ;?>
      </div>
      
      <div class="scene-right">
          <?php do_action( 'wpvr_pro_scene_right_fields', $pano_scene ) ?>
      </div>

      <?php
      ob_end_flush();
    }


    /**
     * Render repeater item hotspot content
     * 
     * @param array $pano_hotspots
     * @param int $data_limit
     * @param int $s
     * 
     * @return void
     * @since 8.0.0
     */
    private function render_repeater_item_hotspot_content($pano_scene, $s)
    {
      if (!empty($pano_scene['hotspot-list'])) { ?>
        <div class="hotspot-setup rex-pano-sub-tabs" data-limit="<?= $this->data_limit;?>">
  
            <?php $this->hotspot->render_hotspot_with_panodata($pano_scene['hotspot-list'], $s); //Render hotspot while scene has hotspot data ?> 
  
        </div>
        <?php } else { ?>
        <div class="hotspot-setup rex-pano-sub-tabs" data-limit="<?= $this->data_limit;?>">
  
            <?php $this->hotspot->render_hotspot($s, $h = 1); //Render hotspot while scene has no hotspot data ?>
  
        </div>
        <?php }
    }


    /**
     * Update post meta data
     * 
     * @param integer $postid
     * @param integer $panoid
     * 
     * @return void
     * @since 8.0.0
     */
    public function wpvr_update_meta_box($postid, $panoid)
    {
      $panodata      = $this->format->prepare_panodata($_POST['panodata']);
      $default_scene = $this->format->prepare_default_scene($panodata);
      $previewtext = $this->validator->preview_text_validation($_POST['previewtext']);

      $gzoom       = $this->format->set_pro_checkbox_value(@$_POST['gzoom']);
      $default_global_zoom = '';
      $max_global_zoom = '';
      $min_global_zoom = '';
      if ($gzoom == 'on') {
        $default_global_zoom = $_POST['dzoom'];
        $max_global_zoom = $_POST['maxzoom'];
        $min_global_zoom = $_POST['minzoom'];
      }

      $custom_control = isset($_POST['customcontrol']) ? $_POST['customcontrol'] : null;

      $vrgallery          = $this->format->set_checkbox_value(@$_POST['vrgallery']);
      $vrgallery_title    = $this->format->set_checkbox_value(@$_POST['vrgallery_title']);
      $vrgallery_display  = $this->format->set_checkbox_value(@$_POST['vrgallery_display']);

      $mouseZoom    = $this->format->set_pro_checkbox_value(@$_POST['mouseZoom']);
      $draggable    = $this->format->set_pro_checkbox_value(@$_POST['draggable']);
      $diskeyboard  = $this->format->set_pro_checkbox_value(@$_POST['diskeyboard']);
      $keyboardzoom = $this->format->set_checkbox_value(@$_POST['keyboardzoom']);
      $compass      = $this->format->set_checkbox_on_value(@$_POST['compass']);
      //===Gyroscopre control===//
      $gyro = $this->format->set_pro_checkbox_value(@$_POST['gyro']);
      if ($gyro == 'on') {
        if (!is_ssl()) {
          wp_send_json_error('<p><span>Warning:</span> Please add SSL to enable Gyroscope for WP VR. </p>');
          die();
        }
        $gyro = true;
        $deviceorientationcontrol = $this->format->set_checkbox_value(@$_POST['deviceorientationcontrol']);
      } else {
        $gyro = false;
        $deviceorientationcontrol = false;
      }
      //===Gyroscopre control===//

      $autoload           = $this->format->set_checkbox_value($_POST['autoload']);
      $control            = $this->format->set_checkbox_value($_POST['control']);

      $scene_fade_duration = $_POST['scenefadeduration'];
      $preview = esc_url($_POST['preview']);
      $rotation = sanitize_text_field($_POST['rotation']);
      $autorotation = sanitize_text_field($_POST['autorotation']);

      $autorotationinactivedelay = sanitize_text_field($_POST['autorotationinactivedelay']);
      $autorotationstopdelay = sanitize_text_field($_POST['autorotationstopdelay']);
    
      $this->validator->basic_setting_validation($autorotationinactivedelay, $autorotationstopdelay);   // Basic setting error control and validation //

      //===Company Logo===//
      $cpLogoSwitch  = isset($_POST['cpLogoSwitch']) ? $_POST['cpLogoSwitch'] : 'off';
      $cpLogoImg     = isset($_POST['cpLogoImg']) ? $_POST['cpLogoImg'] : '';
      $cpLogoContent = isset($_POST['cpLogoContent']) ? sanitize_text_field($_POST['cpLogoContent']) : '';
      //===Company Logo===//

      //===Explainer video===//
      $explainerSwitch = isset($_POST['explainerSwitch']) ? $_POST['explainerSwitch'] : 'off';
      $explainerContent = '';
      $explainerContent = isset($_POST['explainerContent']) ? $_POST['explainerContent'] : '';
      //===Explainer video===//

      $scene_fade_duration = '';
      $scene_fade_duration = $_POST['scenefadeduration'];
              
      $this->validator->scene_validation($panodata);                                                    // Scene content error control and validation //
    
      $this->validator->empty_scene_validation($panodata);                                              // Empty scene content error control and validation //
    
      $this->validator->duplicate_hotspot_validation($panodata);                                        // Duplicate error control and validation //

      $panodata = $this->format->remove_empty_scene_and_hotspot($panodata);                             // Remove Empty scene and hotspot //

      //===audio===//
      $bg_music          = isset($_POST['bg_music']) ? sanitize_text_field($_POST['bg_music']) : 'off';
      $bg_music_url      = isset($_POST['bg_music_url']) ? esc_url_raw($_POST['bg_music_url']) : '';
      $autoplay_bg_music = isset($_POST['autoplay_bg_music']) ? sanitize_text_field($_POST['autoplay_bg_music']) : 'off';
      $loop_bg_music     = isset($_POST['loop_bg_music']) ? sanitize_text_field($_POST['loop_bg_music']) : 'off';
      if ($bg_music == 'on') {
        if (empty($bg_music_url)) {
          wp_send_json_error('<p><span>Warning:</span> Please add an audio file as you enabled audio for this tour </p>');
          die();
        }
      }
      //===audio===//

      $advanced_control = array(
        'keyboardzoom'              => $keyboardzoom,
        'diskeyboard'               => $diskeyboard,
        'draggable'                 => $draggable, 
        'mouseZoom'                 => $mouseZoom,
        'gyro'                      => $gyro, 
        'deviceorientationcontrol'  => $deviceorientationcontrol, 
        'compass'                   => $compass,
        'vrgallery'                 => $vrgallery, 
        'vrgallery_title'           => $vrgallery_title, 
        'vrgallery_display'         => $vrgallery_display,
        'bg_music'                  => $bg_music, 
        'bg_music_url'              => $bg_music_url, 
        'autoplay_bg_music'         => $autoplay_bg_music, 
        'loop_bg_music'             => $loop_bg_music,
        'cpLogoSwitch'              => $cpLogoSwitch, 
        'cpLogoImg'                 => $cpLogoImg, 
        'cpLogoContent'             => $cpLogoContent, 
        'hfov'                      => $default_global_zoom, 
        'maxHfov'                   => $max_global_zoom, 
        'minHfov'                   => $min_global_zoom,
        'explainerSwitch'           => $explainerSwitch,
        'explainerContent'          => $explainerContent,
      );
      
      $pano_array = array();
      $pano_array = array(
                      __("panoid") => $panoid, 
                      __("autoLoad") => $autoload,  
                      __("showControls") => $control,   
                      __("customcontrol") => $custom_control,  
                      __("autoRotate") => $autorotation, 
                      __("autoRotateInactivityDelay") => $autorotationinactivedelay, 
                      __("autoRotateStopDelay") => $autorotationstopdelay, 
                      __("preview") => $preview, 
                      __("defaultscene") => $default_scene, 
                      __("scenefadeduration") => $scene_fade_duration,  
                      __("panodata") => $panodata, 
                      __("previewtext") => $previewtext);
      $pano_array = apply_filters( 'prepare_scene_pano_array_with_pro_version', $pano_array, $_POST, $advanced_control );
      $pano_array = $this->format->prepare_rotation_wrapper_data($pano_array, $rotation);                 // Prepare tour rotation wrapper data //
       
      update_post_meta($postid, 'panodata', $pano_array);
      die();
    
    }


    /**
     * Responsible for showing Scene Preview
     * 
     * @param string $panoid
     * @param string $panovideo
     * 
     * @return wp_send_json_success
     * @since 8.0.0
     */
    public function wpvr_scene_preview($panoid, $panovideo)
    {
      $panodata     = $this->format->prepare_panodata($_POST['panodata']);

      $control      = $this->format->set_checkbox_value($_POST['control']);
      $autoload     = $this->format->set_checkbox_value($_POST['autoload']);

      $compass      = $this->format->set_checkbox_on_value(@$_POST['compass']);
      $mouseZoom    = $this->format->set_pro_checkbox_value(@$_POST['mouseZoom']);
      $draggable    = $this->format->set_checkbox_value(@$_POST['draggable']);
      $gzoom        = $this->format->set_pro_checkbox_value(@$_POST['gzoom']);
      $diskeyboard  = $this->format->set_checkbox_value(@$_POST['diskeyboard']);
      $keyboardzoom = $this->format->set_checkbox_value(@$_POST['keyboardzoom']);

      $scene_fade_duration       = sanitize_text_field($_POST['scenefadeduration']);
      $preview                   = esc_url($_POST['preview']);

      $default_scene = '';

      $rotation                  = sanitize_text_field($_POST['rotation']);
      $autorotation              = sanitize_text_field($_POST['autorotation']);
      $autorotationinactivedelay = sanitize_text_field($_POST['autorotationinactivedelay']);
      $autorotationstopdelay     = sanitize_text_field($_POST['autorotationstopdelay']);

      $default_global_zoom = '';
      $max_global_zoom     = '';
      $min_global_zoom     = '';
      if ($gzoom == 'on') {
          $default_global_zoom = $_POST['dzoom'];
          $max_global_zoom     = $_POST['maxzoom'];
          $min_global_zoom     = $_POST['minzoom'];
      }
    
      $default_scene = $this->format->prepare_default_scene($panodata);
    
      $this->validator->basic_setting_validation($autorotationinactivedelay, $autorotationstopdelay); // Basic setting error control and validation //
  
      $this->validator->scene_validation($panodata);                                                  // Scene content error control and validation //

      $this->validator->empty_scene_validation($panodata);                                            // Empty scene content error control and validation //
  
      $this->validator->duplicate_hotspot_validation($panodata);
  
      $default_data = array();
      if ($gzoom == 'on') {
          $default_data = array("firstScene" => $default_scene, "sceneFadeDuration" => $scene_fade_duration, "hfov" => $default_global_zoom, "maxHfov" => $max_global_zoom, "minHfov" => $min_global_zoom);
      } else {
          $default_data = array("firstScene" => $default_scene, "sceneFadeDuration" => $scene_fade_duration);
      }

      $scene_data = $this->format->prepare_scene_data_for_preview($panodata);
      
      
      $pano_id_array = array();
      $pano_id_array = array("panoid" => $panoid);
      $pano_response = array();
      $pano_response = array("autoLoad" => $autoload, "defaultZoom" => $default_global_zoom, "minZoom" => $min_global_zoom, "maxZoom" => $max_global_zoom, "showControls" => $control, "compass" => $compass, "mouseZoom" => $mouseZoom, "draggable" => $draggable, "disableKeyboardCtrl" => $diskeyboard, 'keyboardZoom' => $keyboardzoom, "preview" => $preview, "autoRotate" => $autorotation, "autoRotateInactivityDelay" => $autorotationinactivedelay, "autoRotateStopDelay" => $autorotationstopdelay, "default" => $default_data, "scenes" => $scene_data);
      
      $pano_response = $this->format->prepare_rotation_wrapper_data($pano_response, $rotation);
  
      $response = array();
      $response = array($pano_id_array, $pano_response, $panovideo);
      wp_send_json_success($response);
    }


    /**
     * Render shortcode for scene and hotspot post data
     * 
     * @param array $postdata
     * @param string $panoid
     * @param integer $id
     * @param mixed $radius
     * @param mixed $width
     * @param mixed $height
     * 
     * @return string
     * @since 8.0.0
     */
    public function render_scene_shortcode($postdata, $panoid, $id, $radius, $width, $height, $mobile_height)
    {
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
        if (isset($postdata['compass'])) {
            $compass = $postdata['compass'] == 'on' || $postdata['compass'] != null ? true : false;
            if ($compass) {
                $audio_right = "60px";
            }
        }

        //===explainer  handle===//

        $explainer_right = "10px";
        if ((isset($postdata['compass']) && $postdata['compass'] == true) && (isset($postdata['bg_music']) && $postdata['bg_music'] == 'on')) {
            $explainer_right = "90px";
        } elseif (isset($postdata['compass']) && $postdata['compass'] == true) {
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
          $draggable = $postdata['draggable'] == 'off' || $postdata['draggable'] == null ? false : true;
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

        $default_global_zoom = '';
        if (isset($postdata['hfov'])) {
            $default_global_zoom = $postdata['hfov'];
        }

        $max_global_zoom = '';
        if (isset($postdata['maxHfov'])) {
            $max_global_zoom = $postdata['maxHfov'];
        }

        $min_global_zoom = '';
        if (isset($postdata['minHfov'])) {
            $min_global_zoom = $postdata['minHfov'];
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

        $hotspoticoncolor = '#00b4ff';
        $hotspotblink = 'on';
        $default_data = array();
        if ($default_global_zoom != '' && $max_global_zoom != '' && $min_global_zoom != '') {
            $default_data = array("firstScene" => $default_scene, "sceneFadeDuration" => $scene_fade_duration, "hfov" => $default_global_zoom, "maxHfov" => $max_global_zoom, "minHfov" => $min_global_zoom);
        } else {
            $default_data = array("firstScene" => $default_scene, "sceneFadeDuration" => $scene_fade_duration);
        }

        $scene_data = array();

        if (!empty($panodata["scene-list"])) {
            foreach ($panodata["scene-list"] as $panoscenes) {
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
                if (isset($panoscenes["scene-zoom"]) && $panoscenes["scene-zoom"] != "") {
                    $default_zoom = $panoscenes["scene-zoom"];
                } else {
                    if ($default_global_zoom != '') {
                        $default_zoom =  (int)$default_global_zoom;
                    }
                }


                $max_zoom = 120;
                if (isset($panoscenes["scene-maxzoom"]) && $panoscenes["scene-maxzoom"] != '') {
                    $max_zoom = (int)$panoscenes["scene-maxzoom"];
                } else {
                    if ($max_global_zoom != '') {
                        $max_zoom =  (int)$max_global_zoom;
                    }
                }



                $min_zoom = 50;
                if (isset($panoscenes["scene-minzoom"]) && $panoscenes["scene-minzoom"] != '') {
                    $min_zoom = (int)$panoscenes["scene-minzoom"];
                } else {
                    if ($min_global_zoom != '') {
                        $min_zoom =  (int)$min_global_zoom;
                    }
                }


                $hotspot_datas = array();
                if (isset($panoscenes["hotspot-list"])) {
                    $hotspot_datas = $panoscenes["hotspot-list"];
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
                        "text" => $hotspot_data["hotspot-title"],
                        "pitch" => $hotspot_data["hotspot-pitch"],
                        "yaw" => $hotspot_data["hotspot-yaw"],
                        "type" => $hotspot_type,
                        "cssClass" => $hotspot_data["hotspot-customclass"],
                        "URL" => $hotspot_data["hotspot-url"],
                        "wpvr_url_open" => $wpvr_url_open,
                        "clickHandlerArgs" => $hotspot_content,
                        "createTooltipArgs" => $hotspot_data["hotspot-hover"],
                        "sceneId" => $hotspot_data["hotspot-scene"],
                        "targetPitch" => (float)$hotspot_scene_pitch,
                        "targetYaw" => (float)$hotspot_scene_yaw,
                        'hotspot_type' => $hotspot_data['hotspot-type'],
                        'hotspot_target' => 'notBlank'
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
                        if ($image_info[0] > 4096) {
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
                                    if ($desktop_scene[0]) {
                                        $device_scene = $desktop_scene[0];
                                    }
                                }
                            }
                        }
                    }
                }

                $scene_info = array();

                if ($panoscenes["scene-type"] == 'cubemap') {
                    $pano_attachment = array(
                        $panoscenes["scene-attachment-url-face0"],
                        $panoscenes["scene-attachment-url-face1"],
                        $panoscenes["scene-attachment-url-face2"],
                        $panoscenes["scene-attachment-url-face3"],
                        $panoscenes["scene-attachment-url-face4"],
                        $panoscenes["scene-attachment-url-face5"]
                    );

                    $scene_info = array("type" => $panoscenes["scene-type"], "cubeMap" => $pano_attachment, "pitch" => $default_scene_pitch, "maxPitch" => $scene_max_pitch, "minPitch" => $scene_min_pitch, "maxYaw" => $scene_max_yaw, "minYaw" => $scene_min_yaw, "yaw" => $default_scene_yaw, "hfov" => $default_zoom, "maxHfov" => $max_zoom, "minHfov" => $min_zoom, "title" => $scene_ititle, "author" => $scene_author, "authorURL" => $scene_author_url, "vaov" => $scene_vaov, "haov" => $scene_haov, "vOffset" => $scene_vertical_offset, "hotSpots" => $hotspots);
                } else {
                    $scene_info = array("type" => $panoscenes["scene-type"], "panorama" => $device_scene, "pitch" => $default_scene_pitch, "maxPitch" => $scene_max_pitch, "minPitch" => $scene_min_pitch, "maxYaw" => $scene_max_yaw, "minYaw" => $scene_min_yaw, "yaw" => $default_scene_yaw, "hfov" => $default_zoom, "maxHfov" => $max_zoom, "minHfov" => $min_zoom, "title" => $scene_ititle, "author" => $scene_author, "authorURL" => $scene_author_url, "vaov" => $scene_vaov, "haov" => $scene_haov, "vOffset" => $scene_vertical_offset, "hotSpots" => $hotspots);
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
                    $panoscenes["scene-id"] => $scene_info
                );
                $scene_data[$panoscenes["scene-id"]] = $scene_info;
            }
        }

        $pano_id_array = array();
        $pano_id_array = array("panoid" => $panoid);
        $pano_response = array();
        $pano_response = array("autoLoad" => $autoload, "showControls" => $control, "orientationSupport" => 'false', "compass" => $compass, 'orientationOnByDefault' => $gyro_orientation, "mouseZoom" => $mouseZoom, "draggable" => $draggable, 'disableKeyboardCtrl' => $diskeyboard, 'keyboardZoom' => $keyboardzoom, "preview" => $preview, "autoRotate" => $autorotation, "autoRotateInactivityDelay" => $autorotationinactivedelay, "autoRotateStopDelay" => $autorotationstopdelay, "default" => $default_data, "scenes" => $scene_data);
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
            $width = '600px';
        }
        if (empty($height)) {
            $height = '400px';
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
                if ($radius) {
                    $html .= '<div id="pano' . $id . '" class="pano-wrap" style="text-align:center; border-radius:' . $radius . '; direction:ltr;">';
                } else {
                    $html .= '<div id="pano' . $id . '" class="pano-wrap" style="text-align:center;">';
                }
            } else {
                if ($radius) {
                    $html .= '<div id="pano' . $id . '" class="pano-wrap vrfullwidth" style=" text-align:center; height: ' . $height . '; border-radius:' . $radius . '; direction:ltr;" >';
                } else {
                    $html .= '<div id="pano' . $id . '" class="pano-wrap vrfullwidth" style=" text-align:center; height: ' . $height . '; direction:ltr;" >';
                }
            }
        } elseif ($width == 'embed') {
            $html .= '<div id="pano' . $id . '" class="pano-wrap vrembed" style=" text-align:center; direction:ltr;" >';
        } else {
            // $browser_width = "<script>document.write(screen.width);</script>";

            if (wpvr_isMobileDevice()) {
                if ($radius) {
                    $html .= '<div id="pano' . $id . '" class="pano-wrap" style=" text-align:center; max-width:100%; width: ' . $width . '; height: ' . $mobile_height . '!important; margin: 0 auto; border-radius:' . $radius . '; direction:ltr;">';
                } else {
                    $html .= '<div id="pano' . $id . '" class="pano-wrap" style=" text-align:center; max-width:100%; width: ' . $width . '; height: ' . $mobile_height . '!important; margin: 0 auto; direction:ltr;">';
                }
            } else {
                if ($radius) {
                    $html .= '<div id="pano' . $id . '" class="pano-wrap" style=" text-align:center; max-width:100%; width: ' . $width . '; height: ' . $height . '; margin: 0 auto; border-radius:' . $radius . '; direction:ltr;">';
                } else {
                    $html .= '<div id="pano' . $id . '" class="pano-wrap" style=" text-align:center; max-width:100%; width: ' . $width . '; height: ' . $height . '; margin: 0 auto; direction:ltr;">';
                }
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
                    if (!empty($menuLocations['primary'])) {
                        $menuID = $menuLocations['primary'];
                        $primaryNav = wp_get_nav_menu_items($menuID);
                        $html .= '<ul class="wpvr-navbar-container">';
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
                        $html .= '</ul>';
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
                    $html .= '<div class="ctrl" id="gyroscope' . $id . '" ><i class="' . $custom_control['gyroscopeIcon'] . '" id="' . $custom_control['gyroscopeIcon'] . '" style="color:' . $custom_control['gyroscopeColor'] . ';"></i></div>';
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
            $explainerControlSwitch = '';
            if (isset($custom_control['explainerSwitch'])) {
                $explainerControlSwitch = $custom_control['explainerSwitch'];
            }
            if ($explainerControlSwitch == "on") {
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
        $autoplay_bg_music = isset($postdata['bg_music']) ? $postdata['bg_music'] : "off";
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
                $html .= '<audio id="vrAudio' . $id . '" class="vrAudioDefault" data-autoplay="' . $autoplay_bg_music . '" onended="audionEnd' . $id . '()" ' . $bg_loop . '>
                                <source src="' . $bg_music_url . '" type="audio/mpeg">
                                Your browser does not support the audio element.
                            </audio>
                            <button onclick="playPause' . $id . '()" class="ctrl audio_control" data-play="' . $autoplay_bg_music . '" data-play="' . $autoplay_bg_music . '" id="audio_control' . $id . '"><i id="vr-volume' . $id . '" class="wpvrvolumeicon' . $id . ' fas fa-volume-up" style="color:#fff;"></i></button>
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
										x' . $id . '.play();
                    jQuery("#audio_control' . $id . '").attr("data-play", "on");
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
            setTimeout(() => {
                window.dispatchEvent(new Event("resize"));
            }, 200);
						if (jQuery("#pano' . $id . '").children().children(".pnlm-panorama-info:visible").length > 0) {
	               jQuery("#controls' . $id . '").css("bottom", "55px");
	           }
	           else {
	             jQuery("#controls' . $id . '").css("bottom", "5px");
	           }
					});';

        $html .= 'panoshow' . $id . '.on("render", function (){
              window.dispatchEvent(new Event("resize"));
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
                    // $html .= 'document.getElementById("'.$scene_key_gallery.'").addEventListener("click", function(e) { ';
                    // $html .= 'if (touchtime == 0) {';
                    // $html .= 'touchtime = new Date().getTime();';
                    // $html .= '} else {';
                    // $html .= 'if (((new Date().getTime()) - touchtime) < 800) {';
                    // $html .= 'panoshow'.$id.'.loadScene("'.$scene_key.'");';
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
                    document.getElementById("' . $custom_control['gyroscopeIcon'] . '").style.color = "red";
                }
                else {
                    panoshow' . $id . '.startOrientation();
                    document.getElementById("' . $custom_control['gyroscopeIcon'] . '").style.color = "' . $custom_control['gyroscopeColor'] . '";
                }

              ';
                $html .= '});';
            }
        }

        $angle_up = '<i class="fa fa-angle-up"></i>';
        $angle_down = '<i class="fa fa-angle-down"></i>';
        $sin_qout = "'";

        //===Explainer Script===//

        if ($autoplay_bg_music == 'on') {

            $html .= '
            jQuery(document).on("click","#explainer_button_' . $id . '",function() {
                jQuery("#explainer' . $id . '").slideToggle();
    
                playing' . $id . ' = false;
                var x' . $id . ' = document.getElementById("vrAudio' . $id . '");
                jQuery("#vr-volume' . $id . '").removeClass("fas fa-volume-up");
                jQuery("#vr-volume' . $id . '").addClass("fas fa-volume-mute");
                x' . $id . '.pause();
            });
    
            jQuery(document).on("click",".close-explainer-video",function() {
                jQuery(this).parent(".explainer").hide();
                var el_src = jQuery(".vr-iframe").attr("src");
                jQuery(".vr-iframe").attr("src", el_src);
              });
    
            ';
        } else {
            $html .= '
            jQuery(document).on("click","#explainer_button_' . $id . '",function() {
                jQuery("#explainer' . $id . '").slideToggle();
            });
    
            jQuery(document).on("click",".close-explainer-video",function() {
                jQuery(this).parent(".explainer").hide();
                var el_src = jQuery(".vr-iframe").attr("src");
                jQuery(".vr-iframe").attr("src", el_src);
              });
    
            ';
        }

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
                    jQuery(".vrgctrl' . $id . '").html(' . $sin_qout . $angle_down . $sin_qout . ');
                    jQuery(".wpvr_slider_nav").show();
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

        //==Old code working properly==//

        $previeword = "Click to Load Panorama";
        if (isset($postdata['previewtext']) && $postdata['previewtext'] != '') {
            $previeword = $postdata['previewtext'];
        }
        $html .= '
            jQuery(".elementor-tab-title").click(function(){
                      var element_id;
                      var pano_id;
                      var element_id = this.id;
                      element_id = element_id.split("-");
                      element_id = element_id[3];
                      jQuery("#elementor-tab-content-"+element_id).children("div").addClass("awwww");
                      var pano_id = jQuery(".awwww").attr("id");
                      jQuery("#elementor-tab-content-"+element_id).children("div").removeClass("awwww");
                      if (pano_id != undefined) {
                        pano_id = pano_id.split("o");
                        pano_id = pano_id[1];
                        if (pano_id == "' . $id . '") {
                          jQuery("#pano' . $id . '").children(".pnlm-render-container").remove();
                          jQuery("#pano' . $id . '").children(".pnlm-ui").remove();
                          panoshow' . $id . ' = pannellum.viewer(response[0]["panoid"], scenes);
                          jQuery("#pano' . $id . '").children(".pnlm-ui").find(".pnlm-load-button p").text("' . $previeword . '")
                          setTimeout(function() {
                                //   panoshow' . $id . '.loadScene("' . $default_scene . '");
                                  window.dispatchEvent(new Event("resize"));
                                  if (jQuery("#pano' . $id . '").children().children(".pnlm-panorama-info:visible").length > 0) {
                                       jQuery("#controls' . $id . '").css("bottom", "55px");
                                   }
                                   else {
                                     jQuery("#controls' . $id . '").css("bottom", "5px");
                                   }
                                   
                          }, 200);
                        }
                      }
            });
        ';
        $html .= '
            jQuery(".geodir-tab-head dd, #vr-tour-tab").click(function(){
              jQuery("#pano' . $id . '").children(".pnlm-render-container").remove();
              jQuery("#pano' . $id . '").children(".pnlm-ui").remove();
              panoshow' . $id . ' = pannellum.viewer(response[0]["panoid"], scenes);
              setTimeout(function() {
                      panoshow' . $id . '.loadScene("' . $default_scene . '");
                      window.dispatchEvent(new Event("resize"));
                      if (jQuery("#pano' . $id . '").children().children(".pnlm-panorama-info:visible").length > 0) {
                           jQuery("#controls' . $id . '").css("bottom", "55px");
                       }
                       else {
                         jQuery("#controls' . $id . '").css("bottom", "5px");
                       }
              }, 200);
            });
        ';
        if (isset($postdata['previewtext']) && $postdata['previewtext'] != '') {
            $html .= '
            jQuery("#pano' . $id . '").children(".pnlm-ui").find(".pnlm-load-button p").text("' . $postdata['previewtext'] . '")
            ';
        }

        if ($default_global_zoom != '' || $max_global_zoom != '' || $min_global_zoom != '') {
            $html .= '
            jQuery(".globalzoom").val("on").change();
            ';
        }


        $html .= '});';
        $html .= '</script>';
        //script end

        return $html;
    }
}