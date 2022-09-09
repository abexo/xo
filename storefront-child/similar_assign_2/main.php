<?php
/**
 * Plugin Name:       RS Connector
 * Plugin URI:        none
 * Description:       Pulls Data from Realstatus CRM
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Panagiotis Spyropoulos
 * Author URI:        https://www.linkedin.com/in/panagioths-spyropoulos-157b97133/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       rs_connector
 * Domain Path:       /languages
 */
remove_action( 'shutdown', 'wp_ob_end_flush_all', 1 );
add_action( 'shutdown', function() {
   while ( @ob_end_flush() );
} );
// =============================================================================
// Recursively delete a directory that is not empty
// =============================================================================

function rrmdir($dir) {
	if (is_dir($dir)) {
		$objects = scandir($dir);
			foreach ($objects as $object) {
				if ($object != "." && $object != "..") {
					if (filetype($dir."/".$object) == "dir") rrmdir($dir."/".$object); else unlink($dir."/".$object);
				}
			}
		reset($objects);
		rmdir($dir);
	}
}

// =============================================================================
// Include Menu Page
// =============================================================================

include( plugin_dir_path( __FILE__ ) . './pages/rs-connector/index.php');

function rs_option_panel(){
	add_menu_page(
        'RS Connector',
        'RS Connector', 
        'manage_options', 
        'rs_connector', 
        'rs_connector', 
        'dashicons-database-import'
    );
}

add_action('admin_menu', 'rs_option_panel');

// =============================================================================
// Register the Property AD Custom Post Type
// =============================================================================

function register_property_ads_post_type() {

	$labels = array(
		'name'                  => _x( 'Property Ads', 'Post Type General Name', 'rs_text' ),
		'singular_name'         => _x( 'Property Ad', 'Post Type Singular Name', 'rs_text' ),
		'menu_name'             => __( 'Property Ads', 'rs_text' ),
		'name_admin_bar'        => __( 'Property Ad', 'rs_text' ),
		'archives'              => __( 'Item Archives', 'rs_text' ),
		'attributes'            => __( 'Item Attributes', 'rs_text' ),
		'parent_item_colon'     => __( 'Parent Item:', 'rs_text' ),
		'all_items'             => __( 'All Items', 'rs_text' ),
		'add_new_item'          => __( 'Add New Item', 'rs_text' ),
		'add_new'               => __( 'Add New', 'rs_text' ),
		'new_item'              => __( 'New Item', 'rs_text' ),
		'edit_item'             => __( 'Edit Item', 'rs_text' ),
		'update_item'           => __( 'Update Item', 'rs_text' ),
		'view_item'             => __( 'View Item', 'rs_text' ),
		'view_items'            => __( 'View Items', 'rs_text' ),
		'search_items'          => __( 'Search Item', 'rs_text' ),
		'not_found'             => __( 'Not found', 'rs_text' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'rs_text' ),
		'featured_image'        => __( 'Featured Image', 'rs_text' ),
		'set_featured_image'    => __( 'Set featured image', 'rs_text' ),
		'remove_featured_image' => __( 'Remove featured image', 'rs_text' ),
		'use_featured_image'    => __( 'Use as featured image', 'rs_text' ),
		'insert_into_item'      => __( 'Insert into item', 'rs_text' ),
		'uploaded_to_this_item' => __( 'Uploaded to this item', 'rs_text' ),
		'items_list'            => __( 'Items list', 'rs_text' ),
		'items_list_navigation' => __( 'Items list navigation', 'rs_text' ),
		'filter_items_list'     => __( 'Filter items list', 'rs_text' ),
	);
	$args = array(
		'label'                 => __( 'Property Ad', 'rs_text' ),
		'description'           => __( 'Property Listings', 'rs_text' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'editor', 'thumbnail', 'custom-fields' ),
		'taxonomies'            => array( 'property_type' ),
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'show_in_rest'          => true,
		"supports" =>       array( "title", "editor", "thumbnail", "custom-fields" ),
		'menu_position'         => 5,
		'menu_icon'             => 'dashicons-admin-multisite',
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => true,
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_type'       => 'post',
	);
	register_post_type( 'register_property_ad', $args );

}

add_action( 'init', 'register_property_ads_post_type', 0 );

// =============================================================================
// Register the Taxonomy for the Custom Post Type "Like Category Inside Post Type"
// =============================================================================

function register_property_ad_type_taxonomy() {
	$labels = array(
		'name'              => _x( 'Property Ad Types', 'taxonomy general name' ),
		'singular_name'     => _x( 'Property Ad Type', 'taxonomy singular name' ),
		'search_items'      => __( 'Search Property Ad Types' ),
		'all_items'         => __( 'All Property Ad Types' ),
		'parent_item'       => __( 'Parent Property Ad Type' ),
		'parent_item_colon' => __( 'Parent Property Ad Type:' ),
		'edit_item'         => __( 'Edit Property Ad Type' ),
		'update_item'       => __( 'Update Property Ad Type' ),
		'add_new_item'      => __( 'Add New Property Ad Type' ),
		'new_item_name'     => __( 'New Property Ad Type' ),
		'menu_name'         => __( 'Property Ad Types' ),
	);
	$args   = array(
		'hierarchical'      => true,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => [ 'slug' => 'property_ad_type' ],
	);
	register_taxonomy( 'property_ad_type', [ 'register_property_ad' ], $args );

	// =========================================================================
	// Insert the Default Values Inside the Taxonomy
	// =========================================================================

	wp_insert_term('Sale', 'property_ad_type');
	wp_insert_term('Rent', 'property_ad_type');
}

add_action( 'init', 'register_property_ad_type_taxonomy' );

// =============================================================================
// Load the XML File 
// =============================================================================

$xml = simplexml_load_file('XML_INPUT_URL');

// =============================================================================
// Post Maker Returns the Post id that Created
// =============================================================================

function pre_save_post($postTitle, $postContent) {
    
    $post = array (
        'post_title'    => $postTitle,
        'post_content'  => $postContent,
        'post_status'   => 'publish',
        'post_author'   => 1,
        'post_type'     => 'register_property_ad',
    );

    $post_id = wp_insert_post( $post );

    return $post_id;
}

// =============================================================================
// Return if an ad is for Rent or Sale | 31 => Sale | 32 => Rent
// =============================================================================

function getCategory($action){
    $action;
    if($action == 1) {
        return 31;
    } else {
        return 32;
    }
}

function deleteAllProperties() {

	$local_path = str_replace('\\', '/', (plugin_dir_path( __FILE__ ) . '/property_images'));

    $allPosts = get_posts(array(
      'post_type' => 'register_property_ad',
      'numberposts' => -1
    ));
    
	// =========================================================================
	// Loop throughout all ids and delete all post plus the custom metas added
	// =========================================================================

    foreach ($allPosts as &$singlePost) {
		$postMeta = get_post_meta($singlePost->ID);
		foreach( $postMeta as $key=>$val ) {
			$postID = (int)$singlePost->ID;
			$keyName = (string)$key;
			delete_post_meta($postID, $keyName);
		}
	  wp_delete_post($singlePost->ID);
	}
	
	// =========================================================================
	// Remove local directory for property image photos if not empty
	// =========================================================================

	if (file_exists($local_path)) {
		rrmdir($local_path);
	}

  }
  


function rsConnectorCron( $schedules ) {
    $schedules['every_12_hours'] = array(
        'interval' => 43200, 
        'display'  => __( 'Every 12 hours' ),
    );
    return $schedules;
}

add_filter( 'cron_schedules', 'rsConnectorCron' );

// =============================================================================
// Schedule an action if it's not already scheduled
// =============================================================================

if ( ! wp_next_scheduled( 'rsConnectorCronHook' ) ) {
    wp_schedule_event( time(), 'every_12_hours', 'rsConnectorCronHook' );
}

// =============================================================================
// Hook into that action that'll fire every 12 hours
// =============================================================================

add_action( 'rsConnectorCronHook', 'rsConnectorImportCron' );

// =============================================================================
// The function, that runs on cron
// =============================================================================
function rsConnectorImportCron() {

		// =====================================================================
		// Import the data to wordpress
		// =====================================================================

		deleteAllProperties();
		$i = 0;
		global $xml;
		
		$local_path = str_replace('\\', '/', (plugin_dir_path( __FILE__ ) . '/property_images'));

		// =====================================================================
		// Check if local image folder exists, else create it
		// =====================================================================

		if (!file_exists($local_path)) {
            mkdir($local_path, 0777, true);
		}
		
		foreach ($xml->ad as $obj) {
		$post_id = pre_save_post(
				(string)$obj[$i]->title_en,
				(string)$obj[$i]->description_en);
				$count = 0; 
			foreach ($xml->ad[$i]->children() as $key) {
				add_post_meta(
					(int)$post_id,
					(string)$key->getName(),
					(string)$key,
					true
				);

				if (substr((string)$key->getName(), 0, 9) == 'photo_url') {
					// =========================================================
					// Check if folder for property with current code exists, if not create it
					// =========================================================
					$dest = str_replace('\\', '/', (string)(plugin_dir_path( __FILE__,2 ) . 'property_images' . '/' . (string)$xml->ad[$i]->code[0]));
					if (!file_exists($dest)) {
                        mkdir($dest, 0777, true);
					}
					// =========================================================
					//  Check if current property photo exists or not (can be ignored as photos will be overwritten)
					// =========================================================
					if (!file_exists($dest . '/' . (string)$count . '.png')) {
						$content = file_get_contents($key);
						$image = imagecreatefromstring($content);
						imagepng($image, $dest . '/' . (string)$count . '.png');
                        //file_put_contents($dest . '/' . (string)$count . '.png', $content);
                        $count++;
                    }
				}
				
			}


			// =================================================================
			// Add meta data to current post with local image folder url and number of images inside the folder
			// =================================================================

			$input = (string)$dest;
			$parts = preg_split('@(?=/wp-content)@', $input);


			add_post_meta(
                (int)$post_id,
                'local_image_folder_path',
                $parts[1],
                true
			);   

			add_post_meta(
                (int)$post_id,
                'imagesUploaded',
				$count-1,
                true
			);     
			
			$i++;
		}
	
	
}

// =============================================================================
// Import the CSS/JS Files to Wordpress front-end
// =============================================================================

function rs_connector_css() {  

	wp_enqueue_style( 'properties-bx1', plugin_dir_url( __FILE__ ) . './assets/css/jquery.bxslider.css');
	wp_enqueue_script( 'properties-js2', plugin_dir_url( __FILE__ ) . './assets/js/jquery.bxslider.js' );
	wp_enqueue_script( 'properties-js', plugin_dir_url( __FILE__ ) . './assets/js/font-awesome-kit.js' );
	wp_enqueue_style( 'properties-css', plugin_dir_url( __FILE__ ) . './assets/css/rs-connector.css');

}

add_action('wp_enqueue_scripts', 'rs_connector_css');

// =============================================================================
// Make the Shortcode to be Used Inside Wordpress Pages to Display Properties
// =============================================================================

add_shortcode('lastProperties', 'lastPropertiesShortCode');

function lastPropertiesShortCode() {
    $result = '';
	global $posts;

	$output = '';

	$args = array(
		'post_type' => 'register_property_ad',
		'posts_per_page' => 10,
		'order' => 'ASC'
	);

	$query = new WP_Query($args);

	if($query->have_posts()) :

	while($query->have_posts()) :

		$query->the_post() ;

		$result .= '
		<div class="property-item">
			<div class="property-image"> 
				<a href="' . get_permalink() . '">
					<img style="object-fit: cover; width: 100%; height: 250px;" src="'. plugin_dir_url( __FILE__ ) . 'property_images/' . get_post_meta(get_the_ID(), 'code', true ) .'/0.png">
				</a>                                   
			</div>
			<div class="property-info">
				<div class="property-info-top">
					<div class="propery-name"><a href="' . get_permalink() . '"><h4>' . get_the_title() . '</h4></a></div>
					<div class="property-code"><h2>€' . get_post_meta(get_the_ID(), 'price', true ) . '</h2></div>
				</div>
				<div class="property-code">
					<strong>CODE:</strong> ' . get_post_meta(get_the_ID(), 'code', true) . '
				</div>
				<div class="property-description">
					<p>' . get_the_content() . '</p> 					
				</div>
				<div class="property-extra">
					<div class="extra"><i class="fas fa-crop-alt"></i> ' . get_post_meta(get_the_ID(), 'size', true ) . ' sq.m.</div>
					<div class="extra"><i class="fas fa-bed"></i> ' . get_post_meta(get_the_ID(), 'rooms', true ) . ' Bedrooms</div>
					<div class="extra"><i class="fas fa-shower"></i> ' . get_post_meta(get_the_ID(), 'bathroom', true ) . ' Bathroom</div>
				</div>
			</div>
		</div>
	
		';

	endwhile;

	wp_reset_postdata();

	endif;

	return $result;
}

// =============================================================================
// Include Property Post Type to REST API
// =============================================================================


add_action( 'rest_api_init', 'create_api_posts_meta_field' );

function create_api_posts_meta_field() {

 // register_rest_field ( 'name-of-post-type', 'name-of-field-to-return', array-of-callbacks-and-schema() )
 register_rest_field( 'register_property_ad', 'post_meta_fields', array(
 'get_callback' => 'get_post_meta_for_api',
 'schema' => null,
 )
 );
}

function get_post_meta_for_api( $object ) {
 //get the id of the post object array
 $post_id = $object['id'];

 //return the post meta
 return get_post_meta( $post_id );
}



?>