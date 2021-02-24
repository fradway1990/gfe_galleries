<?php
/*
  Plugin Name: GFE Galleries
  Plugin URI: https://gfedesign.com
  Description: Galleries for posts
  Author: GFE DESIGN
  Author URI: gfedesign.com
  Version:1.0
*/
defined( 'ABSPATH' ) or die( '' );
if(!function_exists('var_error_log')){
  function var_error_log( $object=null ){
      ob_start();                    // start buffer capture
      var_dump( $object );           // dump the values
      $contents = ob_get_contents(); // put the buffer into a variable
      ob_end_clean();                // end capture
      error_log( $contents );        // log contents of the result of var_dump( $object )
  }
}
class GFE_Galleries{
  public function __construct(){
    add_action('admin_enqueue_scripts',array($this,'gfe_gallery_scripts'));
    add_action( 'add_meta_boxes', array($this,'add_gallery_metabox'));
    add_action( 'save_post',array($this,'save_gallery'),10,2);
  }
  function gfe_gallery_scripts(){
    wp_enqueue_style('gfe-gallery',plugin_dir_url( __FILE__ ).'assets/gallery.css',array(),'1.0.0','all');
    wp_enqueue_media();
    wp_enqueue_script('jquery-ui','https://code.jquery.com/ui/1.12.1/jquery-ui.min.js',array(),'',false);
    wp_enqueue_script('gfe-gallery-script',plugin_dir_url( __FILE__ ).'assets/gallery.js',array(),'1.0.0',true);

  }
  public function gallery_metabox_html($post){
    //import wp media script
    $gfe_gallery_ids = get_post_meta($post->ID,'gfe_gallery',true);
    if(!is_array($gfe_gallery_ids)){
      $gfe_gallery_ids = array();
    }
    ?>
    <div class='gallery-images-holder'>
        <?php
        $count = 0;
        foreach($gfe_gallery_ids as $image_id):
          $thumb = wp_get_attachment_image_src($image_id)[0];
          ?>
          <div media_id='<?php echo $image_id;?>'style='background-image:url(<?php echo $thumb;?>)'>
          </div>
          <div class='gallery-thumb-holder gfe-image' style='background-image:url(<?php echo $thumb;?>)'>
            <div class='gfe-delete-image'>&#x2716;</div>
            <input value='<?php echo $image_id;?>' type='hidden' name='gfe_gallery[<?php echo $image_id?>]' class='gfe-order'>
          </div>
        <?php
          $count++;
        endforeach;?>
      <div class='gallery-thumb-holder add-image' style=''>
        <span class='add-symbol'>&#43;</span>
      </div>
      <?php wp_nonce_field( 'gfe_gallery', 'gfe_gallery_nonce' );?>
    </div>

    <?php
  }

  public function add_gallery_metabox(){
   $post_types = get_post_types();
     add_meta_box('gfe_gallery','GFE Gallery',
     array($this,'gallery_metabox_html'),
     $post_types,'side','high'
     );
   }

   public function save_gallery($post_id, $post ){
     if ( !isset( $_POST['gfe_gallery_nonce'] ) || !wp_verify_nonce( $_POST['gfe_gallery_nonce'], 'gfe_gallery') ){
       return $post_id;
     }
     $gallery_ids = array();
     if(isset($_POST['gfe_gallery'])){
       $gfe_gallery = $_POST['gfe_gallery'];
       foreach($gfe_gallery as $key=>$value){
         $gallery_ids[] = $value;
       }
     }

     update_post_meta($post_id,'gfe_gallery',$gallery_ids);
   }
}
new GFE_Galleries;
