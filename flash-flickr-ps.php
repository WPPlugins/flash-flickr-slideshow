<?php
/*
Plugin Name: Flash Flickr Slideshow
Plugin URI: http://blog.sephiroth.it
Description: Display a flash flickr slideshow widget from user's photostream. You will need a valid <a href="http://www.flickr.com/services/apps/create/apply">Flickr API</a> key
Version: 0.4
Author: Alessandro Crugnola
Author URI: http://www.sephiroth.it
*/

global $flash_flickr_slideshow_id;

class FlashFlickrPS {
	var $version = "0.4";
	
	function FlashFlickrPS(){
		if( is_admin() ){
			add_option('flash_flickr_slideshow_embed_swfobject', '1' );
			add_option('flash_flickr_slideshow_embed_swfobject_source', '1' );
			add_action('admin_menu', array(&$this, 'options_menu'));
		}
		
		// embed swfobject.js
		if ( get_option('flash_flickr_slideshow_embed_swfobject') == '1') {
			wp_deregister_script('swfobject');
			if ( get_option('flash_flickr_slideshow_embed_swfobject_source') == '0' ) {
				wp_register_script( 'swfobject', 'http' . (is_ssl() ? 's' : '') . '://ajax.googleapis.com/ajax/libs/swfobject/2.2/swfobject.js', array(), '2.2' );
			} else {
				wp_register_script( 'swfobject', plugins_url('/flash-flickr-slideshow/media/swfobject.js'), array(), '2.2' );
			}
			wp_enqueue_script('swfobject');
		}
	}
	
	// Set up the Plugin Options Page
	function options_menu(){
		add_options_page('Flash Flickrs Slideshow', 'Flash Flickr Slideshow', 8, __FILE__, array(&$this, 'settings_page'));
	}
	
		// Render the settings page
		function settings_page() {

			$message = null;
			$message_updated = "Flash Flickr Slideshow Options Updated";

			// update options
			if ($_POST['action'] && $_POST['action'] == 'flash_flickr_slideshow_update') {

				$reference_swfobject 	= ($_POST['reference_swfobject'] == '0') ? $_POST['reference_swfobject'] : '1';
				$swfobject_source		= ($_POST['swfobject_source'] == '1') ? $_POST['swfobject_source'] : '0';

				$message = $message_updated;
				update_option('flash_flickr_slideshow_embed_swfobject', $reference_swfobject);
				update_option('flash_flickr_slideshow_embed_swfobject_source', $swfobject_source);

				if (function_exists('wp_cache_flush')) {
					wp_cache_flush();
				}

			}

		?>

	<?php if ($message) : ?>
	<div id="message" class="updated fade"><p><?php echo $message; ?></p></div>
	<?php endif; ?>

	<style type="text/css" media="screen">
		h3 {
			background: #ddd;
			padding: 8px;
			margin: 2em 0 0;
			border-top: 1px solid #fff;
			border-bottom: 1px solid #aaa;
		}
		h2 + h3 {
			margin-top: 1em;
		}
		table.form-table {
			border-collapse: fixed;
		}
		table.form-table th[colspan] {
			background: #eee;
			border-top: 1px solid #fff;
			border-bottom: 1px solid #ccc;
			margin-top: 1em;
		}
		table.form-table th h4 {
			margin: 3px 0;
		}
		table.form-table th, 
		table.form-table td {
			padding: 5px 8px;
		}
	</style>

	<form action="" method="post" accept-charset="utf-8">
		<div class="wrap">
			<h2><?php _e("Flash Flickrs Slideshow Preferences"); ?></h2>

			<h3><?php _e("Javascript Options"); ?></h3> 

			<table class="form-table">
				<tr>
					<th scope="row" style="vertical-align:top;"><?php _e("Include the SWFObject.js?"); ?></th>
					<td>
						<input type="radio" id="reference_swfobject-0" name="reference_swfobject" value="0" class="radio" <?php if (!get_option('flash_flickr_slideshow_embed_swfobject')) echo "checked=\"checked\""; ?> /><label for="reference_swfobject-0"><?php _e("No"); ?></label>
						<input type="radio" id="reference_swfobject-1" name="reference_swfobject" value="1" class="radio" <?php if (get_option('flash_flickr_slideshow_embed_swfobject')) echo "checked=\"checked\""; ?> /><label for="reference_swfobject-1"><?php _e("Yes"); ?></label>
					</td>
				</tr>
				<tr>
					<th scope="row" style="vertical-align:top;"><?php _e("Source of the SWFObject.js?"); ?></th>
					<td>
						<input type="radio" id="swfobject_source-0" name="swfobject_source" value="0" class="radio" <?php if (!get_option('flash_flickr_slideshow_embed_swfobject_source')) echo "checked=\"checked\""; ?> /><label for="swfobject_source-0"><?php _e("Google Ajax Library"); ?></label>
						<input type="radio" id="swfobject_source-1" name="swfobject_source" value="1" class="radio" <?php if (get_option('flash_flickr_slideshow_embed_swfobject_source')) echo "checked=\"checked\""; ?> /><label for="swfobject_source-1"><?php _e("Internal"); ?></label>
					</td>
				</tr>
			</table>
			<p class="submit">
				<input type="hidden" name="action" value="flash_flickr_slideshow_update" /> 
				<input type="submit" name="Submit" value="<?php _e("Update Options"); ?> &raquo;" /> 
			</p>

		</div>

	</form>
		<?php

		}
	
}

class FlashFlickrPS_Widget extends WP_Widget {

    function FlashFlickrPS_Widget() 
	{
        $widget_ops = array('classname' => 'widget_flashflickrps', 'description' => 'Display a Flash Flickr slideshow of an user photostream');
        $this->WP_Widget('flashflickrps', 'Flash Flickr Photostream', $widget_ops);
    }
 
    function widget($args, $instance) {
        extract($args);
        
		echo $before_widget.$before_title.$instance['title'].$after_title;

		$flash_flickr_slideshow_id += 1;
        ?>

		<div>
			<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="<?php echo $instance['widget_width']; ?>" height="<?php echo $instance['widget_height']; ?>" id="flashcontent-<?php echo $this->id; ?>">
				<param name="movie" value="<?php echo get_plugin_url(); ?>/media/flickr_widget.swf" />
				<param name="flashvars" value="username=<?php echo $instance['username']; ?>&api_key=<?php echo $instance['api_key']; ?>&timer=<?php echo $instance['slideshow_timer']; ?>&image_type=<?php echo $instance['image_type']; ?>&use_frame=<?php echo $instance['useframe']; ?>&frame_color=<?php echo $instance['frame_color']; ?>&use_shadow=<?php echo $instance['useshadow']; ?>&frame_size=<?php echo $instance['frame_size']; ?>">
				<param name="wmode" value="transparent">
				<!--[if !IE]>-->
				<object type="application/x-shockwave-flash" data="<?php echo get_plugin_url(); ?>/media/flickr_widget.swf" width="<?php echo $instance['widget_width']; ?>" height="<?php echo $instance['widget_height']; ?>" flashvars="username=<?php echo $instance['username']; ?>&api_key=<?php echo $instance['api_key']; ?>&timer=<?php echo $instance['slideshow_timer']; ?>&image_type=<?php echo $instance['image_type']; ?>&use_frame=<?php echo $instance['useframe']; ?>&frame_color=<?php echo $instance['frame_color']; ?>&use_shadow=<?php echo $instance['useshadow']; ?>&frame_size=<?php echo $instance['frame_size']; ?>" wmode="transparent">
					<!--<![endif]-->
					<a href="http://www.adobe.com/go/getflashplayer">
						<img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash player" />
					</a>
				<!--[if !IE]>-->
				</object>
				<!--<![endif]-->
			</object>
		</div>

		<?php
        echo $after_widget;
    }
 
    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        
        $instance['title'] = (empty($new_instance['title'])) ? "Flickr" : strip_tags($new_instance['title']);
        $instance['username'] = (empty($new_instance['username'])) ? "" : strip_tags($new_instance['username']);
        $instance['slideshow_timer'] = ((int)$new_instance['slideshow_timer'] > 0) ? (int)$new_instance['slideshow_timer'] : 2000;
		$instance['api_key'] = (empty($new_instance['api_key'])) ? "" : strip_tags($new_instance['api_key']);
		$instance['image_type'] = (empty($new_instance['image_type'])) ? "s" : strip_tags($new_instance['image_type']);
		$instance['useframe'] = (isset($new_instance['useframe'])) ? 1 : 0;
		$instance['useshadow'] = (isset($new_instance['useshadow'])) ? 1 : 0;
		$instance['widget_height'] = (isset($new_instance['widget_height'])) ? strip_tags($new_instance['widget_height']) : "100%";
		$instance['widget_width'] = (isset($new_instance['widget_width'])) ? strip_tags($new_instance['widget_width']) : "100%";
		$instance['frame_color'] = (empty($new_instance['frame_color'])) ? "0xFFFFFF" : strip_tags($new_instance['frame_color']);
		$instance['frame_size'] = (isset($new_instance['frame_size'])) ? strip_tags($new_instance['frame_size']) : "5";
        return $instance;
    }

    function form($instance) {
        $instance = wp_parse_args( (array) $instance, array( 
				'username' => '',
				'order' => '', 
				'slideshow_timer' => 5000, 
				'ct' => '', 
				"title" => "Flickr", 
				"username" => "", 
				"useframe" => true,
				"useshadow" => true,
				"frame_color" => "0xFFFFFF",
				"image_type" => "s",
				"widget_height" => "100%",
				"widget_width" => "100%",
				"frame_size" => "5",
				"api_key" => "" ) );

        $title = (empty($instance['title'])) ? "Title" : strip_tags($instance['title']);
        $username = (empty($instance['username'])) ? "acrugnola" : strip_tags($instance['username']);
        $timer = ((int)$instance['slideshow_timer'] > 0) ? (int)$instance['slideshow_timer'] : 2000;
		$api_key = (empty($instance['api_key'])) ? "ac2255e9ab52edad6a5cfbca20b71486" : strip_tags($instance['api_key']);
		$image_type = (empty($instance['image_type'])) ? "s" : $instance['image_type'];
		$use_frame = isset($instance['useframe']) ? $instance['useframe'] : false;
		$use_shadow = isset($instance['useshadow']) ? $instance['useshadow'] : false;
		$frame_color = (empty($instance['frame_color'])) ? "0xFFFFFF" : strip_tags($instance['frame_color']);
		$widget_height = isset($instance['widget_height']) ? $instance['widget_height'] : "100%";
		$widget_width = isset($instance['widget_width']) ? $instance['widget_width'] : "100%";
		$frame_size = isset($instance['frame_size']) ? $instance['frame_size'] : "5";		
		
        ?>
        <p><?php echo $this->errors ?></p>
        
		<!-- title -->
		<p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </p>

		<!-- username -->
		<p>
            <label for="<?php echo $this->get_field_id('username'); ?>"><?php _e('Username:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('username'); ?>" name="<?php echo $this->get_field_name('username'); ?>" type="text" value="<?php echo $username; ?>" />
        </p>

		<!-- slideshow timer -->
        <p>
            <label for="<?php echo $this->get_field_id('slideshow_timer'); ?>"><?php _e("Slideshow Timer: "); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('slideshow_timer'); ?>" name="<?php echo $this->get_field_name('slideshow_timer'); ?>" type="text" value="<?php echo $timer; ?>" />
        </p>

		<!-- widget width -->
        <p>
            <label for="<?php echo $this->get_field_id('widget_width'); ?>"><?php _e("Flash width: "); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('widget_width'); ?>" name="<?php echo $this->get_field_name('widget_width'); ?>" type="text" value="<?php echo $widget_width; ?>" />
        </p>

		<!-- widget height -->
        <p>
            <label for="<?php echo $this->get_field_id('widget_height'); ?>"><?php _e("Flash height: "); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('widget_height'); ?>" name="<?php echo $this->get_field_name('widget_height'); ?>" type="text" value="<?php echo $widget_height; ?>" />
        </p>

		<!-- frame size -->
        <p>
            <label for="<?php echo $this->get_field_id('frame_size'); ?>"><?php _e("Frame size: "); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('frame_size'); ?>" name="<?php echo $this->get_field_name('frame_size'); ?>" type="text" value="<?php echo $frame_size; ?>" />
        </p>

		<!-- api key -->
		<p>
            <label for="<?php echo $this->get_field_id('api_key'); ?>"><?php _e("Api Key: "); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('api_key'); ?>" name="<?php echo $this->get_field_name('api_key'); ?>" type="text" value="<?php echo $api_key; ?>" />
			<br />
	        <label for="<?php echo $this->get_field_id('frame_color'); ?>"><?php _e("Frame Color: "); ?></label>
       		<input class="widefat" id="<?php echo $this->get_field_id('frame_color'); ?>" name="<?php echo $this->get_field_name('frame_color'); ?>" type="text" value="<?php echo $frame_color; ?>" />
        </p>

		<p>
			<label for="<?php echo $this->get_field_id('image_type'); ?>"><?php _e("Image type: "); ?>
				<select id="<?php echo $this->get_field_id( 'image_type' ); ?>" name="<?php echo $this->get_field_name( 'image_type' ); ?>" class="widefat" style="width:100%;">
					<option value="s" <?php if ( 's' == $instance['image_type'] ) echo 'selected="selected"'; ?>><?php _e("Small square"); ?></option>
					<option value="t" <?php if ( 't' == $instance['image_type'] ) echo 'selected="selected"'; ?>><?php _e("Thumbnail"); ?></option>
					<option value="m" <?php if ( 'm' == $instance['image_type'] ) echo 'selected="selected"'; ?>><?php _e("Small"); ?></option>
					<option value="-" <?php if ( '-' == $instance['image_type'] ) echo 'selected="selected"'; ?>><?php _e("Medium"); ?></option>
					<option value="b" <?php if ( 'b' == $instance['image_type'] ) echo 'selected="selected"'; ?>><?php _e("Large"); ?></option>
				</select>
				
			</label>
		</p>


		<p>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('useframe');?>" name="<?php echo $this->get_field_name('useframe');?>" <?php checked( $instance['useframe'], true ); ?> />
            <label for="<?php echo $this->get_field_id('useframe'); ?>"><?php _e("Use Frame"); ?></label>
			<br />
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('useshadow');?>" name="<?php echo $this->get_field_name('useshadow');?>" <?php checked( $instance['useshadow'], true ); ?> />
            <label for="<?php echo $this->get_field_id('useshadow'); ?>"><?php _e("Use Shadow"); ?></label>
			<br />
        </p>

    <?
    }
}


add_action( (preg_match("/(\/\?feed=|\/feed)/i",$_SERVER['REQUEST_URI'])) ? 'template_redirect' : 'plugins_loaded', 'FlashFlickrSlideshowEmbed' );

function FlashFlickrSlideshowEmbed() {
	global $FlashFlickrSlideshowEmbed;
	$FlashFlickrSlideshowEmbed = new FlashFlickrPS();
}


function get_content_dir()
{
  if ( !defined('WP_CONTENT_DIR') )
    define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
  return WP_CONTENT_DIR;
}

function get_plugin_dir()
{
  return get_content_dir().'/plugins/'.plugin_basename(dirname(__FILE__));
}

function get_plugin_url()
{
  if ( !defined('WP_CONTENT_URL') )
    define( 'WP_CONTENT_URL', get_option('siteurl') . '/wp-content');
  return WP_CONTENT_URL.'/plugins/'.plugin_basename(dirname(__FILE__));
}

function reg_flash_flickr_ps()
{
    register_widget('FlashFlickrPS_Widget');
}

function register_object(){
	global $wp_registered_widgets;
	
	echo "<script type=\"text/javascript\">";
	
	while (list(, $value) = each($wp_registered_widgets)) {
	    if( $value['name'] == 'Flash Flickr Photostream' ){
			echo "swfobject.registerObject(\"flashcontent-" . $value['id'] . "\", \"10.0.0\", \"" . get_plugin_url() . "/media/playerProductInstall.swf\" );\n";
		}
	}

	echo "</script>\n";
}


add_action('wp_head', 'register_object');
add_action( 'init', 'reg_flash_flickr_ps', 1 );


?>