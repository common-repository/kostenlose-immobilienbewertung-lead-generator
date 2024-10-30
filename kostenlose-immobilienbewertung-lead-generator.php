<?php
/**
 * Plugin Name:       Free Real Estate Valuation (Lead Generator)
 * Plugin URI:        https://leadmarkt.ch/
 * Description:       Create real estate valuations / generate leads (form) - LeadMarkt.ch
 * Version:           1.9.0
 * Author:            LeadMarkt.ch
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       kostenlose-immobilienbewertung-lead-generator
 */
function rer_iframe_scripts()
{
    wp_enqueue_style('rer-styles', plugin_dir_url(__FILE__).'dist/rerStyles.css');
    wp_enqueue_script(
        'rer-script',
        plugin_dir_url(__FILE__).'dist/iframeResizer.min.js'
    );
}

add_action('wp_enqueue_scripts', 'rer_iframe_scripts');

function rer_settings_menu()
{
    add_submenu_page($parentMenu = 'options-general.php', __('Real Estate Leads', 'kostenlose-immobilienbewertung-lead-generator'), __('Real Estate Leads', 'kostenlose-immobilienbewertung-lead-generator'), 'administrator', __FILE__, 'rer_generator_settings_page');
    add_action('admin_init', 'register_rer_settings');
}

add_action('admin_menu', 'rer_settings_menu');

function register_rer_settings()
{
    register_setting('rer_generator_group', 'rer_api_token');
}

function rer_generator_settings_page()
{
    ?>
    <div class="wrap">
        <h1><?php _e('Real Estate Valuation - Lead Generator', 'kostenlose-immobilienbewertung-lead-generator') ?></h1>
        <h2><?php _e('By', 'kostenlose-immobilienbewertung-lead-generator') ?> <a href="https://leadmarkt.ch/">LeadMarkt.ch</a>
        </h2>
        <hr>
        <p>
            <?php _e('Add this shortcode to any page or location on your website & the valuation form will be displayed', 'kostenlose-immobilienbewertung-lead-generator') ?>
            (<a href="https://leadmarkt.ch/faq"
                target="_blank"><?php _e('Help', 'kostenlose-immobilienbewertung-lead-generator') ?></a>):
        </p>
        <pre>
<?php echo htmlspecialchars('[real-estate-rating class="optional"][/real-estate-rating]'); ?>
</pre>
        <form method="post" action="options.php">
            <?php settings_fields('rer_generator_group'); ?>
            <?php do_settings_sections('rer_generator_group'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php _e('API Token (required)', 'kostenlose-immobilienbewertung-lead-generator') ?></th>
                    <td>
                        <input type="text" name="rer_api_token"
                               value="<?php echo esc_attr(get_option('rer_api_token')); ?>"/>
                    </td>
                </tr>
            </table>
            <p>
                <?php _e('How to quickly & easily get a free API token, you\'ll learn', 'kostenlose-immobilienbewertung-lead-generator') ?>
                <a href="https://leadmarkt.ch/faq"
                   target="_blank"><?php _e('here', 'kostenlose-immobilienbewertung-lead-generator') ?></a>.
            </p>
            <?php submit_button(); ?>
        </form>
    </div>
<?php }

function rer_shortcode($atts, $placeholder = null)
{
    $a = shortcode_atts([
        'class' => 'leadmarkt-generator-immobilien',
    ], $atts);

    ob_start();
    ?>
    <div class="realEstateRatingContainer">
        <iframe class="realEstateRatingIframe <?php echo esc_attr($a['class']); ?>"
                src="https://leadmarkt.ch/kostenlose-immobilienbewertung?api_token=<?php echo esc_attr(get_option('rer_api_token')); ?>"
                scrolling="no" frameBorder="0" onload="initLeadGenIframe()"></iframe>
    </div>
    <script>function initLeadGenIframe() {
            iFrameResize({checkOrigin: false, log: false}, '.realEstateRatingIframe');
        }</script>
    <?php
    return ob_get_clean();
}

add_shortcode('real-estate-rating', 'rer_shortcode');
add_shortcode('immobilienbewertung', 'rer_shortcode');
