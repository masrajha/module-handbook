<?php
/*
Plugin Name: Module Handbook
Description: Menampilkan data mata kuliah dan detail mata kuliah.
Version: 1.0
Author: Didik Kurniawan
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Include Google Sheets API handler
require_once plugin_dir_path(__FILE__) . 'includes/google-sheets.php';

// Enqueue scripts and styles
function mh_enqueue_scripts() {
    wp_enqueue_style('mh-style', plugins_url('assets/style.css', __FILE__));
    wp_enqueue_style('bootstrap-icons', 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css');
}
add_action('wp_enqueue_scripts', 'mh_enqueue_scripts');

// Register shortcode to display course data
function mh_display_courses($atts) {
    ob_start();

    $code = isset($_GET['code']) ? sanitize_text_field($_GET['code']) : '';
    $view = isset($_GET['view']) ? sanitize_text_field($_GET['view']) : '';

    // $atts = shortcode_atts(array('code' => '', 'view' => ''), $atts);
    // $code = sanitize_text_field($atts['code']);
    // $view = sanitize_text_field($atts['view']);
    
    if ($code && $view == 'quiz') {
        echo get_quiz($code);
    } elseif ($code && $view == 'project') {
        echo get_project($code);
    } elseif ($code && $view == 'practice') {
        echo get_practice($code);
    } elseif ($code) {
        echo get_course_detail($code);
    } else {
        echo get_courses();
    }
    
    return ob_get_clean();
}
add_shortcode('module_handbook', 'mh_display_courses');
