<?php
if (!defined('ABSPATH')) exit;
$bxtr_tab_url = static function ($tab) {
    return add_query_arg(['page' => 'baxtersweb-maps', 'tab' => $tab], admin_url('tools.php'));
};
$bxtr_docs_url = 'https://baxtersweb.com/baxtersweb-maps-docs/';
$bxtr_demo_url = 'https://baxtersweb.com/baxtersweb-maps-demo/';
$bxtr_acf_url = 'https://www.advancedcustomfields.com/pro/';
$bxtr_osm_url = 'https://wordpress.org/plugins/acf-openstreetmap-field/';
$bxtr_advanced_views_url = 'https://wordpress.org/plugins/acf-views/';
$bxtr_colour_field = static function ($name, $value, $id) { ?>
    <div class="bxtr-colour-control">
        <input type="text" class="regular-text bxtr-colour-text" id="<?php echo esc_attr($id); ?>" name="<?php echo esc_attr($name); ?>" value="<?php echo esc_attr($value); ?>" pattern="#[0-9a-fA-F]{6}">
        <input type="color" class="bxtr-colour-picker" value="<?php echo esc_attr($value); ?>" aria-label="<?php esc_attr_e('Choose colour', 'baxtersweb-maps'); ?>">
    </div>
<?php };
?>
<div class="wrap bxtr-admin-wrap">
<header class="bxtr-admin-header">
    <div>
        <h1><?php esc_html_e('Baxtersweb Maps', 'baxtersweb-maps'); ?></h1>
        <p class="bxtr-admin-intro"><?php esc_html_e('Build ordered route maps and interactive points of interest from ACF content. Add the map fields to new or existing content, then display the result anywhere with a shortcode.', 'baxtersweb-maps'); ?></p>
    </div>
    <div class="bxtr-admin-header__meta">
        <span class="bxtr-version">v<?php echo esc_html(BXTR_MAPS_VERSION); ?></span>
        <a href="<?php echo esc_url($bxtr_docs_url); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Docs', 'baxtersweb-maps'); ?></a>
        <a href="<?php echo esc_url($bxtr_demo_url); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Demo', 'baxtersweb-maps'); ?></a>
    </div>
</header>

<?php if ($this->message): ?>
<div class="notice notice-<?php echo esc_attr($this->message_type); ?> is-dismissible"><p><?php echo esc_html($this->message); ?></p></div>
<?php endif; ?>

<nav class="nav-tab-wrapper bxtr-tabs">
<?php foreach (['overview' => __('Overview', 'baxtersweb-maps'), 'routing' => __('Routing', 'baxtersweb-maps'), 'markers' => __('Styles', 'baxtersweb-maps'), 'help' => __('Help & Data', 'baxtersweb-maps')] as $bxtr_slug => $bxtr_label): ?>
    <a class="nav-tab <?php echo $active_tab === $bxtr_slug ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url($bxtr_tab_url($bxtr_slug)); ?>"><?php echo esc_html($bxtr_label); ?></a>
<?php endforeach; ?>
</nav>

<?php if ($active_tab === 'overview'): ?>
<div class="bxtr-feature-strip" aria-label="<?php esc_attr_e('What Baxtersweb Maps does', 'baxtersweb-maps'); ?>">
    <div><span class="dashicons dashicons-location-alt"></span><strong><?php esc_html_e('Ordered routes', 'baxtersweb-maps'); ?></strong><small><?php esc_html_e('Turn ACF locations into clear A–B–C route maps.', 'baxtersweb-maps'); ?></small></div>
    <div><span class="dashicons dashicons-admin-site-alt3"></span><strong><?php esc_html_e('Interactive POIs', 'baxtersweb-maps'); ?></strong><small><?php esc_html_e('Add useful places with icons, colours and popups.', 'baxtersweb-maps'); ?></small></div>
    <div><span class="dashicons dashicons-database"></span><strong><?php esc_html_e('Fits your content', 'baxtersweb-maps'); ?></strong><small><?php esc_html_e('Use a new field group or add fields to one you already maintain.', 'baxtersweb-maps'); ?></small></div>
</div>

<div class="bxtr-page-columns">
<div class="bxtr-column-main">
<section class="bxtr-card">
    <h2><?php esc_html_e('Setup status', 'baxtersweb-maps'); ?></h2>
    <p><?php esc_html_e('Everything required to create and display maps is shown here. Road-following routing and Advanced Views are optional.', 'baxtersweb-maps'); ?></p>
    <table class="bxtr-status-table">
        <thead><tr><th><?php esc_html_e('Plugin or feature', 'baxtersweb-maps'); ?></th><th><?php esc_html_e('Status', 'baxtersweb-maps'); ?></th><th><?php esc_html_e('Action', 'baxtersweb-maps'); ?></th></tr></thead>
        <tbody>
        <tr>
            <td><strong><?php esc_html_e('ACF Pro', 'baxtersweb-maps'); ?></strong><small><?php esc_html_e('Provides the repeater fields used for route markers and POIs.', 'baxtersweb-maps'); ?></small></td>
            <td><span class="bxtr-status <?php echo $acf_active ? 'is-good' : 'is-bad'; ?>"><?php echo $acf_active ? esc_html__('Active', 'baxtersweb-maps') : esc_html__('Required', 'baxtersweb-maps'); ?></span></td>
            <td><a href="<?php echo esc_url($bxtr_acf_url); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e('View ACF Pro', 'baxtersweb-maps'); ?></a></td>
        </tr>
        <tr>
            <td><strong><?php esc_html_e('ACF OpenStreetMap Field', 'baxtersweb-maps'); ?></strong><small><?php esc_html_e('Adds the coordinate picker used inside your ACF fields.', 'baxtersweb-maps'); ?></small></td>
            <td><span class="bxtr-status <?php echo $osm_active ? 'is-good' : 'is-bad'; ?>"><?php echo $osm_active ? esc_html__('Active', 'baxtersweb-maps') : esc_html__('Required', 'baxtersweb-maps'); ?></span></td>
            <td><a href="<?php echo esc_url($bxtr_osm_url); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e('View on WordPress.org', 'baxtersweb-maps'); ?></a></td>
        </tr>
        <tr>
            <td><strong><?php esc_html_e('Map fields', 'baxtersweb-maps'); ?></strong><small><?php
                if ($selected_field_group_title) {
                    /* translators: %s: ACF field group title. */
                    echo esc_html(sprintf(__('Currently placed in “%s”.', 'baxtersweb-maps'), $selected_field_group_title));
                } else {
                    esc_html_e('Choose where the route and POI fields should live.', 'baxtersweb-maps');
                }
            ?></small></td>
            <td><span class="bxtr-status <?php echo $field_group_exists ? 'is-good' : 'is-warn'; ?>"><?php echo $field_group_exists ? esc_html__('Ready', 'baxtersweb-maps') : esc_html__('Not added', 'baxtersweb-maps'); ?></span></td>
            <td><a href="#bxtr-map-fields"><?php echo $field_group_exists ? esc_html__('Review fields', 'baxtersweb-maps') : esc_html__('Add fields', 'baxtersweb-maps'); ?></a></td>
        </tr>
        <tr>
            <td><strong><?php esc_html_e('Road-following routes', 'baxtersweb-maps'); ?></strong><small><?php esc_html_e('Optional. Without an API key, markers are connected with straight lines.', 'baxtersweb-maps'); ?></small></td>
            <td><span class="bxtr-status <?php echo $routing_connected ? 'is-good' : 'is-neutral'; ?>"><?php echo $routing_connected ? esc_html__('Connected', 'baxtersweb-maps') : esc_html__('Optional', 'baxtersweb-maps'); ?></span></td>
            <td><a href="<?php echo esc_url($bxtr_tab_url('routing')); ?>"><?php echo $routing_connected ? esc_html__('Routing settings', 'baxtersweb-maps') : esc_html__('Enable routing', 'baxtersweb-maps'); ?></a></td>
        </tr>
        </tbody>
    </table>
</section>

<section class="bxtr-card" id="bxtr-map-fields">
    <h2><?php esc_html_e('Add map fields', 'baxtersweb-maps'); ?></h2>
    <p><?php esc_html_e('Choose where Baxtersweb Maps should add its maintained route and POI fields. Existing fields in the selected group are left in place, and only the Baxtersweb Maps fields are created or updated.', 'baxtersweb-maps'); ?></p>
    <form method="post" class="bxtr-field-setup-form">
        <?php wp_nonce_field('bxtr_setup_fields', 'bxtr_setup_fields_nonce'); ?>
        <div class="bxtr-choice-cards">
            <label class="bxtr-choice-card">
                <input type="radio" name="bxtr_field_group_mode" value="new" <?php checked($field_group_mode, 'new'); ?>>
                <span><strong><?php esc_html_e('Create a new field group', 'baxtersweb-maps'); ?></strong><small><?php esc_html_e('A clean starting point for a new map setup.', 'baxtersweb-maps'); ?></small></span>
            </label>
            <label class="bxtr-choice-card">
                <input type="radio" name="bxtr_field_group_mode" value="existing" <?php checked($field_group_mode, 'existing'); ?>>
                <span><strong><?php esc_html_e('Add fields to an existing group', 'baxtersweb-maps'); ?></strong><small><?php esc_html_e('Best when your pages, posts or CPTs already have ACF content.', 'baxtersweb-maps'); ?></small></span>
            </label>
        </div>

        <div class="bxtr-field-mode bxtr-field-mode--existing">
            <h3><?php esc_html_e('Choose the existing field group', 'baxtersweb-maps'); ?></h3>
            <select name="bxtr_existing_field_group" class="regular-text">
                <option value=""><?php esc_html_e('Select an ACF field group', 'baxtersweb-maps'); ?></option>
                <?php foreach ($field_groups as $bxtr_group_key => $bxtr_group): ?>
                    <option value="<?php echo esc_attr($bxtr_group_key); ?>" <?php selected($selected_field_group_key, $bxtr_group_key); ?>><?php echo esc_html($bxtr_group['title']); ?></option>
                <?php endforeach; ?>
            </select>
            <p class="description"><?php esc_html_e('The plugin adds its own stable fields to this group. You do not need to rename or map your existing fields.', 'baxtersweb-maps'); ?></p>
        </div>

        <div class="bxtr-field-mode bxtr-field-mode--new">
            <h3><?php esc_html_e('Where should the new field group appear?', 'baxtersweb-maps'); ?></h3>
            <div class="bxtr-post-types">
                <?php foreach ($post_types as $bxtr_slug => $bxtr_label): ?>
                    <label><input type="checkbox" name="bxtr_field_post_types[]" value="<?php echo esc_attr($bxtr_slug); ?>" <?php checked(in_array($bxtr_slug, $selected_post_types, true)); ?>> <?php echo esc_html($bxtr_label); ?></label>
                <?php endforeach; ?>
            </div>
        </div>

        <h3><?php esc_html_e('Fields to include', 'baxtersweb-maps'); ?></h3>
        <div class="bxtr-included-fields">
            <div><span class="dashicons dashicons-yes-alt"></span><span><strong><?php esc_html_e('Route markers', 'baxtersweb-maps'); ?></strong><small><?php esc_html_e('Title, description and map coordinates in route order.', 'baxtersweb-maps'); ?></small></span></div>
            <label><input type="checkbox" name="bxtr_include_poi_fields" value="1" <?php checked($values['poi_enabled'], 'yes'); ?>> <span><strong><?php esc_html_e('Points of interest', 'baxtersweb-maps'); ?></strong><small><?php esc_html_e('Optional icons, colours, popup content and coordinates.', 'baxtersweb-maps'); ?></small></span></label>
        </div>

        <p class="bxtr-field-action">
            <button type="submit" class="button button-primary"><?php echo $field_group_exists ? esc_html__('Add or update missing fields', 'baxtersweb-maps') : esc_html__('Add map fields', 'baxtersweb-maps'); ?></button>
            <?php if ($fields_last_updated): ?>
                <span class="bxtr-last-updated"><?php
                    printf(
                        /* translators: %s: date and time when the fields were last updated. */
                        esc_html__('Last updated: %s', 'baxtersweb-maps'),
                        esc_html(wp_date(get_option('date_format') . ' ' . get_option('time_format'), $fields_last_updated))
                    );
                ?></span>
            <?php endif; ?>
        </p>
    </form>
</section>
</div>

<aside class="bxtr-column-preview">
<section class="bxtr-card bxtr-sticky">
    <h2><?php esc_html_e('Example map', 'baxtersweb-maps'); ?></h2>
    <p><?php esc_html_e('An ordered route with styled POIs. Your own content will use the global appearance settings.', 'baxtersweb-maps'); ?></p>
    <div id="bxtr-preview-map" class="bxtr-map bxtr-preview-map" style="--bxtr-marker-color:<?php echo esc_attr($values['marker_color']); ?>;--bxtr-marker-number-color:<?php echo esc_attr($values['marker_number_color']); ?>;--bxtr-poi-marker-color:<?php echo esc_attr($values['poi_marker_color']); ?>;--bxtr-route-color:<?php echo esc_attr($values['route_color']); ?>;--bxtr-map-height:360px;--bxtr-border-radius:<?php echo esc_attr($values['border_radius']); ?>"></div>
    <div class="bxtr-steps">
        <h3><?php esc_html_e('Create your first map', 'baxtersweb-maps'); ?></h3>
        <ol><li><?php esc_html_e('Add the map fields.', 'baxtersweb-maps'); ?></li><li><?php esc_html_e('Enter route markers and POIs in your content.', 'baxtersweb-maps'); ?></li><li><?php esc_html_e('Display the map with [bxtr_map].', 'baxtersweb-maps'); ?></li></ol>
    </div>
</section>
</aside>
</div>

<section class="bxtr-card bxtr-display-card">
    <div>
        <span class="bxtr-eyebrow"><?php esc_html_e('Display your map', 'baxtersweb-maps'); ?></span>
        <h2><?php esc_html_e('Add the map wherever your content needs it.', 'baxtersweb-maps'); ?></h2>
        <p><?php esc_html_e('Use the shortcode on a single page, pass a post ID inside a PHP loop, or place it in an Advanced Views Layout.', 'baxtersweb-maps'); ?></p>
    </div>
    <div class="bxtr-code-stack"><code>[bxtr_map]</code><code>[bxtr_map id="123"]</code></div>
</section>

<section class="bxtr-integration-card">
    <div class="bxtr-integration-card__icon"><span class="dashicons dashicons-layout"></span></div>
    <div>
        <span class="bxtr-eyebrow"><?php esc_html_e('Optional integration', 'baxtersweb-maps'); ?></span>
        <h2><?php esc_html_e('Display your ACF content and maps together with Advanced Views.', 'baxtersweb-maps'); ?></h2>
        <p><?php esc_html_e('Build archive cards, custom layouts and content sections from your existing ACF fields, then include the map for each current post.', 'baxtersweb-maps'); ?></p>
        <code>[bxtr_map id="{{ _layout.object_id }}"]</code>
    </div>
    <div class="bxtr-integration-card__action">
        <span class="bxtr-status <?php echo $advanced_views_active ? 'is-good' : 'is-neutral'; ?>"><?php echo $advanced_views_active ? esc_html__('Active', 'baxtersweb-maps') : esc_html__('Optional', 'baxtersweb-maps'); ?></span>
        <a class="button" href="<?php echo esc_url($bxtr_advanced_views_url); ?>" target="_blank" rel="noopener noreferrer"><?php echo $advanced_views_active ? esc_html__('View Advanced Views', 'baxtersweb-maps') : esc_html__('Explore Advanced Views', 'baxtersweb-maps'); ?></a>
    </div>
</section>

<?php elseif ($active_tab === 'routing'): ?>
<section class="bxtr-card">
    <h2><?php esc_html_e('Enable road-following routes', 'baxtersweb-maps'); ?></h2>
    <div class="bxtr-info-box"><strong><?php esc_html_e('Connecting an API is optional.', 'baxtersweb-maps'); ?></strong> <?php esc_html_e('Without one, Baxtersweb Maps joins route markers with a dashed straight line. Add a free openrouteservice key when you want routes calculated along roads.', 'baxtersweb-maps'); ?></div>
    <form method="post" id="bxtr-routing-form">
        <?php wp_nonce_field('bxtr_test_routing', 'bxtr_test_routing_nonce'); ?><input type="hidden" name="bxtr_active_tab" value="routing">
        <table class="form-table">
            <tr>
                <th><label for="bxtr_ors_api_key"><?php esc_html_e('openrouteservice API key', 'baxtersweb-maps'); ?></label></th>
                <td>
                    <div class="bxtr-key-row">
                        <input type="password" class="regular-text" id="bxtr_ors_api_key" name="bxtr_ors_api_key" value="<?php echo esc_attr($values['ors_api_key']); ?>" autocomplete="off">
                        <button class="button button-primary" type="submit"><?php esc_html_e('Save & Test API', 'baxtersweb-maps'); ?></button>
                    </div>
                    <?php
                    $bxtr_status_class = 'is-neutral';
                    $bxtr_status_text = __('Not tested', 'baxtersweb-maps');
                    if ($values['ors_status'] === 'connected') {
                        $bxtr_status_class = 'is-good';
                        $bxtr_status_text = $values['ors_status_message'] ?: __('Connected — API key verified.', 'baxtersweb-maps');
                    } elseif ($values['ors_status'] === 'error') {
                        $bxtr_status_class = 'is-bad';
                        $bxtr_status_text = $values['ors_status_message'] ?: __('Connection failed.', 'baxtersweb-maps');
                    }
                    ?>
                    <p class="bxtr-api-status"><span class="bxtr-status <?php echo esc_attr($bxtr_status_class); ?>"><?php echo esc_html($bxtr_status_text); ?></span>
                        <?php if ($values['ors_tested_at']) : ?>
                            <small><?php
                            printf(
                                /* translators: %s: date and time of the most recent API test. */
                                esc_html__('Last tested: %s', 'baxtersweb-maps'),
                                esc_html(wp_date(get_option('date_format') . ' ' . get_option('time_format'), $values['ors_tested_at']))
                            );
                            ?></small>
                        <?php endif; ?>
                    </p>
                    <p class="description"><?php esc_html_e('The key is used only when a route is calculated or updated. Visitors viewing a saved route do not use your API quota.', 'baxtersweb-maps'); ?> <a href="https://openrouteservice.org/dev/#/signup" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Get a free API key', 'baxtersweb-maps'); ?></a></p>
                    <div class="bxtr-info-box">
                        <strong><?php esc_html_e('How saved routes behave', 'baxtersweb-maps'); ?></strong>
                        <ul>
                            <li><?php esc_html_e('Removing the API key does not remove road geometry that has already been calculated and saved.', 'baxtersweb-maps'); ?></li>
                            <li><?php esc_html_e('When a valid key is added, existing maps without saved road geometry are calculated automatically.', 'baxtersweb-maps'); ?></li>
                            <li><?php esc_html_e('Without a valid key, new or changed routes use dashed straight fallback lines until routing is available again.', 'baxtersweb-maps'); ?></li>
                        </ul>
                    </div>
                </td>
            </tr>
        </table>
    </form>
</section>
<section class="bxtr-card"><h3><?php esc_html_e('External service disclosure', 'baxtersweb-maps'); ?></h3><p><?php esc_html_e('When a road route is calculated, its coordinates are sent from your WordPress server to openrouteservice solely to return the requested route geometry.', 'baxtersweb-maps'); ?></p></section>

<?php elseif ($active_tab === 'markers'): ?>
<form method="post">
<?php wp_nonce_field('bxtr_save_settings', 'bxtr_settings_nonce'); ?><input type="hidden" name="bxtr_active_tab" value="markers">
<div class="bxtr-page-columns">
<div class="bxtr-column-main">
<section class="bxtr-card"><h2><?php esc_html_e('Make route markers easy to follow', 'baxtersweb-maps'); ?></h2><p><?php esc_html_e('Route markers show the order of a journey. Keep their colour and lettering clear against the map.', 'baxtersweb-maps'); ?></p><table class="form-table">
<tr><th><?php esc_html_e('Route display', 'baxtersweb-maps'); ?></th><td><label><input type="radio" name="bxtr_draw_route" value="yes" <?php checked($values['draw_route'], 'yes'); ?>> <?php esc_html_e('Show the route line', 'baxtersweb-maps'); ?></label><p class="description"><?php esc_html_e('Uses a cached road route when connected, otherwise a dashed straight line.', 'baxtersweb-maps'); ?></p><label><input type="radio" name="bxtr_draw_route" value="no" <?php checked($values['draw_route'], 'no'); ?>> <?php esc_html_e('Show markers only', 'baxtersweb-maps'); ?></label></td></tr>
<tr><th><label for="bxtr_route_color_text"><?php esc_html_e('Route colour', 'baxtersweb-maps'); ?></label></th><td><?php $bxtr_colour_field('bxtr_route_color', $values['route_color'], 'bxtr_route_color_text'); ?><p class="description"><?php esc_html_e('Used for both road-following and straight-line routes.', 'baxtersweb-maps'); ?></p></td></tr>
<tr><th><?php esc_html_e('Marker sequence', 'baxtersweb-maps'); ?></th><td><label><input type="radio" name="bxtr_marker_sequence" value="alphabetic" <?php checked($values['marker_sequence'], 'alphabetic'); ?>> <?php esc_html_e('Letters: A, B, C', 'baxtersweb-maps'); ?></label><br><label><input type="radio" name="bxtr_marker_sequence" value="numeric" <?php checked($values['marker_sequence'], 'numeric'); ?>> <?php esc_html_e('Numbers: 1, 2, 3', 'baxtersweb-maps'); ?></label></td></tr>
<tr><th><?php esc_html_e('Marker background', 'baxtersweb-maps'); ?></th><td><?php $bxtr_colour_field('bxtr_marker_color', $values['marker_color'], 'bxtr_marker_color_text'); ?><p class="description"><?php esc_html_e('The main colour of every ordered route marker.', 'baxtersweb-maps'); ?></p></td></tr>
<tr><th><?php esc_html_e('Marker text', 'baxtersweb-maps'); ?></th><td><?php $bxtr_colour_field('bxtr_marker_number_color', $values['marker_number_color'], 'bxtr_marker_number_color_text'); ?><p class="description"><?php esc_html_e('Choose a contrasting colour for route letters or numbers.', 'baxtersweb-maps'); ?></p></td></tr>
</table></section>

<section class="bxtr-card"><h2><?php esc_html_e('Set useful POI defaults', 'baxtersweb-maps'); ?></h2><p><?php esc_html_e('These defaults keep the content editor compact. Individual POIs can still choose a built-in icon or colour where that option is enabled.', 'baxtersweb-maps'); ?></p><table class="form-table">
<tr><th><?php esc_html_e('Show POIs', 'baxtersweb-maps'); ?></th><td><label><input type="radio" name="bxtr_poi_enabled" value="yes" <?php checked($values['poi_enabled'], 'yes'); ?>> <?php esc_html_e('Show points of interest', 'baxtersweb-maps'); ?></label><br><label><input type="radio" name="bxtr_poi_enabled" value="no" <?php checked($values['poi_enabled'], 'no'); ?>> <?php esc_html_e('Hide points of interest globally', 'baxtersweb-maps'); ?></label></td></tr>
<tr><th><label for="bxtr_poi_icon_mode"><?php esc_html_e('POI marker style', 'baxtersweb-maps'); ?></label></th><td><select name="bxtr_poi_icon_mode" id="bxtr_poi_icon_mode"><option value="builtin" <?php selected($values['poi_icon_mode'], 'builtin'); ?>><?php esc_html_e('Built-in WordPress icons', 'baxtersweb-maps'); ?></option><option value="theme" <?php selected($values['poi_icon_mode'], 'theme'); ?>><?php esc_html_e('Theme icon class', 'baxtersweb-maps'); ?></option><option value="plain" <?php selected($values['poi_icon_mode'], 'plain'); ?>><?php esc_html_e('Plain coloured marker', 'baxtersweb-maps'); ?></option></select><p class="description"><?php esc_html_e('Choose one icon system for the site instead of making editors decide between systems on every POI.', 'baxtersweb-maps'); ?></p></td></tr>
<tr class="bxtr-icon-option bxtr-icon-option--builtin"><th><?php esc_html_e('Default built-in icon', 'baxtersweb-maps'); ?></th><td><select name="bxtr_poi_default_icon" id="bxtr_poi_default_icon"><?php foreach (BXTR_Maps_ACF::get_builtin_icons() as $bxtr_icon => $bxtr_label): ?><option value="<?php echo esc_attr($bxtr_icon); ?>" <?php selected($values['poi_default_icon'], $bxtr_icon); ?>><?php echo esc_html($bxtr_label); ?></option><?php endforeach; ?></select><p class="description"><?php esc_html_e('Used whenever a POI does not choose a different built-in icon.', 'baxtersweb-maps'); ?></p></td></tr>
<tr class="bxtr-icon-option bxtr-icon-option--theme"><th><?php esc_html_e('Default theme icon class', 'baxtersweb-maps'); ?></th><td><input class="regular-text" name="bxtr_poi_theme_icon_class" value="<?php echo esc_attr($values['poi_theme_icon_class']); ?>" placeholder="fas fa-hotel"><p class="description"><?php esc_html_e('The class must be provided by the active theme or another plugin.', 'baxtersweb-maps'); ?></p></td></tr>
<tr><th><?php esc_html_e('Default background', 'baxtersweb-maps'); ?></th><td><?php $bxtr_colour_field('bxtr_poi_marker_color', $values['poi_marker_color'], 'bxtr_poi_marker_color_text'); ?><p class="description"><?php esc_html_e('Individual POIs may override this with their own hex colour.', 'baxtersweb-maps'); ?></p></td></tr>
<tr><th><?php esc_html_e('Nearby POIs', 'baxtersweb-maps'); ?></th><td><label><input type="radio" name="bxtr_cluster_pois" value="yes" <?php checked($values['cluster_pois'], 'yes'); ?>> <?php esc_html_e('Group nearby markers and spread them on click', 'baxtersweb-maps'); ?></label><br><label><input type="radio" name="bxtr_cluster_pois" value="no" <?php checked($values['cluster_pois'], 'no'); ?>> <?php esc_html_e('Always show markers individually', 'baxtersweb-maps'); ?></label><p class="description"><?php esc_html_e('Grouping keeps maps readable when several POIs are close together at the current zoom level.', 'baxtersweb-maps'); ?></p></td></tr>
</table></section>

<section class="bxtr-card"><h2><?php esc_html_e('Shape the map container', 'baxtersweb-maps'); ?></h2><p><?php esc_html_e('Maps use the standard OpenStreetMap tile layer. These settings control how the map fits into your page design.', 'baxtersweb-maps'); ?></p><table class="form-table"><tr><th><?php esc_html_e('Map height', 'baxtersweb-maps'); ?></th><td><input class="regular-text" name="bxtr_map_height" value="<?php echo esc_attr($values['map_height']); ?>"><p class="description"><?php esc_html_e('Examples: 500px, 60vh or 30rem.', 'baxtersweb-maps'); ?></p></td></tr><tr><th><?php esc_html_e('Border radius', 'baxtersweb-maps'); ?></th><td><input class="regular-text" name="bxtr_border_radius" value="<?php echo esc_attr($values['border_radius']); ?>"><p class="description"><?php esc_html_e('Examples: 0px, 12px or 1rem.', 'baxtersweb-maps'); ?></p></td></tr></table></section>
<?php submit_button(__('Save style settings', 'baxtersweb-maps')); ?>
</div>
<aside class="bxtr-column-preview"><section class="bxtr-card bxtr-sticky"><h2><?php esc_html_e('Style preview', 'baxtersweb-maps'); ?></h2><p><?php esc_html_e('Colour and marker changes update here before you save.', 'baxtersweb-maps'); ?></p><div id="bxtr-preview-map" class="bxtr-map bxtr-preview-map" style="--bxtr-marker-color:<?php echo esc_attr($values['marker_color']); ?>;--bxtr-marker-number-color:<?php echo esc_attr($values['marker_number_color']); ?>;--bxtr-poi-marker-color:<?php echo esc_attr($values['poi_marker_color']); ?>;--bxtr-route-color:<?php echo esc_attr($values['route_color']); ?>;--bxtr-map-height:360px;--bxtr-border-radius:<?php echo esc_attr($values['border_radius']); ?>"></div><div class="bxtr-style-tips"><h3><?php esc_html_e('CSS classes', 'baxtersweb-maps'); ?></h3><table class="widefat striped"><tbody><tr><td><code>.bxtr-map</code></td><td><?php esc_html_e('Map container', 'baxtersweb-maps'); ?></td></tr><tr><td><code>.bxtr-marker--route</code></td><td><?php esc_html_e('Route marker', 'baxtersweb-maps'); ?></td></tr><tr><td><code>.bxtr-marker--poi</code></td><td><?php esc_html_e('POI marker', 'baxtersweb-maps'); ?></td></tr><tr><td><code>.bxtr-marker--cluster</code></td><td><?php esc_html_e('Nearby POI group', 'baxtersweb-maps'); ?></td></tr><tr><td><code>.bxtr-popup</code></td><td><?php esc_html_e('Popup content', 'baxtersweb-maps'); ?></td></tr></tbody></table></div></section></aside>
</div>
</form>

<?php else: ?>
<div class="bxtr-admin-grid">
<section class="bxtr-card"><h2><?php esc_html_e('Display maps', 'baxtersweb-maps'); ?></h2><p><?php esc_html_e('Use the current post automatically:', 'baxtersweb-maps'); ?></p><p><code>[bxtr_map]</code></p><p><?php esc_html_e('Pass a specific post ID:', 'baxtersweb-maps'); ?></p><p><code>[bxtr_map id="123"]</code></p><p><?php esc_html_e('Inside a PHP loop:', 'baxtersweb-maps'); ?></p><p><code>echo do_shortcode('[bxtr_map id="' . get_the_ID() . '"]');</code></p></section>
<section class="bxtr-card"><h2><?php esc_html_e('Data on uninstall', 'baxtersweb-maps'); ?></h2><form method="post"><?php wp_nonce_field('bxtr_save_settings', 'bxtr_settings_nonce'); ?><input type="hidden" name="bxtr_active_tab" value="help"><select name="bxtr_uninstall_mode"><option value="keep" <?php selected($values['uninstall_mode'], 'keep'); ?>><?php esc_html_e('Keep plugin settings and generated fields', 'baxtersweb-maps'); ?></option><option value="remove" <?php selected($values['uninstall_mode'], 'remove'); ?>><?php esc_html_e('Remove plugin settings and the generated Baxtersweb Maps field group', 'baxtersweb-maps'); ?></option></select><p class="description"><?php esc_html_e('Fields added to another ACF field group are not deleted with that group. This preference applies when the plugin itself is deleted from WordPress.', 'baxtersweb-maps'); ?></p><?php submit_button(__('Save uninstall preference', 'baxtersweb-maps'), 'secondary', 'submit', false); ?></form></section>
</div>
<section class="bxtr-integration-card bxtr-integration-card--help"><div class="bxtr-integration-card__icon"><span class="dashicons dashicons-layout"></span></div><div><span class="bxtr-eyebrow"><?php esc_html_e('Advanced Views integration', 'baxtersweb-maps'); ?></span><h2><?php esc_html_e('Place a map inside archive cards and custom ACF layouts.', 'baxtersweb-maps'); ?></h2><p><?php esc_html_e('Advanced Views can display your existing ACF fields and the map together for each post in a selection or layout.', 'baxtersweb-maps'); ?></p><code>[bxtr_map id="{{ _layout.object_id }}"]</code></div><div class="bxtr-integration-card__action"><a class="button button-primary" href="<?php echo esc_url($bxtr_advanced_views_url); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e('View Advanced Views on WordPress.org', 'baxtersweb-maps'); ?></a></div></section>
<section class="bxtr-card"><h2><?php esc_html_e('Common map variations', 'baxtersweb-maps'); ?></h2><div class="bxtr-shortcode-grid"><div><strong><?php esc_html_e('POIs without a route', 'baxtersweb-maps'); ?></strong><code>[bxtr_map route="no" poi="yes"]</code></div><div><strong><?php esc_html_e('Route without POIs', 'baxtersweb-maps'); ?></strong><code>[bxtr_map route="yes" poi="no"]</code></div><div><strong><?php esc_html_e('Map in a post loop', 'baxtersweb-maps'); ?></strong><code>[bxtr_map id="123"]</code></div></div></section>
<?php endif; ?>
</div>
