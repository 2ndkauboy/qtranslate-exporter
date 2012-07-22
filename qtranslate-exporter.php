<?php
/*
Plugin Name: qTranslate Exporter
Plugin URI: http://kau-boys.de/
Description: A simple plugin to enable the WordPress Exporter to export a specific qTranslate language with the correct content language.
Version: 1.0
Author: Bernhard Kau
Author URI: http://kau-boys.de
*/

// update the export language into the qTranslate config so that the correct language will be exported 
function qtrans_exporter_change_language(){
	global $q_config;
	$q_config['language'] = get_option( 'qtrans_exporter_export_language', 'en' );
}
add_action( 'export_wp', 'qtrans_exporter_change_language' );

// This is where the magic happens. Just add the two filters so that also the exported text will be changed.
add_filter('the_content_export', 'qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage', 0);
add_filter('the_excerpt_export', 'qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage', 0);


/*
 * ADMIN AREA FUNCTIONS
 */
function qtrans_exporter_init() {
	load_plugin_textdomain( 'qtrans_exporter', false, dirname( plugin_basename( __FILE__ ) ) );
}
add_action( 'init', 'qtrans_exporter_init' );

function qtrans_exporter_filter_plugin_actions( $links, $file ) {
	static $this_plugin;
	if (!$this_plugin) $this_plugin = plugin_basename( __FILE__ );
	
	if ($file == $this_plugin){
		$settings_link = '<a href="options-general.php?page=qtranslate-exporter/qtranslate-exporter.php">'.__( 'Settings' ).'</a>';
		array_unshift($links, $settings_link);
	}
	return $links;
}
add_filter( 'plugin_action_links', 'qtrans_exporter_filter_plugin_actions', 10, 2 );

function qtrans_exporter_admin_menu() {
	add_options_page( 'qTranslate Exporter settings', 'qTranslate Exporter', 8, __FILE__, 'qtrans_exporter_admin_settings' );	
}
add_action( 'admin_menu', 'qtrans_exporter_admin_menu' );

function qtrans_exporter_admin_settings() {
	if(isset($_POST['save'])){
		update_option( 'qtrans_exporter_export_language', $_POST['qtrans_exporter_export_language'] );
		$settings_saved = true;
	}
	$qtranslate_enabled_languages = get_option( 'qtranslate_enabled_languages' );
	$export_language = get_option( 'qtrans_exporter_export_language' );
?>

<div class="wrap">
	<?php screen_icon(); ?>
	<h2><?php _e( 'qTranslate Exporter', 'qtrans_exporter' ) ?></h2>
	<?php if( $settings_saved ) : ?>
	<div id="message" class="updated fade"><p><strong><?php _e( 'Options saved.' ) ?></strong></p></div>
	<?php endif ?>
	<p>
		<?php _e( 'Just choose the language with you want to export.', 'qtrans_exporter' ) ?>
	</p>
	<form method="post" action="">
		<p>
			<label for="qtrans_exporter_export_language"><?php _e( 'Export language', 'qtrans_exporter' ) ?>: </label>
			<select id="qtrans_exporter_export_language" name="qtrans_exporter_export_language">
			<?php foreach( $qtranslate_enabled_languages as $lang ) : ?>
				<option value="<?php echo $lang ?>"<?php echo ($lang == $export_language)? ' selected="selected"' : '' ?>>
					<?php _e( $lang, 'qtrans' ) ?>
				</option>
			<?php endforeach ?>
			</select>
		</p>
		<p class="submit">
			<input class="button-primary" name="save" type="submit" value="<?php _e( 'Save Changes' ) ?>" />
		</p>
	</form>
</div>

<?php
}

?>