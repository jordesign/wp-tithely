<?php

/**

 * @link              http://www.jordesign.com
 * @since             1.0.0
 * @package           Wp_Tithely
 *
 * @wordpress-plugin
 * Plugin Name:       WP Tithely
 * Plugin URI:        http://www.jordesign.com
 * Description:       Tools to insert Tithely giving buttons into your WordPress site
 * Version:           1.5
 * Author:            Jordan Gillman
 * Author URI:        http://www.jordesign.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-tithely
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

//Load Tithely Javascript
function wp_tithely_scripts() {
	wp_enqueue_script( 'tithely', 'https://tithe.ly/widget/give.js?3', array('jquery'), '1.0.0', false );
}

add_action( 'wp_enqueue_scripts', 'wp_tithely_scripts' );


//Options Screen to load Tithely Church ID & Button Text
class TithelyOptions {
	private $tithely_options_options;

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'tithely_options_add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'tithely_options_page_init' ) );
	}

	public function tithely_options_add_plugin_page() {
		add_options_page(
			'Tithely Options', // page_title
			'Tithely Options', // menu_title
			'manage_options', // capability
			'tithely-options', // menu_slug
			array( $this, 'tithely_options_create_admin_page' ) // function
		);
	}

	public function tithely_options_create_admin_page() {
		$this->tithely_options_options = get_option( 'tithely_options_option_name' ); ?>

		<div class="wrap">
			<h2>Tithely Options</h2>
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
			<p>2. You can insert the button into your page content using the shortcode [tithely]. It will use the Church ID and Button Text defined in the options above, unless you include a new phrase or ID like: [tithely button="Donate Now" id="12345"]</p>.
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
	}

	public function tithely_options_sanitize($input) {
		$sanitary_values = array();
		if ( isset( $input['tithely_church_id_0'] ) ) {
			$sanitary_values['tithely_church_id_0'] = sanitize_text_field( $input['tithely_church_id_0'] );
		}

		if ( isset( $input['default_button_text_1'] ) ) {
			$sanitary_values['default_button_text_1'] = sanitize_text_field( $input['default_button_text_1'] );
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

	//Pull in the site options
		$options = get_option( 'tithely_options_option_name' ); 

	//Get Tithely Church ID
		$tithely_church_id = $options['tithely_church_id_0'];

	//Get the Button Text
		$tithely_button_text = $options['default_button_text_1'];

	// Attributes
	$shortcode_atts =  shortcode_atts(
		array(
			'button' => $tithely_button_text,
			'id' => $tithely_church_sc_id,
		), $atts );

	if ($shortcode_atts['id'] !='') {
		$tithely_church_id = $shortcode_atts['id'];
	}

    // Code
    if ($tithely_church_id!=''){
    	 return '<button class="tithely-give-btn button tithely-button" data-church-id="' . $tithely_church_id . '">' . $shortcode_atts['button'] . '</button>
		    <script>
			  var config = {};
			  var tw = create_tithely_widget(config);
			</script>';
    }else{
    	return $tithely_church_id;
    }
   
}
add_shortcode( 'tithely', 'wp_tithely_button' );

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

	//Get Tithely Church ID
		$tithely_church_id = $instance['tithely_church_id'];

	//Get the Button Text
		$tithely_button_text = $instance['title'];
		if ($tithely_button_text =='') {
			$tithely_button_text = 'Give';
		}

    if ($tithely_church_id!=''){
    	 echo '<button class="tithely-give-btn button tithely-button" data-church-id=" data-church-id="' . $tithely_church_id . '">' . $tithely_button_text . '</button>
		    <script>
			  var config = {};
			  var tw = create_tithely_widget(config);
			</script>';
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
	<?php 
	}
		
	// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {
	$instance = array();
	$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
	$instance['tithely_church_id'] = ( ! empty( $new_instance['tithely_church_id'] ) ) ? strip_tags( $new_instance['tithely_church_id'] ) : '';
	return $instance;
	}
	} // Class wpb_widget ends here

	// Register and load the widget
	function wp_tithely_load_widget() {
		register_widget( 'wp_tithely_widget' );
	}
	add_action( 'widgets_init', 'wp_tithely_load_widget' );

	//Add Button to TinyMCE for Shortcode

add_action('media_buttons_context','add_my_tinymce_media_button');
function add_my_tinymce_media_button($context){

return $context.=__("
<a href=\"#TB_inline?width=480&inlineId=my_shortcode_popup&width=640&height=513\" class=\"button thickbox\" id=\"my_shortcode_popup_button\" title=\"Add My Shortcode\">Add My Shortcode</a>");
}

add_action('admin_footer','my_shortcode_media_button_popup');
//Generate inline content for the popup window when the "my shortcode" button is clicked
function my_shortcode_media_button_popup(){?>
  <div id="my_shortcode_popup" style="display:none;">
    <--".wrap" class div is needed to make thickbox content look good-->
    <div class="wrap">
      <div>
        <h2>Insert My Shortcode</h2>
        <div class="my_shortcode_add">
          <input type="text" id="id_of_textbox_user_typed_in"><button class="button-primary" id="id_of_button_clicked">Add Shortcode</button>
        </div>
      </div>
    </div>
  </div>
<?php
}

//javascript code needed to make shortcode appear in TinyMCE edtor
add_action('admin_footer','my_shortcode_add_shortcode_to_editor');
function my_shortcode_add_shortcode_to_editor(){?>
<script>
jQuery('#id_of_button_clicked ').on('click',function(){
  var user_content = jQuery('#id_of_textbox_user_typed_in').val();
  var shortcode = '[my_shortcode attributes="'+user_content+'"/]';
  if( !tinyMCE.activeEditor || tinyMCE.activeEditor.isHidden()) {
    jQuery('textarea#content').val(shortcode);
  } else {
    tinyMCE.execCommand('mceInsertContent', false, shortcode);
  }
  //close the thickbox after adding shortcode to editor
  self.parent.tb_remove();
});
</script>
<?php
}