<?php
/**
Plugin Name: WordCamp NYC Badge
Plugin URI: http://sudarmuthu.com/wordpress/wordcamp-nyc-badge
Description: Helps you to display the <a href = "http://2009.newyork.wordcamp.org/badges/">WordCamp NYC Badge</a>.
Author: Sudar
Version: 0.1
Author URI: http://sudarmuthu.com/
Text Domain: wordcamp-nyc-badge

=== RELEASE NOTES ===
2009-10-19 – v0.1 – Initial Release
*/

// Define some code

define(ATTENDING_BIG, '<a href="http://2009.newyork.wordcamp.org"  title="WordCampNYC – Nov 14-15"><img alt="WordCampNYC – Nov 14-15" src="http://2009.newyork.wordcamp.org/files/2009/10/wcnyc-attending-250.jpg" /></a>');
define(ATTENDING_SMALL, '<a href="http://2009.newyork.wordcamp.org"  title="WordCampNYC – Nov 14-15"><img alt="WordCampNYC – Nov 14-15" src="http://2009.newyork.wordcamp.org/files/2009/10/wcnyc-attending-125.jpg" /></a>');
define(SPONSOR_BIG, '<a href="http://2009.newyork.wordcamp.org"  title="WordCampNYC – Nov 14-15"><img alt="WordCampNYC – Nov 14-15" src="http://2009.newyork.wordcamp.org/files/2009/10/wcnyc-sponsor-250.jpg" /></a>');
define(SPONSOR_SMALL, '<a href="http://2009.newyork.wordcamp.org"  title="WordCampNYC – Nov 14-15"><img alt="WordCampNYC – Nov 14-15" src="http://2009.newyork.wordcamp.org/files/2009/10/wcnyc-sponsor-125.jpg" /></a>');
define(SPEAKING_BIG, '<a href="http://2009.newyork.wordcamp.org"  title="WordCampNYC – Nov 14-15"><img alt="WordCampNYC – Nov 14-15" src="http://2009.newyork.wordcamp.org/files/2009/10/wcnyc-speaking-250.jpg" /></a>');
define(SPEAKING_SMALL, '<a href="http://2009.newyork.wordcamp.org"  title="WordCampNYC – Nov 14-15"><img alt="WordCampNYC – Nov 14-15" src="http://2009.newyork.wordcamp.org/files/2009/10/wcnyc-speaking-125.jpg" /></a>');
define(WISH_BIG, '<a href="http://2009.newyork.wordcamp.org"  title="WordCampNYC – Nov 14-15"><img alt="WordCampNYC – Nov 14-15" src="http://2009.newyork.wordcamp.org/files/2009/10/wcnyc-wish-250.jpg" /></a>');
define(WISH_SMALL, '<a href="http://2009.newyork.wordcamp.org"  title="WordCampNYC – Nov 14-15"><img alt="WordCampNYC – Nov 14-15" src="http://2009.newyork.wordcamp.org/files/2009/10/wcnyc-wish-125.jpg" /></a>');

class WordcampNYCBbadge {

    /**
     * Initalize the plugin by registering the hooks
     */
    function __construct() {

        // Load localization domain
        // Okay, I am too lazy to enable support for translation. Let me know if anyone really need it.
        // load_plugin_textdomain( 'wordcamp-nyc-badge', false, dirname(plugin_basename(__FILE__)) .  '/languages' );

        // Register hooks
        add_action('admin_head', array(&$this, 'add_script_config'));
    }

    /**
     * add script to admin page
     */
    function add_script_config() {
        // Add script only to Widgets page
        if (substr_compare($_SERVER['REQUEST_URI'], 'widgets.php', -11) == 0) {
    ?>

    <script type="text/javascript">
    // Function to add auto suggest
    var wcnyc_codes = new Array();

    wcnyc_codes['attending_big'] = '<?php echo ATTENDING_BIG; ?>';
    wcnyc_codes['attending_small'] = '<?php echo ATTENDING_SMALL;?>';
    wcnyc_codes['sponsor_big'] = '<?php echo SPONSOR_BIG;?>';
    wcnyc_codes['sponsor_small'] = '<?php echo SPONSOR_SMALL;?>';
    wcnyc_codes['speaking_big'] = '<?php echo SPEAKING_BIG;?>';
    wcnyc_codes['speaking_small'] = '<?php echo SPEAKING_SMALL;?>';
    wcnyc_codes['wish_big'] = '<?php echo WISH_BIG;?>';
    wcnyc_codes['wish_small'] = '<?php echo WISH_SMALL;?>';

    function wcnyc_show_preview(elm) {
        jQuery(elm).parent().nextAll('div.badge_preview').html(wcnyc_codes[jQuery(elm).val()]).show();
    }
    </script>
    <?php
        }
    }

    // PHP4 compatibilityselected
    function WordcampNYCBbadge() {
        $this->__construct();
    }
}

// Start this plugin once all other plugins are fully loaded
add_action( 'init', 'WordcampNYCBbadge' ); function WordcampNYCBbadge() { global $WordcampNYCBbadge; $WordcampNYCBbadge = new WordcampNYCBbadge(); }

// register NYCBadgeWidget widget
add_action('widgets_init', create_function('', 'return register_widget("NYCBadgeWidget");'));

/**
 * NYCBadgeWidget Class
 */
class NYCBadgeWidget extends WP_Widget {
    /** constructor */
    function NYCBadgeWidget() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'NYCBadgeWidget', 'description' => __('Widget that shows WordCamp NYC Badge', 'wordcamp-nyc-badge'));

		/* Widget control settings. */
		$control_ops = array('id_base' => 'wordcamp-nyc-badge' );

		/* Create the widget. */
		parent::WP_Widget( 'wordcamp-nyc-badge', __('WordCamp NYC Badge', 'wordcamp-nyc-badge'), $widget_ops, $control_ops );
    }

    /** @see WP_Widget::widget */
    function widget($args, $instance) {
        extract( $args );

        $title = $instance['title'];
        $badge_type = $instance['badge_type'];

        echo $before_widget;
        echo $before_title;
        echo $title;
        echo $after_title;
        wordcamp_nyc_badge($badge_type);
        echo $after_widget;
    }

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) {
		$instance = $old_instance;
        // validate data
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['badge_type'] = strip_tags($new_instance['badge_type']);

        return $instance;
    }

    /** @see WP_Widget::form */
    function form($instance) {

		/* Set up some default widget settings. */
		$defaults = array( 'title' => '', 'badge_type' => 'attending_small');
		$instance = wp_parse_args( (array) $instance, $defaults );

        $title = esc_attr($instance['title']);
		$badge_type = $instance['badge_type'];
?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'wordcamp-nyc-badge'); ?>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label>
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('badge_type'); ?>"><?php _e('Badge Type:', 'wordcamp-nyc-badge'); ?></label>
            <select id="<?php echo $this->get_field_id('badge_type'); ?>" name="<?php echo $this->get_field_name('badge_type'); ?>" onchange="wcnyc_show_preview(this)" >
                <option value="attending_small" <?php echo selected('attending_small', $badge_type); ?>><?php _e('Attending Small', 'wordcamp-nyc-badge');?></option>
                <option value="attending_big" <?php echo selected('attending_big', $badge_type); ?>><?php _e('Attending Big', 'wordcamp-nyc-badge');?></option>
                <option value="sponsor_small" <?php echo selected('sponsor_small', $badge_type); ?>><?php _e('Sponsor Small', 'wordcamp-nyc-badge');?></option>
                <option value="sponsor_big" <?php echo selected('sponsor_big', $badge_type); ?>><?php _e('Sponsor Big', 'wordcamp-nyc-badge');?></option>
                <option value="speaking_small" <?php echo selected('speaking_small', $badge_type); ?>><?php _e('Speaking Small', 'wordcamp-nyc-badge');?></option>
                <option value="speaking_big" <?php echo selected('speaking_big', $badge_type); ?>><?php _e('Speaking Big', 'wordcamp-nyc-badge');?></option>
                <option value="wish_small" <?php echo selected('wish_small', $badge_type); ?>><?php _e('Wish Small', 'wordcamp-nyc-badge');?></option>
                <option value="wish_big" <?php echo selected('wish_big', $badge_type); ?>><?php _e('Wish Big', 'wordcamp-nyc-badge');?></option>
            </select>
        </p>

        <p><?php _e('Preview', 'wordcamp-nyc-badge'); ?></p>
        <div id="<?php $this->get_field_id('preview_div'); ?>" class="badge_preview">
<?php
            switch ($badge_type) {
                case 'attending_small':
                    echo ATTENDING_SMALL;
                    break;
                case 'attending_big':
                    echo ATTENDING_BIG;
                    break;
                case 'sponsor_small':
                    echo SPONSOR_SMALL;
                    break;
                case 'sponsor_big':
                    echo SPONSOR_BIG;
                    break;
                case 'speaking_small':
                    echo SPEAKING_SMALL;
                    break;
                case 'speaking_big':
                    echo SPEAKING_BIG;
                    break;
                case 'wish_small':
                    echo WISH_SMALL;
                    break;
                case 'wish_big':
                    echo WISH_BIG;
                    break;
                default:
                    echo ATTENDING_SMALL;
                    break;
}
?>
        </div>

<?php
    }
} // class NYCBadgeWidget

/**
 * Template function to display the badge
 * 
 * @param string $badge_type 
 */
function wordcamp_nyc_badge($badge_type) {
?>
        <div class="wcnyc_badge">
<?php
            switch ($badge_type) {
                case 'attending_small':
                    echo ATTENDING_SMALL;
                    break;
                case 'attending_big':
                    echo ATTENDING_BIG;
                    break;
                case 'sponsor_small':
                    echo SPONSOR_SMALL;
                    break;
                case 'sponsor_big':
                    echo SPONSOR_BIG;
                    break;
                case 'speaking_small':
                    echo SPEAKING_SMALL;
                    break;
                case 'speaking_big':
                    echo SPEAKING_BIG;
                    break;
                case 'wish_small':
                    echo WISH_SMALL;
                    break;
                case 'wish_big':
                    echo WISH_BIG;
                    break;
                default:
                    echo ATTENDING_SMALL;
                    break;
        }
?>
    </div>
<?php
}
?>