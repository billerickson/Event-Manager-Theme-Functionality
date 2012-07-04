<?php
/*
Plugin Name: Event Manager Theme Functionality
Plugin URI: http://www.billerickson.net
Description: To be used with the Event Manager theme
Version: 0.9.1
Author: Bill Erickson
Author URI: http://www.billerickson.net
License: GPLv2
*/

define( 'SC_DIR', dirname( __FILE__ ) );

class Social_Coup_Functionality {
	var $instance;

	function __construct() {
		$this->instance =& $this;
		add_action( 'plugins_loaded', array( $this, 'init' ) );	
	}

	function init() {
		// Translations
		load_plugin_textdomain( 'social-coup-functionality', false, SC_DIR . '/lib/languages' );
		
		// Create Speaker and Session Post Types
		add_action( 'init', array( $this, 'speakers_post_type' ) );
		add_action( 'init', array( $this, 'sessions_post_type' ) );

		// Connect Speakers and Sessions
		add_action( 'init', array( $this, 'post_type_connections' ), 100 );
		
		// Create Sessions Taxonomy
		add_action( 'init', array( $this, 'sessions_taxonomy' ) );

		// Create Metaboxes
		add_filter( 'cmb_meta_boxes', array( $this, 'metaboxes' ) );
		add_action( 'init', array( $this, 'initialize_meta_boxes' ), 9999 );
		
		// Create hidden timestamp postmeta
		add_action( 'save_post', array( $this, 'hidden_timestamp_meta' ), 99 );

		// Create Rotator Post Type
		add_action( 'init', array( $this, 'rotator_post_type' ) );
		
		// Rotator Image Size
		add_image_size( 'sc-rotator', 999, 205 );
		
		// Move Featured Image metabox on Rotator
		add_action('do_meta_boxes', array( $this, 'rotator_image_metabox' ) );
		
		// Adjust Rotator Columns
		add_filter( "manage_sc-rotator_posts_columns",       array( $this, 'rotator_columns' ) );
		add_action( "manage_sc-rotator_posts_custom_column", array( $this, 'rotator_column_data' ), 10, 2 );
		
		// Sort Rotator Admin by Menu Order
		add_action( 'pre_get_posts', array( $this, 'rotator_order' ) );
		
		// Help Tab, for Event Manager Settings page
		add_action( 'load-genesis_page_event-manager', array( $this, 'help_tab' ) );

		
	}
	
	/** 
	 * Register Speaker Post Type
	 * @link http://codex.wordpress.org/Function_Reference/register_post_type
	 *
	 */

	function speakers_post_type() {
		$labels = array(
			'name' => __( 'Speakers', 'social-coup-functionality' ),
			'singular_name' => __( 'Speaker', 'social-coup-functionality' ),
			'add_new' => __( 'Add New', 'social-coup-functionality' ),
			'add_new_item' => __( 'Add New Speaker', 'social-coup-functionality' ),
			'edit_item' => __( 'Edit Speaker', 'social-coup-functionality' ),
			'new_item' => __( 'New Speaker', 'social-coup-functionality' ),
			'view_item' => __( 'View Speaker', 'social-coup-functionality' ),
			'search_items' => __( 'Search Speakers', 'social-coup-functionality' ),
			'not_found' =>  __( 'No Speakers found', 'social-coup-functionality' ),
			'not_found_in_trash' => __( 'No Speakers found in trash', 'social-coup-functionality' ),
			'menu_name' => __( 'Speakers', 'social-coup-functionality' ),
		);
		
		$args = array(
			'labels' => $labels,
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true, 
			'show_in_menu' => true, 
			'query_var' => true,
			'rewrite' => array( 'slug' => 'speakers' ),
			'capability_type' => 'post',
			'has_archive' => false, 
			'hierarchical' => false,
			'menu_position' => 5,
			'supports' => array('title','editor', 'thumbnail', 'page-attributes')
		); 
	
		register_post_type( 'sc-speakers', $args );	
	}

	/** 
	 * Register Sessions Post Type
	 * @link http://codex.wordpress.org/Function_Reference/register_post_type
	 *
	 */

	function sessions_post_type() {
		$labels = array(
			'name' => __( 'Sessions', 'social-coup-functionality' ),
			'singular_name' => __( 'Session', 'social-coup-functionality' ),
			'add_new' => __( 'Add New', 'social-coup-functionality' ),
			'add_new_item' => __( 'Add New Session', 'social-coup-functionality' ),
			'edit_item' => __( 'Edit Session', 'social-coup-functionality' ),
			'new_item' => __( 'New Session', 'social-coup-functionality' ),
			'view_item' => __( 'View Session', 'social-coup-functionality' ),
			'search_items' => __( 'Search Sessions', 'social-coup-functionality' ),
			'not_found' =>  __( 'No Sessions found', 'social-coup-functionality' ),
			'not_found_in_trash' => __( 'No Sessions found in trash', 'social-coup-functionality' ),
			'menu_name' => __( 'Sessions', 'social-coup-functionality' ),
		);
		
		$args = array(
			'labels' => $labels,
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true, 
			'show_in_menu' => true, 
			'query_var' => true,
			'rewrite' => array( 'slug' => 'schedule' ),
			'capability_type' => 'post',
			'has_archive' => false, 
			'hierarchical' => false,
			'menu_position' => 5,
			'supports' => array('title','editor')
		); 
	
		register_post_type( 'sc-sessions', $args );	
	}

	/**
	 * Post Type Connection
	 * Uses Posts2Posts
	 * @link https://github.com/scribu/wp-posts-to-posts/wiki
	 */
	 function post_type_connections() {
	 
	 	// Make Sure plugin is active
	 	if ( !function_exists( 'p2p_register_connection_type' ) )
			return;
			
		p2p_register_connection_type( array(
			'name' => 'sessions_to_speakers',
			'from' => 'sc-sessions',
			'to' => 'sc-speakers'
		) );
	 }
	
	/**
	 * Create Taxonomies
	 * @link http://codex.wordpress.org/Function_Reference/register_taxonomy
	 *
	 */
	
	function sessions_taxonomy() {

		$labels = array(
			'name' => __( 'Session Groupings', 'social-coup-functionality' ),
			'singular_name' => __( 'Session Grouping', 'social-coup-functionality' ),
			'search_items' =>  __( 'Search Groupings', 'social-coup-functionality' ),
			'all_items' => __( 'All Groupings', 'social-coup-functionality' ),
			'parent_item' => __( 'Parent Grouping', 'social-coup-functionality' ),
			'parent_item_colon' => __( 'Parent Grouping:', 'social-coup-functionality' ),
			'edit_item' => __( 'Edit Grouping', 'social-coup-functionality' ),
			'update_item' => __( 'Update Grouping', 'social-coup-functionality' ),
			'add_new_item' => __( 'Add New Grouping', 'social-coup-functionality' ),
			'new_item_name' => __( 'New Grouping Name', 'social-coup-functionality' ),
			'menu_name' => __( 'Session Groupings', 'social-coup-functionality' ),
		); 	
	
		register_taxonomy( 'sc-session-grouping', array('sc-sessions'), array(
			'hierarchical' => true,
			'labels' => $labels,
			'show_ui' => true,
			'query_var' => true,
			'rewrite' => array( 'slug' => 'session-grouping' ),
		));
		
	}
	
	/**
	 * Create Metaboxes
	 * @link http://www.billerickson.net/wordpress-metaboxes/
	 *
	 */
	
	function metaboxes( $meta_boxes ) {
		
		// Speakers Metabox
		$speakers_metabox = array(
		    'id' => 'speaker-details',
		    'title' => __( 'Speaker Details', 'social-coup-functionality' ),
		    'pages' => array('sc-speakers'), 
			'context' => 'normal',
			'priority' => 'high',
			'show_names' => true, 
		    'fields' => array(
		    	array(
		    		'name' => __( 'Website URL', 'social-coup-functionality' ),
		    		'id' => 'sc_speaker_url',
		    		'desc' => '',
		    		'type' => 'text'
		    	),
		    	array(
		    		'name' => __( 'Twitter Username', 'social-coup-functionality' ),
		    		'id' => 'sc_speaker_twitter',
		    		'desc' => '',
		    		'type' => 'text_medium',
		    	),
		    )
		
		);
		
		// Use this to override the speaker metabox to create your own
		$override_speaker = apply_filters( 'sc_speaker_metabox_override', false );
		if ( false === $override_speaker ) $meta_boxes[] = $speakers_metabox;
		
		// Sessions Metabox
		$sessions_metabox = array(
			'id' => 'session-details',
			'title' => __( 'Session Details', 'social-coup-functionality' ),
			'pages' => array( 'sc-sessions' ),
			'context' => 'normal',
			'priority' => 'high',
			'show_names' => true,
			'fields' => array(
				array(
					'name' => __( 'Date', 'social-coup-functionality' ),
					'id' => 'sc_session_date',
					'desc' => '',
					'type' => 'text_date_timestamp'
				),
				array(
					'name' => __( 'Time', 'social-coup-functionality' ),
					'id' => 'sc_session_time',
					'desc' => '',
					'type' => 'text_time'
				),
				array(
					'name' => __( 'Location', 'social-coup-functionality' ),
					'id' => 'sc_session_location',
					'desc' => '',
					'type' => 'wysiwyg',
					'options' => array(
						'textarea_rows' => 5,
						'media_buttons' => false,
					)
				)
			)
		);

		// Use this to override the speaker metabox to create your own
		$override_session = apply_filters( 'sc_session_metabox_override', false );
		if ( false === $override_session ) $meta_boxes[] = $sessions_metabox;
		
		// Schedule Page metabox
		$schedule_content = post_type_exists( 'sc-sessions' ) ? sprintf( __( 'Edit this page\'s content from the <a href="%s">Sessions</a> section', 'social-coup-functionality'), admin_url( 'edit.php?post_type=sc-sessions' ) ) : __( 'Please activate the Social Coup Functionality plugin to make this page operational', 'social-coup-functionality' );
		$meta_boxes[] = array(
			'id' => 'schedule-content',
			'title' => __( 'Content', 'social-coup-functionality' ),
			'pages' => array( 'page' ),
			'show_on' => array( 'key' => 'page-template', 'value' => 'template-schedule.php' ),
			'context' => 'normal',
			'priority' => 'high',
			'show_names' => false,
			'fields' => array(
				array(
					'name' => $schedule_content,
					'id' => 'sc_schedule_content',
					'desc' => '',
					'type' => 'title'
				)
			)
		);

		// Speakers Page metabox
		$speaker_content = post_type_exists( 'sc-speakers' ) ? sprintf( __( 'Edit this page\'s content from the <a href="%s">Speakers</a> section', 'social-coup-functionality' ), admin_url( 'edit.php?post_type=sc-speakers' ) ) : __( 'Please activate the Social Coup Functionality plugin to make this page operational', 'social-coup-functionality' );
		$meta_boxes[] = array(
			'id' => 'speaker-content',
			'title' => __( 'Content', 'social-coup-functionality' ),
			'pages' => array( 'page' ),
			'show_on' => array( 'key' => 'page-template', 'value' => 'template-speakers.php' ),
			'context' => 'normal',
			'priority' => 'high',
			'show_names' => false,
			'fields' => array(
				array(
					'name' => $speaker_content,
					'id' => 'sc_speaker_content',
					'desc' => '',
					'type' => 'title'
				)
			)
		);
		
		// Registration Page
		$meta_boxes[] = array(
			'id' => 'registration-iframe',
			'title' => __( 'Registration Iframe', 'social-coup-functionality' ),
			'pages' => array( 'page' ),
			'show_on' => array( 'key' => 'page-template', 'value' => 'template-registration.php' ),
			'context' => 'normal',
			'priority' => 'high',
			'show_names' => false,
			'fields' => array(
				array(
					'name' => __( 'Iframe', 'social-coup-functionality' ),
					'desc' => __( 'If you\'d like to embed an iframe for your registration form (ex: From EventBrite), place it here and it will automatically be added to the bottom of your page\'s content.', 'social-coup-functionality' ), 
					'id' => 'sc_registration_iframe',
					'type' => 'textarea_code'
				)
			)
		);

		return $meta_boxes;
	}

	function initialize_meta_boxes() {
	    if (!class_exists('cmb_Meta_Box')) {
	        require_once( 'lib/metabox/init.php' );
	    }
	}
	
	function hidden_timestamp_meta( $post_id ) {
		if( 'sc-sessions' !== get_post_type( $post_id ) )
			return;
			
		$date = get_post_meta( $post_id, 'sc_session_date', true );
		$time = get_post_meta( $post_id, 'sc_session_time', true );
		if( !empty( $date ) && !empty( $time ) ) {
			$date = date( 'm/d/Y', $date );
			$timestamp = strtotime( $date . ' ' . $time );
			update_post_meta( $post_id, 'sc_session_timestamp', $timestamp );		
		}
				
	}	
	
	/** 
	 * Register Rotator Post Type
	 * @link http://codex.wordpress.org/Function_Reference/register_post_type
	 *
	 */

	function rotator_post_type() {
		$labels = array(
			'name' => __( 'Rotator', 'social-coup-functionality' ),
			'singular_name' => __( 'Image', 'social-coup-functionality' ),
			'add_new' => __( 'Add New', 'social-coup-functionality' ),
			'add_new_item' => __( 'Add New Image', 'social-coup-functionality' ),
			'edit_item' => __( 'Edit Image', 'social-coup-functionality' ),
			'new_item' => __( 'New Image', 'social-coup-functionality' ),
			'view_item' => __( 'View Image', 'social-coup-functionality' ),
			'search_items' => __( 'Search Images', 'social-coup-functionality' ),
			'not_found' =>  __( 'No Images found', 'social-coup-functionality' ),
			'not_found_in_trash' => __( 'No Images found in trash', 'social-coup-functionality' ),
			'menu_name' => __( 'Rotator', 'social-coup-functionality' ),
		);
		
		$args = array(
			'labels' => $labels,
			'public' => true,
			'publicly_queryable' => true,
			'exclude_from_search' => true,
			'show_ui' => true, 
			'show_in_menu' => true, 
			'query_var' => true,
			'rewrite' => array( 'slug' => 'rotator' ),
			'capability_type' => 'post',
			'has_archive' => false, 
			'hierarchical' => false,
			'menu_position' => 5,
			'supports' => array('thumbnail', 'page-attributes')
		); 
	
		register_post_type( 'sc-rotator', $args );	
	}
	
	/**
	 * Move Featured Image Metabox on Rotator
	 *
	 */
	function rotator_image_metabox() {
		remove_meta_box( 'postimagediv', 'sc-rotator', 'side' );
		add_meta_box('postimagediv', __('Custom Image'), 'post_thumbnail_meta_box', 'sc-rotator', 'normal', 'high');
	}		

	function rotator_columns( $columns ) {
		if( !is_array( $columns ) )
			$columns = array();
			
		$new = array();
		$new['featured-image'] = __( 'Image', 'social-coup-functionality' );
		foreach( $columns as $key => $title ) {
			if( 'title' !== $key )
				$new[$key] = $title;
		}	
		
		return $new;
	
	}
	
	function rotator_column_data( $column_name, $post_id ) {
		if( 'featured-image' !== $column_name )
			return;
			
		$image = get_the_post_thumbnail( $post_id, 'sc-rotator' );
		if( empty( $image ) )
			$image = '[No Image]';
			
		echo '<a href="' . get_edit_post_link( $post_id ) . '">' . $image . '</a>';
	}
	
	function rotator_order( $query ) {
		if( is_admin() && isset( $_GET['post_type'] ) && 'sc-rotator' == $_GET['post_type'] ) {
			$query->set( 'orderby', 'menu_order' );
			$query->set( 'order', 'ASC' );
		}
			
	}
	
	/**
	 * Help Tab
	 * @link http://wpdevel.wordpress.com/2011/12/06/help-and-screen-api-changes-in-3-3/
	 *
	 */
	function help_tab() {
		// Create screen content
		$overview = '<h2>Overview</h2>
		
		<p>Thank you for choosing to use the Event Manager Theme, built on WordPress and Genesis.</p>

<p>Helpful links: <a href="http://www.billerickson.net/get-genesis" target="_blank">Genesis Theme Framework</a> | <a href="/wp-admin/index.php?page=wp101">WP101 Video Tutorials</a> | <a href="http://en.support.wordpress.com/">WordPress Support</a></p>

<h2>Training Videos</h2><p>For information on using WordPress, please see the <a href="/wp-admin/admin.php?page=wp101">WP101 training videos</a>. They will guide you through the basics of creating posts and pages, adding photos, creating links, managing sidebars and widgets, and installing plugins. The following instructions assume this basic understanding of WordPress and describe the theme-specific features.</p>

<h2>Required Plugins</h2><p>Once activating this theme, you should be prompted to automatically install three plugins. The <strong>Event Manager Theme Functionality</strong> plugin contains the core functionality of this theme and is required. This allows us to provide ongoing improvements and new features easily without interfering with any of your theme customizations. The second plugin is <strong>Posts 2 Posts</strong>, which allows you to link Sessions and Speakers. The final one is the <strong>WP101 Plugin</strong>, which provides video tutorials for using WordPress.</p>

<p>If you haven\'t done so already, please install and activate these plugins. There should be a notice at the top of this screen to start the installation.</p>

<h2>Recommended Plugins</h2>

<ul>
<li><a href="http://www.billerickson.net/go/gravity-forms">Gravity Forms</a> - The best contact form plugin available</li>
<li><a href="http://wordpress.org/extend/plugins/contact-form-7/">Contact Form 7</a> - The best <em>free</em> contact form plugin available</li>
<li><a href="http://wordpress.org/extend/plugins/genesis-simple-sidebars/">Genesis Simple Sidebars</a> - Create additional sidebars for specific pages</li>
<li><a href="http://wordpress.org/extend/plugins/genesis-title-toggle/">Genesis Title Toggle</a> - Disable page titles on certain pages</li>
</ul>
';
		

		$event_information = '<h2>Event Information</h2><p>Go to the <a href="' . admin_url( 'admin.php?page=event-manager' ) . '">Event Manager Settings</a> page to set up your event\'s information.</p>

<p>In the first box (Event Information) you can define the date, location, and registration information. Sample information will already be entered to help guide you.</p>

<p>In the second box (Homepage Introduction) you can add additional content that displays below the site title on the homepage. By default there is two columns of text, but you can change this by clicking the HTML tab. See the <strong>Content Columns</strong> section below for more information. </p>

<p>In the third box (Footer Text) you can specify the text on the left and right of the footer. Again, there is sample text to help guide you.</p>';

		$menu = '<h2>Menu</h2><p>To set up the menu at the top of the page, first go to Genesis > Theme Settings and check "Include Primary Navigation Menu". Then go to Appearance > Menus, type a "Menu Name" (ex: Primary Menu), then click "Save Menu". Then in the left column, select your menu from the "Primary Navigation Menu" dropdown.</p>

<p>Now you can add pages to your menu and have them appear at the top of all your site\'s pages. For more information, see this <a href="' . admin_url( 'admin.php?page=wp101&document=13' ) . '">video on building menus</a>.</p>

<p>You can also include a search form like the demo site by going to Genesis > Theme Settings, checking "Enable Extras on Right Side", and selecting "Search form".</p>';

		$homepage = '<h2>Homepage</h2><p>The homepage features three columns of "widget areas", which allow you to drop prebuilt widgets or text in any order and column you choose. Go to Appearance > Widgets to manage the three widget areas: Home Left, Home Middle, and Home Right.</p>

<p>On the demo site, the sidebars feature the following widgets:</p>

<ul>
<li>Home Left: a <strong>text</strong> widget with general information about the event.</li>
<li>Home Middle: the <strong>Genesis - Featured Posts</strong> widget to display latest news, and <strong>Genesis - Latest Tweets</strong> widget for displaying recent twitter updates.</li>
<li>Home Right: the <strong>Speakers Widget</strong> for displaying all the event\'s speakers, and a <strong>text</strong> widget containing images of sponsor logos.
</ul>

<p>For more information on managing widgets, see this <a href="' . admin_url( 'index.php?page=wp101&document=12' ) . '">video on adding widgets</a>.</p>

<h2>Homepage Rotator</h2>

<p>This theme has a built-in image rotator on the homepage, which displays above the three widget areas. It is only displayed if images have been added.</p>

<p>Go to <a href="' . admin_url( 'post-new.php?post_type=sc-rotator' ) . '">Rotator > Add New</a> and click "Set Featured Image". Upload an image, then click "Use as Featured". The image will automatically be scaled down to 205px tall, so make sure the image is at least that tall. You can specify the image order by using the "Order" attribute in the right column (lower numbers come first).</p>';

		$speakers = '<h2>Speakers</h2>

<p>Go to Pages > Add New and create a page with any name you\'d like (ex: Speakers). In the right column, under Page Attributes select "Speakers" as the page template. Publish the page. This creates the page that you can now add to your menu.</p>

<p>To populate the page with speakers, go to Speakers > Add New. Give the speaker a name in the title area and describe the speaker in the editor. Provide the speaker\'s website URL and twitter username in the Speaker Details box (both are optional fields). Upload their photo by clicking "Set Featured Image", uploading it, then clicking "Use as Featured". Specify the order in which they are displayed using the "Order" field in the right column. If you\'ve created a session that this speaker will participate in, select it in the Connected Sessions area.</p>';

		$schedule = '<h2>Schedule</h2>

<p>Go to Pages > Add New and create a page with any name you\'d like (ex: Schedule). In the right column, under Page Attributes select "Schedule" as the page template. Publish the page. this creates the page that you can now add to your menu.</p>

<p>To populate the page with sessions, go to Sessions > Add New. Give the session a title and describe the session in the editor. Under Session Details, select a date, time, and describe the location. The date and time are used for sorting the sessions. Connect a speaker to this session from the Connected Speakers section.</p>

<p>If you\'d like to group your sessions (ex: Day 1, Day 2), go to Sessions > Session Groupings and create your groupings. Give each grouping a Name (ex: Day 1) and Description (ex: 23 Nov 2011). Then edit your sessions and add them to the appropriate groupings.</p>';

		$faq = '<h2>FAQ</h2>

<p>The FAQ in the demo is built using a standard page (no page template is needed). To link to individual answers:</p>
<ul>
<li>Type the answer as a headline (ex: h3)</li>
<li>Switch to HTML view and give it a unique id ( ex: id="answer1")</li>
<li>Switch back to Visual view, type the answer at the top in the Questions section</li>
<li>Select the answer, click the Link button, and for the URL put #answer1 (where answer1 is the same as your unique id).</li>
</ul>

<p><a href="https://gist.github.com/1553021">Here\'s an example</a>';

		$registration = '<h2>Registration</h2>

<p>Go to Pages > Add New and create a page with any name you\'d like (ex: Registration). In the right column, under Page Attributes select "Registration" as the page template. Publish the page.</p>

<p>Below the editor you\'ll now have a box that says "Registration Iframe". You can drop an iframe from your registration service (ex: EventBrite) and it will automatically be added to the end of the page\'s content.</p>';

		$contact = '<h2>Contact</h2>

<p>The theme is designed to work with both Gravity Forms (paid plugin) and Contact Form 7 (free plugin). Install the plugin of your choice, build the form in the appropriate section, then create a page and drop the appropriate shortcode in it.</p>

<p>If using Contact Form 7, you might want to customize the HTML of the form itself. Here\'s the HTML from the demo site\'s form: <a href="https://gist.github.com/1553046">https://gist.github.com/1553046</a></p>';

		$content_columns = '<h2>Content Columns</h2>

<p>This theme has Content Columns built-in so that you can create multiple columns of content. For example, the <a href="#">Press</a> page in the theme demo has two columns of content, in addition to the sidebar on the right.</p>

<p>To create multiple columns, click the "HTML" tab on the editor and type the appropriate HTML code. Then switch back to the "Visual" tab and fill those content areas in.</p>

<p>For two columns, use this:</p>

<pre>
&lt;div class="one-half first">This is the left column&lt;/div>
&lt;div class="one-half">This is the right column&lt;/div>
</pre>

<p>For three columns, use this:</p>

<pre>
&lt;div class="one-third first">This is the left column.&lt;/div>
&lt;div class="one-third">This is the middle column.&lt;/div>
&lt;div class="one-third">This is the right column.&lt;/div>
</pre>

<p>For more information, see <a href="http://www.studiopress.com/tutorials/genesis/content-column-classes">How to use column classes</a>.</p>';

		$advanced_customization = '<h2>Advanced Customization</h2>

<p>If you\'re a developer, there are hooks and filters in the Event Manager Theme Functionality plugin so that you can customize it to your needs.</p>

<ul>
<li><code>apply_filters( \'sc_speaker_metabox_override\', \'__return_true\' );</code> will remove the Speaker Details metabox (so that you can create your own)</li>
<li><code>apply_filters( \'sc_session_metabox_override\', \'__return_true\' );</code> will remove the Session Details metabox</li>
<li><code>cmb_meta_boxes</code> filter can be used to create your own metaboxes. See the <a href="https://github.com/jaredatch/Custom-Metaboxes-and-Fields-for-WordPress/wiki">metabox wiki</a> and <a href="https://github.com/jaredatch/Custom-Metaboxes-and-Fields-for-WordPress/blob/master/example-functions.php">example file</a> for details</li>
</ul>

<p>And of course the theme can be completely customized through the theme files. All future functionality will come through plugin updates, so you are free to tweak the theme.</p>';
		
		
		// Build the Screen
		$screen = get_current_screen();
		
		$screen->add_help_tab( array(
			'id'      => 'overview', 
			'title'   => 'Overview',
			'content' => $overview,
		) );
		
		$screen->add_help_tab( array(
			'id'      => 'event_information',
			'title'   => 'Event Information',
			'content' => $event_information,
		) );
		
		$screen->add_help_tab( array(
			'id'      => 'menu',
			'title'   => 'Menu',
			'content' => $menu,
		) );
		
		$screen->add_help_tab( array(
			'id'      => 'homepage',
			'title'   => 'Homepage',
			'content' => $homepage,
		) );
		
		$screen->add_help_tab( array( 
			'id'      => 'speakers',
			'title'   => 'Speakers',
			'content' => $speakers,
		));
		
		$screen->add_help_tab( array(
			'id'      => 'schedule',
			'title'   => 'Schedule',
			'content' => $schedule
		));
		
		$screen->add_help_tab( array( 
			'id'      => 'faq',
			'title'   => 'FAQ',
			'content' => $faq,
		));
		
		$screen->add_help_tab( array( 
			'id'      => 'registration',
			'title'   => 'Registration',
			'content' => $registration,
		));
		
		$screen->add_help_tab( array( 
			'id'      => 'contact',
			'title'   => 'Contact',
			'content' => $contact,
		));
		
		$screen->add_help_tab( array( 
			'id'      => 'content-columns',
			'title'   => 'Content Columns',
			'content' => $content_columns,
		));
		
		$screen->add_help_tab( array( 
			'id'      => 'advanced-customization',
			'title'   => 'Advanced Customization',
			'content' => $advanced_customization,
		));
	}

}

new Social_Coup_Functionality;

// Widgets
require_once( SC_DIR . '/lib/widgets/widget-speakers.php' );
require_once( SC_DIR . '/lib/widgets/widget-connect.php' );

// Shortcodes
require_once( SC_DIR . '/lib/functions/shortcodes.php' );