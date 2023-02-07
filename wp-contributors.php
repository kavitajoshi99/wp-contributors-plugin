<?php
/**
 * Plugin Name: WP Contributors Plugin
 * Description: Creates a Meta-Box for Contibutors(Authors) on Post Page
 * Author: Kavita Joshi
 * Author URI: https://github.com/kavitajoshi99
 * Version: 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// define plugin version. All update to the plugin will be made using this version.
define ('CURRENT_WPCONTIBUTORS_PLUGIN_VERSION', '1.0.0');

// plugin activation
register_activation_hook( __FILE__, 'wpcontributors_activate' );
function wpcontributors_activate() {
    if(!get_option('WPCONTRIBUTORS_PLUGIN_VERSION')) {
        add_option('WPCONTRIBUTORS_PLUGIN_VERSION', CURRENT_WPCONTIBUTORS_PLUGIN_VERSION);
    } else {
        update_option('WPCONTRIBUTORS_PLUGIN_VERSION', CURRENT_WPCONTIBUTORS_PLUGIN_VERSION);
    }
}

add_action("add_meta_boxes", "contributors");
add_action("save_post", "update_wp_contributors_for_post", "10", "3");
add_filter("the_content", 'post_display_wp_contributors');
// plugin deactivation
register_deactivation_hook( __FILE__, 'wpcontributors_deactivate' );
function wpcontributors_deactivate() {
    delete_option('WPCONTRIBUTORS_PLUGIN_VERSION');
}



// Create a Meta Box field for WP-Contributors
function contributors() {
    add_meta_box("wp_contributors", "Contributors", "admin_display_wp_contributors_", "post", "side", "high", null);
}

// Displaying Meta Box in Admin View (Post Page)
function admin_display_wp_contributors_() {
    global $post;
    wp_nonce_field(basename(__FILE__), "wp-contributors-nonce");
    $users = get_users();
    $is_contributor = false;
    $contributor = get_post_meta($post->ID, "wp_contributors", true);
    if ($contributor) {
    	$contributors = explode(',', $contributor);
    }
    if ($users) {
        foreach($users as $wpuser) {
        	if (isset($contributors)) {
	            $is_contributor = in_array($wpuser->ID, $contributors); 
        	}
        	?>
            <div class="contributors">
            	<input type="checkbox" name="wpcontributors[]" value="<?php echo $wpuser->ID; ?>" <?php checked($is_contributor); ?> >
            	<label for="wpcontributors[]" class="checkbox-label-inline"><?php echo $wpuser->user_login; ?>
            	</label>
            </div>
            <?php 
        }
    }
}

// Update/Add WP-Contributors data for Post in Database
function update_wp_contributors_for_post($post_id, $post, $update) {
    if (!isset($_POST["wp-contributors-nonce"]) || !wp_verify_nonce($_POST["wp-contributors-nonce"], basename(__FILE__)))
        return $post_id;

    if(!current_user_can("edit_post", $post_id))
        return $post_id;

    if(defined("DOING_AUTOSAVE") && DOING_AUTOSAVE)
        return $post_id;

    if($post->post_type != 'post')
        return $post_id;

    if(isset($_POST['wpcontributors']))
    {
        $contributors = implode(',', $_POST["wpcontributors"]);
    }   
    update_post_meta($post_id, "wp_contributors", $contributors);
}

// Display Wp-Contributors on Post Page
function post_display_wp_contributors($content) {
	global $post;
    $contributor = get_post_meta($post->ID, "wp_contributors", true);
    if (is_admin()) {
    	return $content;
    }
    if (isset($contributor)) {
    	$authors = explode(',', $contributor);
    	$html = '<div class="container">
				<em>Authors:</em>
					<ul>';
		if (isset($authors)) {
			foreach ($authors as $author) {
	    		$wpuser = get_user_by('id', $author);
	    		$avatar = get_avatar($author, 95);
	    		$author_url = get_author_posts_url($author);
	    		$html .= '<li><a href="'. $author_url . '">'. $avatar . $wpuser->user_login .'</></li>';
    		}
		}
		$html .= '</ul>';
		$html .= '</div>';
		return $content . $html;
	}
}
