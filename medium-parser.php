<?php
	
/*
Plugin Name: Medium Parser
Plugin URI: https://github.com/jasonm4130/Medium-Parser-WordPress-Plugin
Description: This plugin allows you to parse your medium feed into your WordPress site. Linking back to your medium accounts stories.
Author: Jason Matthew
Author URI: https://jasonmdesign.com
Version: 1.0
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: medium-parser
*/

function medium_parser_admin_menu () {
    add_submenu_page( 
        'themes.php',
        'Medium Parser',
        'Medium Parser',
        'manage_options',
        'medium_parser',
        'medium_parser_settings_callback'
        );
}

function medium_parser_public_scripts () {
    wp_register_script('medium-parser-js-dateFormat-inc', plugins_url('/js/public/jquery-dateFormat.min.js',__FILE__), array('jquery'), '', true);
    wp_register_script('medium-parser-js-public', plugins_url('/js/public/medium-parser-api.js',__FILE__), array('jquery', 'medium-parser-js-dateFormat-inc'), '', true);

    wp_enqueue_script('medium-parser-js-dateFormat-inc');
    wp_enqueue_script('medium-parser-js-public');
}

function medium_parser_public_styles () {
    wp_enqueue_style( 'bootstrap-4-grid-css', plugins_url('/css/public/bootstrap-grid.min.css',__FILE__) );
    wp_enqueue_style( 'medium-parser-css', plugins_url('/css/public/medium-parser.css',__FILE__) );
}

add_action( 'admin_menu', 'medium_parser_admin_menu');
add_action( 'admin_init', 'medium_parser_register_options');
add_action( 'wp_enqueue_scripts', 'medium_parser_public_scripts');
add_action( 'wp_enqueue_scripts', 'medium_parser_public_styles' );
add_action( 'init', 'medium_parser_register_shortcodes');

function medium_parser_settings_callback () {
    $feed_url = (get_option('medium_feed_url')) ? get_option('medium_feed_url') : 'https://medium.jasonmdesign.com/feed';
    $feed_section_title = (get_option('feed_section_title')) ? get_option('feed_section_title') : 'Latest articles from Medium';
    $publication_url = (get_option('publication_url')) ? get_option('publication_url') : 'https://medium.jasonmdesign.com';

    echo '
        <div class="wrap">
            <h2>Medium Parser Settings</h2>
            <p><b>Note:</b> Please add your medium feed to the settings below</p>
            <form action="options.php" method="post">';
            settings_fields('medium_parser_plugin_options');
            @do_settings_fields('medium_parser_plugin_options');
            echo 'Blog Section Title: <input type="text" name="feed_section_title" value="' . $feed_section_title . '"></br>';
            echo 'Medium Feed Url: <input type="text" name="medium_feed_url" value="' . $feed_url . '"></br>';
            echo 'Medium Publication Url: <input type="text" name="publication_url" value="' . $publication_url . '"></br>';
            @submit_button();
    echo'
            </form>
            <p>To display the first 4 posts from your medium feed simply use the shortcode [medium-parser-disp] wherever it is required.</p>
            <p>This plugin was created for simple use but if you require more functionality feel free to get in touch with me at <a href="mailto:jason@jasonmdesign.com">jason@jasonmdesign.com</a></p> 
        </div>
    ';
}

function medium_parser_register_options () {
    register_setting('medium_parser_plugin_options', 'medium_feed_url');
    register_setting('medium_parser_plugin_options', 'feed_section_title');
    register_setting('medium_parser_plugin_options', 'publication_url');
}

add_action('wp_head', 'medium_parser_feed_url_js');

function medium_parser_feed_url_js () {

    $feed_url = (get_option('medium_feed_url')) ? get_option('medium_feed_url') : 'https://medium.jasonmdesign.com/feed';

   echo '<script type="text/javascript">
           var mediumFeedUrl = "' . $feed_url . '";
         </script>';
}

function medium_parser_shortcode ( $args, $content="") {
    $feed_section_title = get_option('feed_section_title');
    $publication_url = get_option('publication_url');
    $output = '
        <!-- Blog-->
        <section class="section blog" id="blog">
        <div class="container">
            <!---->
            <header class="section-heading">
            <h2>' . $feed_section_title . '</h2>
            </header>
            <!---->
            <div class="section-content" id="blogContent">
            <div class="row" id="jsonContent"></div>
            </div>
            <!-- /#blogContent-->
            <div class="text-center"><a class="btn btn-dark" id="loadBlogPosts" href="' . $publication_url . '">View All<span class="fa fa-angle-double-right"></span></a></div>
        </div>
        <!-- /.container-->
        </section>
        <!-- Blog-->
    ';

    return $output;
}

function medium_parser_register_shortcodes () {
    add_shortcode( 'medium_parser_disp', 'medium_parser_shortcode');
}