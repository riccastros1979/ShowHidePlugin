<?php
/*
Plugin Name: ShowHide Text Widget
Plugin URI: http://wordpress.org/plugins/ShowHide-text-widget/
Description: Description about this plugin.
Version: 1.0
Author: BIREME | OPAS | OMS - by Ricardo de Castro
Author URI: http://rcastro.net.br/
Text Domain: ShowHidetext
Domain Path: /languages/
License: MIT
*/

class ShowHideTextWidget extends WP_Widget {

    /**
     * Widget construction
     */
    function __construct() {
        $widget_ops = array('classname' => 'widget_text ShowHide-text-widget', 'description' => __('Text, HTML, CSS, PHP, Flash, JavaScript, Shortcodes', 'ShowHidetext'));
        $control_ops = array('width' => 450);
        parent::__construct('ShowHideTextWidget', __('ShowHide Text', 'ShowHidetext'), $widget_ops, $control_ops);
        load_plugin_textdomain('ShowHidetext', false, basename( dirname( __FILE__ ) ) . '/languages' );
    }

    /**
     * Setup the widget output
     */
	function widget( $args, $instance ) {

        if (!isset($args['widget_id'])) {
          $args['widget_id'] = null;
        }

        extract($args);

        $title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance);
        $titleUrl = empty($instance['titleUrl']) ? '' : $instance['titleUrl'];
        $cssClass = empty($instance['cssClass']) ? '' : $instance['cssClass'];
        $text = apply_filters('widget_ShowHide_text', $instance['text'], $instance);
        $hideTitle = !empty($instance['hideTitle']) ? true : false;
        $hideEmpty = !empty($instance['hideEmpty']) ? true : false;
        $startOpen = !empty($instance['startOpen']) ? true : false;
        $filterText = !empty($instance['filter']) ? true : false;
        $bare = !empty($instance['bare']) ? true : false;

        if ( $cssClass ) {
            if( strpos($before_widget, 'class') === false ) {
                $before_widget = str_replace('>', 'class="'. $cssClass . '"', $before_widget);
            } else {
                $before_widget = str_replace('class="', 'class="'. $cssClass . ' ', $before_widget);
            }
        }

        // Parse the text through PHP
        ob_start();
        eval('?>' . $text);
        $text = ob_get_contents();
        ob_end_clean();
		?>
		<?
        // Run text through do_shortcode
        $text = do_shortcode($text);
		$showhide_text_id = "id_".rand(10,1000000);
		$showhide_header_id = "header_".$showhide_text_id;
		//echo $showhide_text_id;
		//echo $showhide_header_id;
		
        if (!empty($text) || !$hideEmpty) {
            echo $bare ? '' : $before_widget; //insere classe antes do widget
            //if ($startOpen) $startOpen = "target='_blank'";
			if ($startOpen) {
				$startOpen = "display: block;";
				$my_icon = "fa-minus";
			}
			else {
					$startOpen = "display: none;";
					$my_icon = "fa-plus";
			}
			?>
			
			<script>
			function mudacss<? echo $showhide_text_id;?>() {
			 var myButtonClasses = document.getElementById("id_<? echo $showhide_text_id;?>").classList;
			 if (myButtonClasses.contains("fa-plus")) {
				myButtonClasses.remove("fa-plus");
			 } else {
				myButtonClasses.add("fa-plus");
			 }
			 if (myButtonClasses.contains("fa-minus")) {
				myButtonClasses.remove("fa-minus");
			 } else {
				myButtonClasses.add("fa-minus");
			 }
			}
			</script>
			<script>
				function showhide<? echo $showhide_text_id;?>()
				 {
					   var div = document.getElementById("<? echo $showhide_text_id;?>");
				if (div.style.display !== "none") {
					div.style.display = "none";
				}
				else {
					div.style.display = "block";
				}
				 }
			</script>
			<script>
				function toogle_<? echo $showhide_text_id;?>()
				{
					mudacss<? echo $showhide_text_id;?>();
					showhide<? echo $showhide_text_id;?>();
				}
			</script>
			<? echo $before_title ?> 
			<span onclick="toogle_<? echo $showhide_text_id;?>()"> 
				<? echo $title; ?> 
				
				<i id='id_<? echo $showhide_text_id;?>' class='my_icon fa <?php echo $my_icon; ?>' aria-hidden='true'></i>
			</span>
			<?php echo $after_title; ?>
			<div class="showHide_widget" id="<? echo $showhide_text_id;?>" style="<? echo $startOpen; ?>">
			<?

            // Echo the content
            echo $filterText ? wpautop($text) : $text;

            echo $bare ? '' : '</div>' . $after_widget;
        }
    }

    /**
     * Run on widget update
     */
    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        if ( current_user_can('unfiltered_html') )
            $instance['text'] =  $new_instance['text'];
        else
            $instance['text'] = wp_filter_post_kses($new_instance['text']);
        $instance['titleUrl'] = strip_tags($new_instance['titleUrl']);
        $instance['cssClass'] = strip_tags($new_instance['cssClass']);
        $instance['hideTitle'] = isset($new_instance['hideTitle']);
        $instance['hideEmpty'] = isset($new_instance['hideEmpty']);
        $instance['startOpen'] = isset($new_instance['startOpen']);
        $instance['filter'] = isset($new_instance['filter']);
        $instance['bare'] = isset($new_instance['bare']);

        return $instance;
    }

    /**
     * Setup the widget admin form
     */
    function form( $instance ) {
        $instance = wp_parse_args( (array) $instance, array(
            'title' => '',
            'titleUrl' => '',
            'cssClass' => '',
            'text' => ''
        ));
        $title = $instance['title'];
        $titleUrl = $instance['titleUrl'];
        $cssClass = $instance['cssClass'];
        $text = format_to_edit($instance['text']);
?>
        <style>
            .monospace {
                font-family: Consolas, Lucida Console, monospace;
            }
            .etw-credits {
                font-size: 0.9em;
                background: #F7F7F7;
                border: 1px solid #EBEBEB;
                padding: 4px 6px;
            }
        </style>

        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'ShowHidetext'); ?>:</label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('cssClass'); ?>"><?php _e('CSS Classes', 'ShowHidetext'); ?>:</label>
            <input class="widefat" id="<?php echo $this->get_field_id('cssClass'); ?>" name="<?php echo $this->get_field_name('cssClass'); ?>" type="text" value="<?php echo $cssClass; ?>" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('text'); ?>"><?php _e('Content', 'ShowHidetext'); ?>:</label>
            <textarea class="widefat monospace" rows="16" cols="20" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>"><?php echo $text; ?></textarea>
        </p>

        <p>
            <input type="checkbox" id="<?php echo $this->get_field_id('startOpen'); ?>" name="<?php echo $this->get_field_name('startOpen'); ?>" <?php checked(isset($instance['startOpen']) ? $instance['startOpen'] : 0); ?> />
            <label for="<?php echo $this->get_field_id('startOpen'); ?>"><?php _e('Start with show div?', 'ShowHidetext'); ?></label>
        </p>

        <p>
            <input id="<?php echo $this->get_field_id('bare'); ?>" name="<?php echo $this->get_field_name('bare'); ?>" type="checkbox" <?php checked(isset($instance['bare']) ? $instance['bare'] : 0); ?> />&nbsp;<label for="<?php echo $this->get_field_id('bare'); ?>"><?php _e('Do not output before/after_widget/title', 'ShowHidetext'); ?></label>
        </p>

        
<?php
    }
}

/**
 * Register the widget
 */
function ShowHide_text_widget_init() {
    register_widget('ShowHideTextWidget');
}
add_action('widgets_init', 'ShowHide_text_widget_init');


