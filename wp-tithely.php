<?php

/**

 * @link              http://www.jordesign.com
 * @since             1.0.0
 * @package           Wp_Tithely
 *
 * @wordpress-plugin
 * Plugin Name:       Tithe.ly Giving Button
 * Plugin URI:        http://wpchurch.team/tithely
 * Description:       Tools to insert Tithely giving buttons into your WordPress site
 * Version:           2.3
 * Author:            WP Church Team, Jordan Gillman, 
 * Author URI:        http://www.wpchurch.team
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-tithely
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

//Register the Tithely script
function wp_tithely_enqueue_script() {
	wp_register_script( "tithely", 'https://tithe.ly/widget/v3/give.js?3', array(), '1.0.0', true );
}

add_action( 'wp_enqueue_scripts', 'wp_tithely_enqueue_script' );


//Options Screen to load Tithely Church ID & Button Text
class TithelyOptions {
	private $tithely_options_options;

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'tithely_options_add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'tithely_options_page_init' ) );
	}

	public function tithely_options_add_plugin_page() {
		add_options_page(
			'Tithe.ly Giving Options', // page_title
			'Tithe.ly Giving Options', // menu_title
			'manage_options', // capability
			'tithely-options', // menu_slug
			array( $this, 'tithely_options_create_admin_page' ) // function
		);
	}

	public function tithely_options_create_admin_page() {
		$this->tithely_options_options = get_option( 'tithely_options_option_name' ); ?>

		<div class="wrap">
			<h2>Tithe.ly Giving Options</h2>
			<p>Enter your Tithely Church ID and the default text you would like for the button.</p>
			<?php settings_errors(); ?>

			<form method="post" action="options.php">
				<?php
					settings_fields( 'tithely_options_option_group' );
					do_settings_sections( 'tithely-options-admin' );
					submit_button();
				?>
			</form>

			<h2>Usage</h2>
			<p>There are two ways you can insert your button.</p>

			<p>1. Insert the WP Tithely Widget into one of your sidebars. You can insert your Church ID and the text you would like the button to display.</p>

			<p>2. You can insert the button into your page content using the shortcode <code>[tithely]</code>. It will use the Church ID and Button Text defined in the options above, unless you include the additional attributes outlined below: <code>[tithely button="Donate Now" id="12345" amount="100" styling_class="button" give-to="Building Fund"]</code></p>.

			<h2>Shortcode Attributes</h2>
			<p><strong><code>button</code></strong> <br>

				The text that will appear on the button
			</p>
			<p><strong><code>id</code></strong> <br>
				Your Tithe.ly Church ID
			</p>
			<p><strong><code>styling_class</code></strong>  <br>
				Optionally include a CSS class (or classes) for styling
			</p>
			<p><strong><code>amount</code></strong>  <br>
				Optionally set a specific amount for the button
			</p>
			<p><strong><code>giving-to</code></strong> <br>
				Optionally set a specific purpose the money will be given to
			</p>

			<h2>Embed Form</h2>
			<p>You may use the [tithely_embed] shortcode to embed an iframe giving form directly in your page or post content.</p>
			<p><strong>IMPORTANT:</strong> You <em>must</em> have an SSL certificate running on your site to use this feature. You can use the <a href="https://www.sslshopper.com/ssl-checker.html" target="_blank">SSL Checker</a> to verify that your site has an SSL certificate installed.</p>
			<p>Example: <code>[tithely_embed id="12345" class="class1 class2" width="100%" height="770px"]</code></p>
			<h3>Embed Shortcode Attributes</h3>
			<p><strong>id</strong> <br>
				Your Tithe.ly Church ID
			</p>
			<p><strong>class</strong> <br>
				CSS classnames to add to the iframe
			</p>
			<p><strong>width</strong> <br>
				CSS width of iframe (include units, eg: 100%)
			</p>
			<p><strong>height</strong> <br>
				CSS height of iframe (include units, eg: 770px)
			</p>
		</div>
	<?php }

	public function tithely_options_page_init() {
		register_setting(
			'tithely_options_option_group', // option_group
			'tithely_options_option_name', // option_name
			array( $this, 'tithely_options_sanitize' ) // sanitize_callback
		);

		add_settings_section(
			'tithely_options_setting_section', // id
			'Settings', // title
			array( $this, 'tithely_options_section_info' ), // callback
			'tithely-options-admin' // page
		);

		add_settings_field(
			'tithely_church_id_0', // id
			'Tithely Church ID', // title
			array( $this, 'tithely_church_id_0_callback' ), // callback
			'tithely-options-admin', // page
			'tithely_options_setting_section' // section
		);

		add_settings_field(
			'default_button_text_1', // id
			'Default Button Text', // title
			array( $this, 'default_button_text_1_callback' ), // callback
			'tithely-options-admin', // page
			'tithely_options_setting_section' // section
		);

		add_settings_field(
			'styling_class_2', // id
			'CSS class for styling', // title
			array( $this, 'styling_class_2_callback' ), // callback
			'tithely-options-admin', // page
			'tithely_options_setting_section' // section
		);
	}

	public function tithely_options_sanitize($input) {
		$sanitary_values = array();
		if ( isset( $input['tithely_church_id_0'] ) ) {
			$sanitary_values['tithely_church_id_0'] = sanitize_text_field( $input['tithely_church_id_0'] );
		}

		if ( isset( $input['default_button_text_1'] ) ) {
			$sanitary_values['default_button_text_1'] = sanitize_text_field( $input['default_button_text_1'] );
		}

		if ( isset( $input['styling_class_2'] ) ) {
			$sanitary_values['styling_class_2'] = sanitize_text_field( $input['styling_class_2'] );
		}

		return $sanitary_values;
	}

	public function tithely_options_section_info() {
		
	}

	public function tithely_church_id_0_callback() {
		printf(
			'<input class="regular-text" type="text" name="tithely_options_option_name[tithely_church_id_0]" id="tithely_church_id_0" value="%s">',
			isset( $this->tithely_options_options['tithely_church_id_0'] ) ? esc_attr( $this->tithely_options_options['tithely_church_id_0']) : ''
		);
	}

	public function default_button_text_1_callback() {
		printf(
			'<input class="regular-text" type="text" name="tithely_options_option_name[default_button_text_1]" id="default_button_text_1" value="%s">',
			isset( $this->tithely_options_options['default_button_text_1'] ) ? esc_attr( $this->tithely_options_options['default_button_text_1']) : ''
		);
	}

	public function styling_class_2_callback() {
		printf(
			'<input class="regular-text" type="text" name="tithely_options_option_name[styling_class_2]" id="styling_class_2" value="%s">
			<p><small>Add any CSS classes your theme uses for styling buttons</small></p>',
			isset( $this->tithely_options_options['styling_class_2'] ) ? esc_attr( $this->tithely_options_options['styling_class_2']) : ''
		);
	}

}
if ( is_admin() )
	$tithely_options = new TithelyOptions();

/* 
 * Retrieve this value with:
 * $tithely_options_options = get_option( 'tithely_options_option_name' ); // Array of All Options
 * $tithely_church_id_0 = $tithely_options_options['tithely_church_id_0']; // Tithely Church ID
 * $default_button_text_1 = $tithely_options_options['default_button_text_1']; // Default Button Text
 */

	


// Define Tithely Shortcode
function wp_tithely_button( $atts) {

	//Enqueue script
		wp_enqueue_script( 'tithely' );
		wp_add_inline_script( "tithely", "var tw = create_tithely_widget();" );


	//Pull in the site options
		$options = get_option( 'tithely_options_option_name' ); 

	//Get Tithely Church ID
		$tithely_church_id = esc_html($options['tithely_church_id_0']);

	//Get the Button Text
		$tithely_button_text = esc_html($options['default_button_text_1']);

	//Get the Styling Class
		$tithely_styling_class = esc_html($options['styling_class_2']);

	// Attributes
	$shortcode_atts =  shortcode_atts(
		array(
			'button' => $tithely_button_text,
			'id' => $tithely_church_id,
			'class' => $tithely_styling_class,
			'amount'=> '',
			'giving-to' =>'',
		), $atts );

	if ($shortcode_atts['id'] !='') {
		$tithely_church_id = esc_html($shortcode_atts['id']);
	}

    // Code

    if ($tithely_church_id!=''){
    	 $buttonCode =  '<button class="tithely-give-btn btn btn-primary et_pb_button fl-button elementor-button ' . $shortcode_atts['class'] . '" data-church-id="' . $tithely_church_id . '"' ;
    	 if($tithely_amount = $shortcode_atts['amount']){
    	 	$buttonCode .= 'data-amount="' . $tithely_amount . '"' ;
    	 }
    	 if($tithely_giving_to = $shortcode_atts['giving-to']){
    	 	$buttonCode .= 'data-giving-to="' . $tithely_giving_to . '"' ;
    	 }
    	 $buttonCode .='>' . $shortcode_atts['button'] . '</button>
    	 		<script src="https://tithe.ly/widget/v3/give.js?3"></script>
		    <script>
			  var tw = create_tithely_widget();
			</script>';
	return $buttonCode;
    }else{
    	return $tithely_church_id;
    }
   
}
add_shortcode( 'tithely', 'wp_tithely_button' );

function wp_tithely_iframe( $atts ) {

	//Enqueue script
		wp_enqueue_script( 'tithely' );
		wp_add_inline_script( "tithely", "var tw = create_tithely_widget();" );

	//Pull in the site options
	$options = get_option( 'tithely_options_option_name' ); 

	//Get Tithely Church ID
	$tithely_church_id = $options['tithely_church_id_0'];

	// Attributes
	$shortcode_atts = shortcode_atts(
		array(
			'id' => $tithely_church_id,
			'class' => 'tithely-iframe', // class to apply to the iframe for styling
			'width' => '100%', // CSS width to apply to iframe
			'height' => '770px', // CSS height to apply to iframe
		), 
		$atts 
	);

	if ( $shortcode_atts['id'] != '' ) {

		// Generate the iframe output
		$iframe = sprintf( '<iframe id="tithely-web-widget-wrapper-embed" class="%s" style="overflow: hidden; opacity: 1;" src="https://tithe.ly/give_new/www/#/tithely/give-one-time/%s" width="%s" height="%s" frameborder="0"></iframe>', $shortcode_atts['class'], $shortcode_atts['id'], $shortcode_atts['width'], $shortcode_atts['height'] );

		return $iframe;
 
 	} else {

 		return $tithely_church_id;

	}
}
add_shortcode( 'tithely_embed', 'wp_tithely_iframe' );

//Tithely Widget
// Creating the widget 
	class wp_tithely_widget extends WP_Widget {

	function __construct() {
	parent::__construct(
	// Base ID of your widget
	'wp_tithely_widget', 

	// Widget name will appear in UI
	__('Tithely Widget', 'wp_tithely_domain'), 

	// Widget description
	array( 'description' => __( 'Insert a Tithely "Give" Button"', 'w[_tithely_domain' ), ) 
	);
	}

	// Creating widget front-end
	// This is where the action happens
	public function widget( $args, $instance ) {
	// before and after widget arguments are defined by themes
	echo $args['before_widget'];

	// This is where you run the code and display the output
	// Code

	//Enqueue script
		wp_enqueue_script( 'tithely' );
		wp_add_inline_script( "tithely", "var tw = create_tithely_widget();" );


	//Get Tithely Church ID
		$tithely_church_id = esc_html($instance['tithely_church_id']);

	//Get Styling Class
		$tithely_styling_class = esc_html($instance['tithely_styling_class']);

	//Get Amount
		$tithely_amount = esc_html($instance['tithely_amount']);

	//Get Styling Class
		$tithely_giving_to = esc_html($instance['tithely_giving_to']);

	//Get the Button Text
		$tithely_button_text = $instance['title'];
		if ($tithely_button_text =='') {
			$tithely_button_text = 'Give';
		}

    if ($tithely_church_id!=''){
    	 echo '<button class="tithely-give-btn btn btn-primary et_pb_button fl-button elementor-button ' . $tithely_styling_class . '" data-church-id="' . $tithely_church_id . '" data-amount="' . $tithely_amount . '" data-giving-to="' . $tithely_giving_to . '">' . $tithely_button_text . '</button>';
    }else{
    	echo $tithely_church_id;
    }



	echo $args['after_widget'];
	}
			
	// Widget Backend 
	public function form( $instance ) {
	if ( isset( $instance[ 'title' ] ) ) {
	$title = $instance[ 'title' ];
	}
	else {
	$title = __( 'Give', 'wp_tithely_domain' );
	}
	if ( isset( $instance[ 'tithely_church_id' ] ) ) {
	$tithely_church_id = $instance[ 'tithely_church_id' ];
	}
	else {
	$tithely_church_id = '';
	}
	if ( isset( $instance[ 'tithely_styling_class' ] ) ) {
	$tithely_styling_class = $instance[ 'tithely_styling_class' ];
	}
	else {
	$tithely_styling_class = '';
	}
	if ( isset( $instance[ 'tithely_amount' ] ) ) {
	$tithely_amount = $instance[ 'tithely_amount' ];
	}
	else {
	$tithely_amount = '';
	}
	if ( isset( $instance[ 'tithely_giving_to' ] ) ) {
	$tithely_giving_to = $instance[ 'tithely_giving_to' ];
	}
	else {
	$tithely_giving_to = '';
	}
	// Widget admin form
	?>
	<p>
	<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Button Text:' ); ?></label> 
	<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
	</p>
	<p>
	<label for="<?php echo $this->get_field_id( 'tithely_church_id' ); ?>"><?php _e( 'Tithely Church ID:' ); ?></label> 
	<input class="widefat" id="<?php echo $this->get_field_id( 'tithely_church_id' ); ?>" name="<?php echo $this->get_field_name( 'tithely_church_id' ); ?>" type="text" value="<?php echo esc_attr( $tithely_church_id ); ?>" />
	</p>
	<p>
	<label for="<?php echo $this->get_field_id( 'tithely_styling_class' ); ?>"><?php _e( 'Styling Class:' ); ?></label> 
	<input class="widefat" id="<?php echo $this->get_field_id( 'tithely_styling_class' ); ?>" name="<?php echo $this->get_field_name( 'tithely_styling_class' ); ?>" type="text" value="<?php echo esc_attr( $tithely_styling_class ); ?>" />
	<small>Add any CSS classes your theme uses for styling buttons</small>
	</p>
	<p>
	<label for="<?php echo $this->get_field_id( 'tithely_amount' ); ?>"><?php _e( 'Amount to Give:' ); ?></label> 
	<input class="widefat" id="<?php echo $this->get_field_id( 'tithely_amount' ); ?>" name="<?php echo $this->get_field_name( 'tithely_amount' ); ?>" type="text" value="<?php echo esc_attr( $tithely_amount ); ?>" />
	<small>Use this to set a specific amount for this button</small>
	</p>
	<p>
	<label for="<?php echo $this->get_field_id( 'tithely_giving_to' ); ?>"><?php _e( 'Give To:' ); ?></label> 
	<input class="widefat" id="<?php echo $this->get_field_id( 'tithely_giving_to' ); ?>" name="<?php echo $this->get_field_name( 'tithely_giving_to' ); ?>" type="text" value="<?php echo esc_attr( $tithely_giving_to ); ?>" />
	<small>Use this if this button is to give to a specific purpose</small>
	</p>
	<?php 
	}
		
	// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {
	$instance = array();
	$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
	$instance['tithely_church_id'] = ( ! empty( $new_instance['tithely_church_id'] ) ) ? strip_tags( $new_instance['tithely_church_id'] ) : '';
	$instance['tithely_styling_class'] = ( ! empty( $new_instance['tithely_styling_class'] ) ) ? strip_tags( $new_instance['tithely_styling_class'] ) : '';
	$instance['tithely_amount'] = ( ! empty( $new_instance['tithely_amount'] ) ) ? strip_tags( $new_instance['tithely_amount'] ) : '';
	$instance['tithely_giving_to'] = ( ! empty( $new_instance['tithely_giving_to'] ) ) ? strip_tags( $new_instance['tithely_giving_to'] ) : '';
	return $instance;
	}
	} // Class wpb_widget ends here

	// Register and load the widget
	function wp_tithely_load_widget() {
		register_widget( 'wp_tithely_widget' );
	}
	add_action( 'widgets_init', 'wp_tithely_load_widget' );
