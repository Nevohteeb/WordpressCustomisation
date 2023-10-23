<?php

  add_theme_support('post-thumbnails');
  add_theme_support('custom-logo');

  function add_cors_http_header() {header("Access-Control-Allow-Origin: *");}
  add_action('init', 'add_cors_http_header');

  function enqueue_parent_and_custom_styles() {
      // Enqueue parent theme styles
      wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
    
      // Enqueue custom styles
      wp_enqueue_style('child-style', get_stylesheet_directory_uri() . '/custom.css', array('parent-style'));
  }

  add_action('wp_enqueue_scripts', 'enqueue_parent_and_custom_styles');
  

  // Customiser Settings
  // Use the WordPress Customization API to register these customizer settings --------
  function custom_theme_customize_register( $wp_customize ) {

      // ******** BODY BG COLOUR *********
      // Register and define customizer settings here
      $wp_customize->add_setting('background_color', array(
        'default' => '#1f1f1f', // Default background color
        'transport' => 'postMessage',
      ));
      
      // Add a control for the background color
      $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'background_color', array(
        'label' => __('Background Color', 'custom-theme'),
        'section' => 'colors',
      )));

      // ******** FONT FAMILY ***********
      $wp_customize->add_section('fonts', array(
          'title' => __('Fonts', 'custom-theme'),
          'priority' => 30,
      ));

      // Font family setting
      $wp_customize->add_setting('font_family', array(
          'default' => 'Arial, sans-serif', // Default font family
          'transport' => 'postMessage',
      ));

      // Add a control for font family
      $wp_customize->add_control('font_family_control', array(
          'label'    => 'Font Family',
          'section'  => 'fonts',
          'settings' => 'font_family',
          'type'     => 'select',
          'choices'  => array(
              'Roboto'     => 'Roboto',
              'Poppins' => 'Poppins',
              'DotGothic' => 'DotGothic',
          ),
      ));

      // ****** NAVBAR BG COLOUR **********

      $wp_customize->add_setting('navbar_color', array(
          'default' => '#333333', // Default navbar color
          'transport' => 'postMessage',
      ));

      $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'navbar_color', array(
          'label' => __('Navbar Color', 'custom-theme'),
          'section' => 'colors',
      )));

      // ******* MOBILE MENU BG COLOUR ********* 
      $wp_customize->add_setting('mobile_menu_color', array(
          'default' => '#333333', // Default navbar color
          'transport' => 'postMessage',
      ));

      $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'mobile_menu_color', array(
          'label' => __('Mobile Menu Colour', 'custom-theme'),
          'section' => 'colors',
      )));

  }

  add_action( 'customize_register', 'custom_theme_customize_register' );

  
  // Custom REST API endpoint to retrieve customizer settings
  function get_customizer_settings() {
    $settings = array(
      'backgroundColor' => get_theme_mod('background_color', '#ffffff'),
      'fontFamily' => get_theme_mod('font_family', 'Arial, sans-serif'),
      'navbarColor' => get_theme_mod('navbar_color', '#333333'),
      'mobileMenu' => get_theme_mod('mobile_menu_color', '#333333'),
    );
  
    return rest_ensure_response($settings);
  }

  add_action('rest_api_init', function () {
    register_rest_route('custom-theme/v1', '/customizer-settings', array(
      'methods' => 'GET',
      'callback' => 'get_customizer_settings',
    ));
  });

  // ********* GET NAV LOGO SET IN ADMIN DASHBOARD ************
  function get_nav_logo() {
    $custom_logo_id = get_theme_mod('custom_logo');
    $logo = wp_get_attachment_image_src($custom_logo_id, 'full');
    
    return $logo;
  }

  add_action('rest_api_init', function () {
      register_rest_route('custom/v1', 'nav-logo', array(
          'methods' => 'GET',
          'callback' => 'get_nav_logo',
      ));
  });
         
?>