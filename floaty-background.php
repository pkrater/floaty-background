<?php

/**
        * Plugin Name: Floaty Background
        * Author:  Riksantikvarieämbetet, Petter Johannisson
        * Description: Plugin for creating a floaty svg background. Just add svgs..
    */

class FloatyBackground
{
    private $plugin_name = 'FloatyBackground';
    private $plugin_version = '0.2.1';
    private $url;
    private $path = '';
    private $style = 'css/floatystyle.css';
    private $script = 'js/floatyscript.js';
    private $string; 

    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;
   // public $value;

    /**
     * Start up
     */
    public function __construct()
    {   
        $this->url = plugin_dir_url( __FILE__ );

        add_action( 'wp_enqueue_scripts', array( &$this ,'floaty_enqueue_scripts') );
        add_shortcode( 'get_floaty', array( &$this ,'floaty_shortcode') );
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }
     
    public function floaty_enqueue_scripts(){
        $currFile = '/shared/httpd/wordpress/htdocs/wp-content/plugins/floaty-background/js/floatyscript.js';
        // filemtime($currFile)
        wp_register_style( $this->plugin_name, $this->url . $this->path . $this->style , array(), $this->plugin_version, false);
        wp_register_script( $this->plugin_name, $this->url . $this->path . $this->script, array(), getlastmod(), false);
    }

    public function floaty_shortcode( $attributes ) {
        $svg_array =  get_option( 'floaty_options' )['svg_array'];
        wp_enqueue_style( $this->plugin_name );  
        wp_add_inline_script( $this->plugin_name, 'var passed_object = {theSvgs : "' . $svg_array . '"}');
        wp_enqueue_script( $this->plugin_name );
        return;
    }
    


    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        // This page will be under "Settings"
        add_options_page(
            'Settings Admin', 
            'Floaty Background Settings', 
            'manage_options', 
            'floaty-background-admin', 
            array( $this, 'create_admin_page' )
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        // Set class property
        $this->options = get_option( 'floaty_options' );
        ?>
        <div class="wrap">
            <h1>Floaty Background Settings</h1>
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'floaty_option_group' );
                do_settings_sections( 'floaty-setting-admin' );
                submit_button();
            ?>
            </form>
        </div>
        <?php
       
       echo(var_dump($this->options));
       //$this->value = $this->options;
        
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {        
        register_setting(
            'floaty_option_group', // Option group
            'floaty_options', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'setting_section_id', // ID
            'Floaty Background', // Title
            array( $this, 'print_section_info' ), // Callback
            'floaty-setting-admin' // Page
        );  

        add_settings_field(
            'svg_array', // ID
            'Paste svg array', // Title 
            array( $this, 'svg_array_callback' ), // Callback
            'floaty-setting-admin', // Page
            'setting_section_id' // Section           
        );
        
          
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {
        $new_input = array();
        if( isset( $input['svg_array'] ) )
            $new_input['svg_array'] = sanitize_text_field( $input['svg_array'] );
        return $new_input;
    }

    /** 
     * Print the Section text
     */
    public function print_section_info()
    {
        print 'Enter your settings below:';
    }

    /** 
     * Get the settings option array and print the svg_array
     */
    public function svg_array_callback()
    {
        printf(
            '<textarea id="svg_array" name="floaty_options[svg_array]" value="%s" /></textarea>',
            isset( $this->options['svg_array'] ) ? esc_attr( $this->options['svg_array']) : ''
        );
    }   
}


new FloatyBackground();

