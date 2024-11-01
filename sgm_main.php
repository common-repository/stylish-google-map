<?php
/**
 * @internal    never define functions inside callbacks.
 *              these functions could be run multiple times; this would result in a fatal error.
 */

/**
 * custom option and settings
 */
function sgm_settings_init()
{
    // register a new setting for "sgm" page
    register_setting('sgm', 'sgm_options');

    // register a new section in the "sgm" page
    add_settings_section(
        'sgm_section_developers',
        __('Stylish Google Map Options', 'sgm'),
        'sgm_section_developers_cb',
        'sgm'
        );

    // register a new field in the "sgm_section_developers" section, inside the "sgm" page
    add_settings_field(
        'sgm_field_gm_api_key', // as of WP 4.6 this value is used only internally
        // use $args' label_for to populate the id inside the callback
        __('Google Map Api Key', 'sgm'),
        'sgm_field_gm_api_key_cb',
        'sgm',
        'sgm_section_developers',
        [
        'label_for'         => 'sgm_field_gm_api_key',
        'class'             => 'sgm_row',
        'sgm_custom_data' => 'custom',
        ]
        );
    /*add_settings_field(
        'sgm_field_pill', // as of WP 4.6 this value is used only internally
        // use $args' label_for to populate the id inside the callback
        __('Pill', 'sgm'),
        'sgm_field_pill_cb',
        'sgm',
        'sgm_section_developers',
        [
            'label_for'         => 'sgm_field_pill',
            'class'             => 'sgm_row',
            'sgm_custom_data' => 'custom',
        ]
        );*/
    }

/**
 * register our sgm_settings_init to the admin_init action hook
 */
add_action('admin_init', 'sgm_settings_init');

/**
 * custom option and settings:
 * callback functions
 */

// developers section cb

// section callbacks can accept an $args parameter, which is an array.
// $args have the following keys defined: title, id, callback.
// the values are defined at the add_settings_section() function.
function sgm_section_developers_cb($args) {
    ?>
    <p id="<?= esc_attr($args['id']); ?>">Plugin Developed by: <a href="http://khprajapati.com.np" target="_blank">Krishna H. Prajapati</a></p>
    <?php
}

// pill field cb

// field callbacks can accept an $args parameter, which is an array.
// $args is defined at the add_settings_field() function.
// wordpress has magic interaction with the following keys: label_for, class.
// the "label_for" key value is used for the "for" attribute of the <label>.
// the "class" key value is used for the "class" attribute of the <tr> containing the field.
// you can add custom key value pairs to be used inside your callbacks.

/*function sgm_field_pill_cb($args)
{
    // get the value of the setting we've registered with register_setting()
    $options = get_option('sgm_options');
    // output the field
    ?>
    <select id="<?= esc_attr($args['label_for']); ?>"
            data-custom="<?= esc_attr($args['sgm_custom_data']); ?>"
            name="sgm_options[<?= esc_attr($args['label_for']); ?>]"
    >
        <option value="red" <?= isset($options[$args['label_for']]) ? (selected($options[$args['label_for']], 'red', false)) : (''); ?>>
            <?= esc_html('red pill', 'sgm'); ?>
        </option>
        <option value="blue" <?= isset($options[$args['label_for']]) ? (selected($options[$args['label_for']], 'blue', false)) : (''); ?>>
            <?= esc_html('blue pill', 'sgm'); ?>
        </option>
    </select>

    <p class="description">
        <?= esc_html('You take the blue pill and the story ends. You wake in your bed and you believe whatever you want to believe.', 'sgm'); ?>
    </p>
    <p class="description">
        <?= esc_html('You take the red pill and you stay in Wonderland and I show you how deep the rabbit-hole goes.', 'sgm'); ?>
    </p>
    <?php
}
*/

function sgm_field_gm_api_key_cb($args)
{
    // get the value of the setting we've registered with register_setting()
    $options = get_option('sgm_options');
    // output the field
    ?>

    <input id="<?= esc_attr($args['label_for']); ?>" type="text" name="sgm_options[<?= esc_attr($args['label_for']); ?>]" value="<?php echo $options[$args['label_for']]; ?>">

    <p>
        Please Note, All Google Map users must have an API key.<br>
        Get Your Google Map API Key <a href="https://console.developers.google.com/flows/enableapi?apiid=maps_backend,geocoding_backend,directions_backend,distance_matrix_backend,elevation_backend,places_backend&keyType=CLIENT_SIDE&reusekey=true&pli=1" target="_blank">Here</a><br>
        It may take 5-10 minutes to activate the key.
    </p>

    <?php
}

add_action( 'init', 'create_google_maps' );

function create_google_maps() {
    register_post_type( 'stylish-google-map',
        array(
            'labels' => array(
            'name' => 'Stylish Google Maps',
            'singular_name' => 'Stylish Google Map',
            'add_new' => 'Add New',
            'add_new_item' => 'Add New Map',
            'edit' => 'Edit',
            'edit_item' => 'Edit Map',
            'new_item' => 'New Map',
            'view' => 'View',
            'view_item' => 'View Map',
            'search_items' => 'Search Map',
            'not_found' => 'No Mapa found',
            'not_found_in_trash' => 'No Maps found in Trash',
            'parent' => 'Parent Map'
            ),
        'public' => true,
        //'show_in_menu' => false,
        'menu_position' => 100,
        'supports' => array( 'title' ),
        'taxonomies' => array( '' ),
        'menu_icon' => plugins_url( 'images/plugin_icon.png', __FILE__ ),
        'has_archive' => false
        )
    );
}

function theme_options_panel(){
  add_submenu_page('edit.php?post_type=stylish-google-map', __('Stylish Google Map Settings','sgm-settings'), __('SGM Settings','sgm-settings'), 'manage_options', 'sgm', 'sgm_options_page_html');
}
add_action('admin_menu', 'theme_options_panel');


/**
 * register our sgm_options_page to the admin_menu action hook
 */
//add_action('admin_menu', 'sgm_options_page');

/**
 * top level menu:
 * callback functions
 */
function sgm_options_page_html()
{
    // check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }

    // add error/update messages

    // check if the user have submitted the settings
    // wordpress will add the "settings-updated" $_GET parameter to the url
    if (isset($_GET['settings-updated'])) {
        // add settings saved message with the class of "updated"
        add_settings_error('sgm_messages', 'sgm_message', __('Settings Saved', 'sgm'), 'updated');
    }

    // show error/update messages
    settings_errors('sgm_messages');
    ?>
    <div class="wrap">
        <h1><?= esc_html(get_admin_page_title()); ?></h1>
        <form action="options.php" method="post">
            <?php
            // output security fields for the registered setting "sgm"
            settings_fields('sgm');
            // output setting sections and their fields
            // (sections are registered for "sgm", each field is registered to a specific section)
            do_settings_sections('sgm');
            // output save settings button
            submit_button('Save Settings');
            ?>
        </form>
    </div>
    <?php
}






abstract class SGM_Meta_Box
{
    public static function sgm_add()
    {
        add_meta_box(
            'sgm_box_id',               // Unique ID
            'Custom Meta Box Title',    // Box title
            [self::class, 'sgm_html'],  // Content callback, must be of type callable
            'stylish-google-map',       // Post type
            'normal',
            'default'
        );

        add_meta_box(
            'sgm_box_shortcode',
            'Shortcode',
            [self::class, 'sgm_shortcode_metabox_html'],
            'stylish-google-map',
            'normal',
            'high'
        );
    }


    function sgm_shortcode_metabox_html()
    {
        echo '<p>Copy the shortcode below and paste it where you want to display the map.</p><br>';
        echo '[sgm id="'.$_GET['post'].'"]';   
    }

 
    public static function sgm_save($post_id)
    {
        if (array_key_exists('map_style', $_POST)) {
            update_post_meta(
                $post_id,
                'map_style',
                $_POST['map_style']
            );
        }
        if (array_key_exists('lattitude', $_POST)) {
            update_post_meta(
                $post_id,
                'lattitude',
                $_POST['lattitude']
            );
        }
        if (array_key_exists('longitude', $_POST)) {
            update_post_meta(
                $post_id,
                'longitude',
                $_POST['longitude']
            );
        }
        if (array_key_exists('zoom_level', $_POST)) {
            update_post_meta(
                $post_id,
                'zoom_level',
                $_POST['zoom_level']
            );
        }
        if (array_key_exists('map_height', $_POST)) {
            update_post_meta(
                $post_id,
                'map_height',
                $_POST['map_height']
            );
        }
    }

    public static function sgm_html($post)
    {
        $map_style = get_post_meta($post->ID, 'map_style', true);
        $lattitude = get_post_meta($post->ID, 'lattitude', true);
        $longitude = get_post_meta($post->ID, 'longitude', true);
        $zoom_level = get_post_meta($post->ID, 'zoom_level', true);
        $map_height = get_post_meta($post->ID, 'map_height', true);
        ?>
        <div class="sgm_field_row">
            <label for="map_style">Map Style</label>
            <div>
                <select name="map_style" id="map_style" class="postbox">
                    <option value="">Default Style</option>
                    <option value="style-1" <?php selected($map_style, 'style-1'); ?>>Style 1</option>
                    <option value="style-2" <?php selected($map_style, 'style-2'); ?>>Style 2</option>
                    <option value="style-3" <?php selected($map_style, 'style-3'); ?>>Style 3</option>
                    <option value="style-4" <?php selected($map_style, 'style-4'); ?>>Style 4</option>
                    <option value="style-5" <?php selected($map_style, 'style-5'); ?>>Style 5</option>
                </select>
            </div>
        </div>
        <div class="sgm_field_row">
            <label for="sgm_field">Lattitude</label>
            <div>
                <input type="text" name="lattitude" value="<?php echo $lattitude; ?>">
            </div>
        </div>
        <div class="sgm_field_row">
            <label for="sgm_field">Longitude</label>
            <div>
                <input type="text" name="longitude" value="<?php echo $longitude; ?>">
            </div>
        </div>
        <div class="sgm_field_row">
            <label for="sgm_field">Zoom Level</label>
            <div>
                <input type="text" name="zoom_level" value="<?php echo $zoom_level; ?>">
            </div>
        </div>
        <div class="sgm_field_row">
            <label for="sgm_field">Map Height</label>
            <div>
                <input type="text" name="map_height" value="<?php echo $map_height; ?>">
            </div>
        </div>
        <?php
    }
}
 
add_action('add_meta_boxes', ['SGM_Meta_Box', 'sgm_add']);
add_action('save_post', ['SGM_Meta_Box', 'sgm_save']);


function sgm_meta_box_scripts()
{
    // get current admin screen, or null
    $screen = get_current_screen();
    // verify admin screen object
    if (is_object($screen)) {
        // enqueue only for specific post types
        if (in_array($screen->post_type, ['post', 'wporg_cpt'])) {
            // enqueue script
            wp_enqueue_script('sgm_meta_box_script', plugin_dir_url(__FILE__) . 'admin/meta-boxes/js/admin.js', ['jquery']);
            // localize script, create a custom js object
            wp_localize_script(
                'sgm_meta_box_script',
                'sgm_meta_box_obj',
                [
                    'url' => admin_url('admin-ajax.php'),
                ]
            );
        }
    }
}
add_action('admin_enqueue_scripts', 'sgm_meta_box_scripts');


function sgm_meta_box_ajax_handler()
{
    if (isset($_POST['sgm_field_value'])) {
        switch ($_POST['sgm_field_value']) {
            case 'something':
                echo 'success';
                break;
            default:
                echo 'failure';
                break;
        }
    }
    // ajax handlers must die
    die;
}
// wp_ajax_ is the prefix, wporg_ajax_change is the action we've used in client side code
add_action('wp_ajax_sgm_ajax_change', 'sgm_meta_box_ajax_handler');




add_shortcode( 'sgm', 'sgm_shortcode' );
function sgm_shortcode( $atts ) {
    ob_start();

    $options = get_option('sgm_options');
    $gm_api_key = $options['sgm_field_gm_api_key'];

    wp_enqueue_script( 'google-map-api', 'http://maps.google.com/maps/api/js?sensor=true&key='.$gm_api_key, array(), '1.0.0', true );
    // use a template for the output so that it can easily be overridden by theme
    
    // check for template in active theme
    $template = locate_template(array('sgm_template.php'));
    
    // if none found use the default template
    if ( $template == '' ) $template = 'sgm_template.php';
            
    include ( $template );
}