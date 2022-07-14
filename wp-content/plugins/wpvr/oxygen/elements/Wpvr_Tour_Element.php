<?php

class Wpvr_Tour_Element extends WPVR_CUSTOM_OXY_ELEMENT
{

    public function name()
    {
        return 'WP VR Tour';
    }

    public function controls()
    {
        /*
         * Adds a control to the element or a section (depends on the caller)
         */

         $posts = get_posts([
            'post_type' => 'wpvr_item',
            'post_status' => 'publish',
            'orderby' => 'date',
            'order'   => 'DESC',
            'numberposts' => -1,
          ]);

          $array = array();
          $array[0] = "None";
          foreach ($posts as $post) {
             $id = $post->ID;
             $title = $post->post_title.' : '.$id;
             if (!$post->post_title) {
               $title = "No title : ".$id;
             }
             $array[$id] = $title;
          }

          $this->addOptionControl([
             "type" => "dropdown",
             "name" => "Tour ID",
             "slug" => "tour_id",
             "value" 		=> $array
         ]);

         $this->addOptionControl([
             "type" => "textfield",
             "name" => "Height",
             "slug" => "tour_height",
             "value" => ""
         ]);

         $this->addOptionControl([
             "type" => "textfield",
             "name" => "Width",
             "slug" => "tour_width",
             "value" => ""
         ]);

         $this->addOptionControl([
             "type" => "textfield",
             "name" => "Radius",
             "slug" => "tour_radius",
             "value" => ""
         ]);

    }

    /*
     * @param {array} $options   values you set in the controls
     * @param {array} $defaults  default values for all controls
     * @param {array} $content   shortcode that holds all nested elements (more on this later)
     */
    public function render($options, $defaults, $content)
    {

        $id = 0;
        $width = "600px";
        $height = "400px";
        $radius = "0px";
        $id = $options['tour_id'];
        $width = $options['tour_width'];
        $height = $options['tour_height'];
        $radius = $options['tour_radius'];
        if (empty($width)) {
            $width = "600px";
        }
        if (empty($height)) {
            $height = "400px";
        }
        if (empty($radius)) {
            $radius = "0px";
        }

        if ($id) {
            $shortcode = do_shortcode( shortcode_unautop( '[wpvr id="'.$id.'" width="'.$width.'" height="'.$height.'" radius="'.$radius.'"]'  ) );
            echo $shortcode;
        }
    }
}

new Wpvr_Tour_Element();
