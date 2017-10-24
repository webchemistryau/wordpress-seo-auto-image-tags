<?php
/**
* PLUGIN SETTINGS PAGE
*/
class SeoImageSettings{
	/**
	 * Holds the values to be used in the fields callbacks
	 */
	private $options;

	/**
	 * Start up
	 */
	public function __construct()
	{
		add_action( 'admin_menu', array( $this, 'add_seo_image_settings_page' ) );
		add_action( 'admin_init', array( $this, 'page_init' ) );
	}

	/**
	 * Add options page
	 */
	public function add_seo_image_settings_page()
	{
		// This page will be under "Settings"add_submenu_page( 'tools.php', 'SEO Image Tags', 'SEO Image Tags', 'manage_options', 'seo_image_tags', 'seo_image_tags_options_page' );

		add_submenu_page(
			'tools.php',
			'SEO Friendly Clean ALT Tags',
			'SEO Friendly Clean ALT Tags',
			'manage_options',
			'seo-friendly-clean-alt-tags',
			array( $this, 'create_seo_image_settings_page' )
		);
	}

	/**
	 * Options page callback
	 */
	public function create_seo_image_settings_page()
	{
		// Set class property
		$this->options = get_option( 'seo_image_option' );
		?>
		<div class="wrap">
			<h1>SEO Friendly Clean ALT Tags Settings</h1>
			<form method="post" action="options.php">

			<?php
				// This prints out all hidden setting fields
				settings_fields( 'seo_image_option_group' );
				do_settings_sections( 'seo-image-settings' );
				submit_button('Update Database');
			?>
			</form>
			
			<p>Developed by <a href="https://webchemistry.com.au" target="_blank"><img src="<?=plugins_url('../img/wc-logo.png',__FILE__)?>" style="vertical-align: middle;" alt="Web Chemistry"></a></p>
			<p>Credits to <a href="http://andrewmgunn.com" target="_blank">Andrew Gunn</a> for original plugin</p>
			
		</div>
		<?php
	}

	/**
	 * Register and add settings
	 */
	public function page_init()
	{
		register_setting(
			'seo_image_option_group', // Option group
			'seo_image_option', // Option name
			array( $this, 'sanitize' ) // Sanitize
		);

		add_settings_section(
			'seo_image_settings_section', // ID
			'Image Tag Database Updater', // Title
			array( $this, 'print_section_info' ), // Callback
			'seo-image-settings' // Page
		);

		add_settings_field(
			'delete_tags', // ID
			'<label for="delete_tags">Delete Image Tags</label>', // Title
			array( $this, 'delete_tags_callback' ), // Callback
			'seo-image-settings', // Page
			'seo_image_settings_section' // Section
		);

		add_settings_field(
			'update_tags', // ID
			'<label for="update_tags">Update Image Tags</label>', // Title
			array( $this, 'update_tags_callback' ), // Callback
			'seo-image-settings', // Page
			'seo_image_settings_section' // Section
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

		if( isset( $input['delete_tags'] ) )
			$new_input['delete_tags'] = absint( $input['delete_tags'] );

		if( isset( $input['update_tags'] ) )
			$new_input['update_tags'] = absint( $input['update_tags'] );

		return $new_input;
	}

	/**
	 * Print the Section text
	 */
	public function print_section_info()
	{
		print '<br/><p style="font-size:14px; margin:0 25% 0 0;"><strong>IMPORTANT:&nbsp;&nbsp;</strong>'.

		'Running this database updater will <i>modify</i> the Alt text fields for images in the database. Any pre-existing images '.
		'that have appropriate Alt tags filled in <b>will not</b> be changed, only ones where the field is blank or less than 2 characters long. '.
		'If you have a lot of pre-existing images without alt text, it is <b>recommended</b> you run the database updater.'.
		'The alt tags will be applied and saved to the database automatically on upload going forward.</p><br/>';
	}
	/**
	 * Get the settings option array and print one of its values
	 */
	public function delete_tags_callback(){
		//Get plugin options
		$options = get_option( 'seo_image_option' );
		$file_counts = null;

		//if update_databae option is checked, run database updater and get count
		//of images updated, then delete option so the script isnt run again, then
		//output success message and count of images update. Else output checkbox
		//var_dump(checked( 1, $options['delete_tags'], false ));

		if (isset($options['delete_tags']) && (!isset($options['update_tags'] ))) {
			$file_counts = batch_update_image_tags(false);
			delete_option( 'seo_image_option' );
		} else if (isset($options['delete_tags']) && (isset($options['update_tags'] ))) {
			$file_counts = batch_update_image_tags(false);
		}
		echo $this->result_count('delete_tags',$file_counts);
	}

	/**
	 * Get the settings option array and print one of its values
	 */
	public function update_tags_callback(){
		//Get plugin options
		$options = get_option( 'seo_image_option' );
		$file_counts = null;

		//if update_databae option is checked, run database updater and get count
		//of images updated, then delete option so the script isnt run again, then
		//output success message and count of images update. Else output checkbox
		if (isset($options['update_tags'] )) {
			$file_counts = batch_update_image_tags(true);
			delete_option( 'seo_image_option' );
		} 
		echo $this->result_count('update_tags',$file_counts);
	}

	private function result_count($opt,$file_counts){
		$html = '';
		if($file_counts){
			$html .= '<div class="seo-image-tags">';
			$html .= '<div class="updated"><h3 style="font-size:14px;">Database update successful!</h3>' .
			'<p style="font-size:14px;">Parsed: <b>'. $file_counts['total'] . '</b> files' .
			'<br/>Created: <b>'. $file_counts['created'] . '</b> tags' .
			'<br/>Updated: <b>' . $file_counts['updated'] . '</b> tags' .
			'<br/>Deleted: <b>' . $file_counts['deleted'] . '</b> tags' .
			'</p></div></div>';
		}
		$html .= '<input type="checkbox" id="'.$opt.'" name="seo_image_option['.$opt.']" value="1"' . checked( 1, $options[$opt], false ) . '/>';
		return $html;
	}
}

if( is_admin() )
	$seo_image_settings = new SeoImageSettings();
