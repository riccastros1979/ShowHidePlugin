<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Widget class
 *
 * @package Show_Hide_TinyMCE_Widget
 * @since 0.5
 */

if ( ! class_exists( 'ShowHideTextWidget' ) ) {

	class ShowHideTextWidget extends WP_Widget {

		/**
		 * Widget Class constructor
		 *
		 * @uses WP_Widget::__construct()
		 * @since 0.5
		 */
		public function __construct() {
			/* translators: title of the widget */
			$widget_title = __( 'Show Hide Text', 'show-hide-tinymce-widget' );
			/* translators: description of the widget, shown in available widgets */
			$widget_description = __( 'Arbitrary text or HTML with visual editor', 'show-hide-tinymce-widget' );
			$widget_ops = array( 'classname' => 'ShowHideTextWidget', 'description' => $widget_description );
			$control_ops = array( 'width' => 800, 'height' => 600 );
			parent::__construct( 'show-hide-tinymce', $widget_title, $widget_ops, $control_ops );
		}

		/**
		 * Output widget HTML code
		 *
		 * @uses apply_filters()
		 * @uses WP_Widget::$id_base
		 *
		 * @param string[] $args
		 * @param mixed[] $instance
		 * @return void
		 * @since 0.5
		 */
		public function widget( $args, $instance ) {
			$before_widget = $args['before_widget'];
			$after_widget = $args['after_widget'];
			$before_title = $args['before_title'];
			$after_title = $args['after_title'];
			do_action( 'Show_Hide_tinymce_before_widget', $args, $instance );
			$before_text = apply_filters( 'Show_Hide_tinymce_before_text', '<div class="textwidget">', $instance );
			$after_text = apply_filters( 'Show_Hide_tinymce_after_text', '</div>', $instance );
			$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
			$text = apply_filters( 'widget_text', empty( $instance['text'] ) ? '' : $instance['text'], $instance, $this );
			$hide_empty = apply_filters( 'Show_Hide_tinymce_hide_empty', false, $instance );
			$randomId = "id_".rand(10,1000000);
			$showhide_text_id = $randomId;
			$startOpen = array ();
			$startOpen[$randomId] = $instance['startOpen'];
			//echo $startOpen[$randomId];
			if ($startOpen[$randomId] == 1) {
				$startOpen[$randomId] = "display: block;";
				$my_icon = "fa-minus";  
			}
			else {
				$startOpen[$randomId] = "display: none;";
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
				function showhide<? echo $showhide_text_id;?>() {
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
				function toogle_<? echo $showhide_text_id;?>() {
					mudacss<? echo $showhide_text_id;?>();
					showhide<? echo $showhide_text_id;?>();
				}
			</script>
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
			<? echo $before_widget; ?>
			<? echo $before_title ?> 
			<span onclick="toogle_<? echo $showhide_text_id;?>()"> 
				<? echo $title; ?> 
				<i id='id_<? echo $showhide_text_id;?>' class='my_icon fa <?php echo $my_icon; ?>' aria-hidden='true'></i>
			</span>
			<?php echo $after_title; ?>
			<div class="showHide_widget" id="<? echo $showhide_text_id;?>" style="<? echo $startOpen[$randomId]; ?>">
			<?
            // Echo the content
			echo $before_text;
            echo $filterText ? wpautop($text) : $text;
			echo $after_text;
            echo '</div>';
			
			echo $after_widget;
			do_action( 'Show_Hide_tinymce_after_widget', $args, $instance );
		}

		/**
		 * Update widget data
		 *
		 * @uses current_user_can()
		 * @uses wp_filter_post_kses()
		 * @uses apply_filters()
		 *
		 * @param mixed[] $new_instance
		 * @param mixed[] $old_instance
		 * @return mixed[]
		 * @since 0.5
		 */
		public function update( $new_instance, $old_instance ) {
			$instance = $old_instance;
			$instance['title'] = strip_tags( $new_instance['title'] );
			if ( current_user_can( 'unfiltered_html' ) ) {
				$instance['text'] = $new_instance['text'];
			}
			else {
				$instance['text'] = stripslashes( wp_filter_post_kses( addslashes( $new_instance['text'] ) ) ); // wp_filter_post_kses() expects slashed
			}
			$instance['type'] = strip_tags( $new_instance['type'] );
			$instance['startOpen'] = strip_tags( $new_instance['startOpen'] );
			$instance['filter'] = strip_tags( $new_instance['filter'] );
			$instance = apply_filters( 'Show_Hide_tinymce_widget_update',  $instance, $this );
			return $instance;
		}
		
		/**
		 * Output widget form
		 *
		 * @uses wp_parse_args()
		 * @uses apply_filters()
		 * @uses esc_attr()
		 * @uses esc_textarea()
		 * @uses WP_Widget::get_field_id()
		 * @uses WP_Widget::get_field_name()
		 * @uses _e()
		 * @uses do_action()
		 * @uses apply_filters()
		 *
		 * @param mixed[] $instance
		 * @return void
		 * @since 0.5
		 */
		public function form( $instance ) {
			global $wp_customize;
			$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'text' => '', 'type' => 'visual' ) );
			// Force Visual mode in Customizer (to avoid glitches)
			if ( $wp_customize ) {
				$instance['type'] = 'visual';
			}
			// Guess (wpautop) filter value for widgets created with previous version
			if ( ! isset( $instance['filter'] ) ) {
				$instance['filter'] = $instance['type'] == 'visual' && substr( $instance['text'], 0, 3 ) != '<p>' ? 1 : 0;
			}
			$title = strip_tags( $instance['title'] );
			do_action( 'Show_Hide_tinymce_before_editor' );
			?>
			<input id="<?php echo esc_attr( $this->get_field_id( 'type' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'type' ) ); ?>" type="hidden" value="<?php echo esc_attr( $instance['type'] ); ?>" />
			<p><label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title:' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></p>
			<?php
			do_action( 'Show_Hide_tinymce_editor', $instance['text'], $this->get_field_id( 'text' ), $this->get_field_name( 'text' ), $instance['type'] );
			do_action( 'Show_Hide_tinymce_after_editor' );
			?>
			<input id="<?php echo esc_attr( $this->get_field_id( 'filter' ) ); ?>-hidden" name="<?php echo esc_attr( $this->get_field_name( 'filter' ) ); ?>" type="hidden" value="0" />
			<p><input id="<?php echo esc_attr( $this->get_field_id( 'filter' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'filter' ) ); ?>" type="checkbox" value="1" <?php checked( $instance['filter'] ); ?> />&nbsp;<label for="<?php echo esc_attr( $this->get_field_id( 'filter' ) ); ?>"><?php _e( 'Automatically add paragraphs' ); ?></label></p>
			<input id="<?php echo esc_attr( $this->get_field_id( 'startOpen' ) ); ?>-hidden" name="<?php echo esc_attr( $this->get_field_name( 'startOpen' ) ); ?>" type="hidden" value="0" />
			<p><input id="<?php echo esc_attr( $this->get_field_id( 'startOpen' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'startOpen' ) ); ?>" type="checkbox" value="1" <?php checked( $instance['startOpen'] ); ?> />&nbsp;<label for="<?php echo esc_attr( $this->get_field_id( 'startOpen' ) ); ?>"><?php _e( 'Start with the widget open.' ); ?></label></p>
			<?php
		}

	} // END class ShowHideTextWidget

} // END class_exists check
