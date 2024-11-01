<?php
/*
Plugin Name: SPORTREEF Sports Widgets
Description: SPORTREEF sports widgets using [sportreef_widget id="xxx"] short code or widgets menu
Version: 1.2
Author: Sportreef
Author URI: htts://www.sportreef.com
License: GPLv2
*/

class SportreefWidgets_Shortcode {
    public static $add_script = false;

    static function init() {
        add_shortcode('sportreef_widget', array(__CLASS__, 'handle_shortcode'));

        add_action('the_posts', array(__CLASS__, 'has_my_shortcode'));

        add_action("wp_enqueue_scripts", array(__CLASS__, 'enqueue_scripts'));
    }

    static function handle_shortcode($atts) {
        $id = @trim($atts['id']);
        if (strlen($id) === 0) return '';
        return '<div data-sportreef-widget="' . $id . '"></div>';
    }

    static function has_my_shortcode($posts) {
        foreach ($posts as $post) {
            if ( @stripos($post->post_content, '[sportreef_widget id="') !== FALSE) {
                self::$add_script = true;
                break;
            }
        }
        return $posts;
    }

    static function enqueue_scripts() {
        if ( ! self::$add_script ) { return; }
        wp_enqueue_script('sportreef_footer_script', 'https://gate.sports-widgets.com/widget/3.0/loader.js', array(), false, true);
    }
}

SportreefWidgets_Shortcode::init();


class SportreefWidgets_Sidebar  extends WP_Widget {
    function __construct() {
        parent::__construct(
            'sportreef_widgets_widget',
            __('SPORTREEF Sports Widget', 'sportreef_widgets' ),
            array (
                'description' => __( 'SPORTREEF Sports Widget - Simply add SPORTREEF sports widgets', 'sportreef_widgets' )
            )
        );
    }

    function form( $instance ) {
        $widget_id = ! empty( $instance['widget_id'] ) ? $instance['widget_id'] : '';
        echo '<p>';
        echo '<label for="' . esc_attr( $this->get_field_id( 'widget_id' ) ) . '">' . esc_attr_e( 'SPORTREEF Widget id:', 'sportreef_widgets' ) . '</label>';
        echo '<input class="widefat" id="' . esc_attr( $this->get_field_id( 'widget_id' ) ) . '" name="' . esc_attr( $this->get_field_name( 'widget_id' ) ) . '" type="text" value="' . esc_attr( $widget_id ) . '">';
        echo '</p>';
    }

    function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['widget_id'] = ( ! empty( $new_instance['widget_id'] ) ) ? strip_tags( $new_instance['widget_id'] ) : '';
        return $instance;
    }

    function widget( $args, $instance ) {
        echo $args['before_widget'];
        if ( ! empty( $instance['widget_id'] ) ) {
            wp_enqueue_script('sportreef_footer_script', 'https://gate.sports-widgets.com/widget/3.0/loader.js', array(), false, true);
            echo '<div data-sportreef-widget="' . $instance['widget_id'] . '"></div>';
        }
        echo $args['after_widget'];
    }
}

function sportreef_register_sidebar_widgets() {
    register_widget( 'SportreefWidgets_Sidebar' );
}

add_action( 'widgets_init', 'sportreef_register_sidebar_widgets' );
