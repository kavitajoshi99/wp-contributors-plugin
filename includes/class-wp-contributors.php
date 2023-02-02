<?php

class Wp_Contributors {

    public function __construct() {
        add_action("add_meta_boxes", "wp_contributors");
        add_action("save_post", "save_wp_contributors", "10", "3");
    }

    // Create a Meta Box field for WP-Contributors
    public function wp_contributors() {
        add_meta_box("wp-contributors", "Contributors", "display_wp_contributors", "post", "side", "high", null);
    }

    public function display_wp_contributors($data) {
        global $post;
        wp_nonce_field(basename(__FILE__), "wp-contributors-nonce");
        $users = get_users();
        $contributor = get_post_meta($post->ID, "wp-contributors", true);
        $contributors = explode(',', $contributor);
        foreach($users as $wpuser) { 
            $is_contributor = in_array($wpuser->ID, $contributors); ?>
            <input type="checkbox" name="wpcontributors[]" value="<?php echo $wpuser->ID; ?>" <?php checked($is_contributor); ?> >
            <label for="wpcontributors[]" class="checkbox-label-inline"><?php echo $wpuser->user_login; ?>
            </label><br>
            <?php 
        }
    }

    // Update WP-Contributors data for Post 
    public function update_wp_contributors_for_post($post_id, $post, $update) {
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
        update_post_meta($post_id, "wp-contributors", $contributors);
    }
}
