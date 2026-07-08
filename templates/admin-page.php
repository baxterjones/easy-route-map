<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wrap bxtr-admin-page">

    <div class="bxtr-admin-header">
        <div>
            <h1><?php esc_html_e('Baxtersweb Maps', 'baxtersweb-maps'); ?></h1>
            <p class="bxtr-admin-page__intro">
                <?php esc_html_e('Create dynamic OpenStreetMap route displays from ACF map markers and optional points of interest saved on posts, pages, or custom post type entries.', 'baxtersweb-maps'); ?>
            </p>
        </div>
        <div class="bxtr-admin-header__meta">
            <nav class="bxtr-admin-links" aria-label="<?php esc_attr_e('Baxtersweb Maps links', 'baxtersweb-maps'); ?>">
                <a href="<?php echo esc_url('https://baxtersweb.com/baxtersweb-maps-docs/'); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Docs', 'baxtersweb-maps'); ?></a>
                <a href="<?php echo esc_url('https://baxtersweb.com/baxtersweb-maps-demo/'); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Demo', 'baxtersweb-maps'); ?></a>
            </nav>
            <span class="bxtr-version"><?php /* translators: %s: plugin version. */ echo esc_html(sprintf(__('Version %s', 'baxtersweb-maps'), BXTR_MAPS_VERSION)); ?></span>
        </div>
    </div>

    <?php if (!empty($this->message)) : ?>
        <div class="notice notice-<?php echo esc_attr($this->message_type); ?> is-dismissible">
            <p><?php echo esc_html($this->message); ?></p>
        </div>
    <?php endif; ?>

    <section class="bxtr-card bxtr-requirements-card">
        <h2><?php esc_html_e('Requirements', 'baxtersweb-maps'); ?></h2>
        <ul class="bxtr-status-list">
            <li class="<?php echo esc_attr($acf_active ? 'is-ok' : 'is-missing'); ?>">
                <span><?php echo esc_html($acf_active ? '✓' : '×'); ?></span>
                <?php esc_html_e('Advanced Custom Fields Pro', 'baxtersweb-maps'); ?>
            </li>
            <li class="<?php echo esc_attr($osm_active ? 'is-ok' : 'is-missing'); ?>">
                <span><?php echo esc_html($osm_active ? '✓' : '×'); ?></span>
                <?php esc_html_e('ACF OpenStreetMap Field', 'baxtersweb-maps'); ?>
            </li>
        </ul>
        <?php if (!$acf_active || !$osm_active) : ?>
            <p><?php esc_html_e('Install and activate the missing plugins first. Baxtersweb Maps currently uses ACF Pro repeater fields for map marker editing.', 'baxtersweb-maps'); ?></p>
        <?php else : ?>
            <p><?php esc_html_e('Requirements are active. You can create the recommended ACF fields automatically.', 'baxtersweb-maps'); ?></p>
        <?php endif; ?>
    </section>

    <div class="bxtr-setup-grid">
        <div class="bxtr-card">
            <h2><?php esc_html_e('1. Setup fields', 'baxtersweb-maps'); ?></h2>
            <p><?php esc_html_e('Create the recommended ACF field group instead of manually setting up route and point-of-interest repeaters.', 'baxtersweb-maps'); ?></p>

            <form method="post" action="" class="bxtr-confirm-submit" data-confirm-message="<?php echo esc_attr__('This will create or update the recommended Baxtersweb Maps ACF fields. Existing generated fields may be updated, but custom fields are not deleted. Continue?', 'baxtersweb-maps'); ?>">
                <?php wp_nonce_field('bxtr_setup_fields', 'bxtr_setup_fields_nonce'); ?>

                <fieldset>
                    <legend><strong><?php esc_html_e('Show route fields on:', 'baxtersweb-maps'); ?></strong></legend>

                    <?php foreach ($post_types as $post_type => $bxtr_label) : ?>
                        <label class="bxtr-checkbox-row">
                            <input
                                type="checkbox"
                                name="bxtr_field_post_types[]"
                                value="<?php echo esc_attr($post_type); ?>"
                                <?php checked(in_array($post_type, (array) $selected_post_types, true)); ?>>
                            <?php echo esc_html($bxtr_label); ?>
                            <code><?php echo esc_html($post_type); ?></code>
                        </label>
                    <?php endforeach; ?>
                </fieldset>

                <p class="bxtr-note"><strong><?php esc_html_e('Optional fields', 'baxtersweb-maps'); ?></strong></p>
                <label class="bxtr-checkbox-row">
                    <input type="checkbox" name="bxtr_setup_include_poi" value="yes">
                    <?php esc_html_e('Also create or update Points of Interest fields', 'baxtersweb-maps'); ?>
                </label>
                <p class="description"><?php esc_html_e('Leave this unchecked if you only need map markers. If POI fields already exist, leaving this unchecked will not delete them.', 'baxtersweb-maps'); ?></p>

                <?php submit_button($field_group_exists ? __('Update Baxtersweb Maps ACF fields', 'baxtersweb-maps') : __('Set up ACF fields for me', 'baxtersweb-maps'), 'primary', 'submit', false, (!$acf_active || !$osm_active) ? ['disabled' => 'disabled'] : []); ?>
            </form>

            <?php if ($field_group_exists) : ?>
                <p class="bxtr-note"><?php esc_html_e('The Baxtersweb Maps ACF field group already exists. Use the update button if you need to add the newer POI fields to an older installation.', 'baxtersweb-maps'); ?></p>
            <?php endif; ?>
        </div>

        <div class="bxtr-card">
            <h2><?php esc_html_e('2. Add map markers', 'baxtersweb-maps'); ?></h2>
            <p><?php echo wp_kses_post(__('Edit a selected post type and look for the <strong>Map Markers</strong> field group.', 'baxtersweb-maps')); ?></p>
            <ol>
                <li><?php esc_html_e('Add one row per map marker.', 'baxtersweb-maps'); ?></li>
                <li><?php echo wp_kses_post(__('Drop one marker in the <strong>Marker Coordinates</strong> field.', 'baxtersweb-maps')); ?></li>
                <li><?php esc_html_e('Drag rows to control the route order.', 'baxtersweb-maps'); ?></li>
                <li><?php echo wp_kses_post(__('Optionally add extra markers under <strong>Points of Interest</strong>.', 'baxtersweb-maps')); ?></li>
                <li><?php esc_html_e('Save or update the post.', 'baxtersweb-maps'); ?></li>
            </ol>
        </div>

        <div class="bxtr-card">
            <h2><?php esc_html_e('3. Display the map', 'baxtersweb-maps'); ?></h2>
            <p><?php esc_html_e('Add this shortcode to the same post, page, CPT content, or template output:', 'baxtersweb-maps'); ?></p>
            <div class="bxtr-shortcode-row">
                <code id="bxtr-shortcode-default">[bxtr_map]</code>
                <button type="button" class="button bxtr-copy-shortcode" data-copy-target="bxtr-shortcode-default"><?php esc_html_e('Copy', 'baxtersweb-maps'); ?></button>
            </div>
            <p><?php esc_html_e('Developers can target a specific post ID, or turn route/POI layers on or off per output:', 'baxtersweb-maps'); ?></p>
            <div class="bxtr-shortcode-row">
                <code id="bxtr-shortcode-id">[bxtr_map id=&quot;123&quot; route=&quot;yes&quot; poi=&quot;yes&quot;]</code>
                <button type="button" class="button bxtr-copy-shortcode" data-copy-target="bxtr-shortcode-id"><?php esc_html_e('Copy', 'baxtersweb-maps'); ?></button>
            </div>
            <p class="description"><?php echo wp_kses_post(__('For custom loops, pass the current item ID into the shortcode. In PHP templates, use <code>echo do_shortcode(\'[bxtr_map id="\' . get_the_ID() . \'"]\');</code>. In <a href="https://wordpress.org/plugins/acf-views/" target="_blank" rel="noopener noreferrer">Advanced Views</a> Layouts, use <code>[bxtr_map id="{{ _layout.object_id }}"]</code>. The plugin will try to detect the current post automatically, but an explicit ID is the safest method inside loops.', 'baxtersweb-maps')); ?></p>
        </div>
    </div>

    <hr>

    <h2><?php esc_html_e('Settings', 'baxtersweb-maps'); ?></h2>

    <form method="post" action="">
        <?php wp_nonce_field('bxtr_save_settings', 'bxtr_settings_nonce'); ?>

        <div class="bxtr-settings-preview-grid">
            <div class="bxtr-settings-panel">
        <table class="form-table" role="presentation">
            <tbody>
                <tr>
                    <th scope="row"><label for="bxtr_marker_color"><?php esc_html_e('Marker colour', 'baxtersweb-maps'); ?></label></th>
                    <td>
                        <input type="text" id="bxtr_marker_color" name="bxtr_marker_color" value="<?php echo esc_attr($marker_color); ?>" class="regular-text" placeholder="#3d874d" pattern="^#[a-fA-F0-9]{6}$">
                        <p class="description"><?php echo wp_kses_post(__('Use a six-character web hex colour, for example <code>#3d874d</code>.', 'baxtersweb-maps')); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="bxtr_marker_number_color"><?php esc_html_e('Marker text colour', 'baxtersweb-maps'); ?></label></th>
                    <td>
                        <input type="text" id="bxtr_marker_number_color" name="bxtr_marker_number_color" value="<?php echo esc_attr($marker_number_color); ?>" class="regular-text" placeholder="#ffffff" pattern="^#[a-fA-F0-9]{6}$">
                        <p class="description"><?php echo wp_kses_post(__('Controls the text inside route markers. Default is white: <code>#ffffff</code>.', 'baxtersweb-maps')); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="bxtr_route_color"><?php esc_html_e('Route colour', 'baxtersweb-maps'); ?></label></th>
                    <td>
                        <input type="text" id="bxtr_route_color" name="bxtr_route_color" value="<?php echo esc_attr($route_color); ?>" class="regular-text" placeholder="#3388ff" pattern="^#[a-fA-F0-9]{6}$">
                        <p class="description"><?php echo wp_kses_post(__('Use a six-character web hex colour. Default is Leaflet blue: <code>#3388ff</code>.', 'baxtersweb-maps')); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="bxtr_poi_marker_color"><?php esc_html_e('POI marker colour', 'baxtersweb-maps'); ?></label></th>
                    <td>
                        <input type="text" id="bxtr_poi_marker_color" name="bxtr_poi_marker_color" value="<?php echo esc_attr($poi_marker_color); ?>" class="regular-text" placeholder="#f59e0b" pattern="^#[a-fA-F0-9]{6}$">
                        <p class="description"><?php echo wp_kses_post(__('Controls the background colour for POI label markers. Default is orange: <code>#f59e0b</code>. These do not affect the route line.', 'baxtersweb-maps')); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e('Layers', 'baxtersweb-maps'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" id="bxtr_draw_route" name="bxtr_draw_route" value="yes" <?php checked($draw_route, 'yes'); ?>>
                            <?php esc_html_e('Draw route line between map markers', 'baxtersweb-maps'); ?>
                        </label>
                        <br>
                        <label>
                            <input type="checkbox" id="bxtr_poi_enabled" name="bxtr_poi_enabled" value="yes" <?php checked($poi_enabled, 'yes'); ?>>
                            <?php esc_html_e('Show points of interest layer', 'baxtersweb-maps'); ?>
                        </label>
                        <p class="description"><?php echo wp_kses_post(__('Both can still be overridden on a shortcode using <code>route="no"</code> or <code>poi="no"</code>.', 'baxtersweb-maps')); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="bxtr_map_tile_style"><?php esc_html_e('Map style', 'baxtersweb-maps'); ?></label></th>
                    <td>
                        <select id="bxtr_map_tile_style" name="bxtr_map_tile_style">
                            <option value="osm" <?php selected($map_tile_style, 'osm'); ?>><?php esc_html_e('OpenStreetMap Standard', 'baxtersweb-maps'); ?></option>
                            <option value="hot" <?php selected($map_tile_style, 'hot'); ?>><?php esc_html_e('Humanitarian', 'baxtersweb-maps'); ?></option>
                            <option value="topo" <?php selected($map_tile_style, 'topo'); ?>><?php esc_html_e('OpenTopoMap', 'baxtersweb-maps'); ?></option>
                        </select>
                        <p class="description"><?php esc_html_e('These are public OpenStreetMap-compatible tile styles. Keep this simple for now; custom branded tile providers can come later.', 'baxtersweb-maps'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="bxtr_map_height"><?php esc_html_e('Map height', 'baxtersweb-maps'); ?></label></th>
                    <td>
                        <input type="text" id="bxtr_map_height" name="bxtr_map_height" value="<?php echo esc_attr($map_height); ?>" class="regular-text" placeholder="500px">
                        <p class="description"><?php echo wp_kses_post(__('Enter a fixed CSS height using <code>px</code>, <code>vh</code>, <code>rem</code>, or <code>em</code>. Avoid percentage heights because Leaflet maps often cannot calculate them unless the parent has a fixed height.', 'baxtersweb-maps')); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="bxtr_border_radius"><?php esc_html_e('Border radius', 'baxtersweb-maps'); ?></label></th>
                    <td>
                        <input type="text" id="bxtr_border_radius" name="bxtr_border_radius" value="<?php echo esc_attr($border_radius); ?>" class="regular-text" placeholder="12px">
                        <p class="description"><?php echo wp_kses_post(__('Enter any valid CSS border-radius value. Examples: <code>12px</code>, <code>1rem</code>, <code>50%</code>.', 'baxtersweb-maps')); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="bxtr_marker_sequence"><?php esc_html_e('Marker numbering', 'baxtersweb-maps'); ?></label></th>
                    <td>
                        <select id="bxtr_marker_sequence" name="bxtr_marker_sequence">
                            <option value="alphabetic" <?php selected($marker_sequence, 'alphabetic'); ?>><?php esc_html_e('Alphabetical markers: A, B, C', 'baxtersweb-maps'); ?></option>
                            <option value="numeric" <?php selected($marker_sequence, 'numeric'); ?>><?php esc_html_e('Numbered markers: 1, 2, 3', 'baxtersweb-maps'); ?></option>
                        </select>
                        <p class="description"><?php esc_html_e('Controls the text shown inside connected map markers. Popup headings use the marker title when available.', 'baxtersweb-maps'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e('Plugin data on uninstall', 'baxtersweb-maps'); ?></th>
                    <td>
                        <fieldset>
                            <label>
                                <input type="radio" name="bxtr_uninstall_mode" value="keep" <?php checked($uninstall_mode, 'keep'); ?>>
                                <?php esc_html_e('Keep all data', 'baxtersweb-maps'); ?> <span class="description"><?php esc_html_e('Recommended. Preserves map markers, generated ACF fields, and settings if the plugin is deleted.', 'baxtersweb-maps'); ?></span>
                            </label><br>
                            <label>
                                <input type="radio" name="bxtr_uninstall_mode" value="remove" <?php checked($uninstall_mode, 'remove'); ?>>
                                <?php esc_html_e('Remove plugin data', 'baxtersweb-maps'); ?> <span class="description"><?php esc_html_e('Deletes Baxtersweb Maps settings and the generated ACF field group on uninstall. Existing post meta is preserved for safety.', 'baxtersweb-maps'); ?></span>
                            </label>
                        </fieldset>
                    </td>
                </tr>
            </tbody>
        </table>
            </div>

            <aside class="bxtr-preview-card">
                <h2><?php esc_html_e('Style preview', 'baxtersweb-maps'); ?></h2>
                <p class="bxtr-preview-card__notice"><?php esc_html_e('Change the fields, then save settings to apply them to real maps. This preview uses fake route and POI markers.', 'baxtersweb-maps'); ?></p>
                <div
                    id="bxtr-preview-map"
                    class="bxtr-map bxtr-preview-map"
                    style="--bxtr-marker-color: <?php echo esc_attr($marker_color); ?>; --bxtr-marker-number-color: <?php echo esc_attr($marker_number_color); ?>; --bxtr-poi-marker-color: <?php echo esc_attr($poi_marker_color); ?>; --bxtr-route-color: <?php echo esc_attr($route_color); ?>; --bxtr-map-height: 360px; --bxtr-border-radius: <?php echo esc_attr($border_radius); ?>;">
                </div>

                <div class="bxtr-style-tips">
                    <h3><?php esc_html_e('Common style overrides', 'baxtersweb-maps'); ?></h3>
                    <p class="description"><?php esc_html_e('Use these classes in your theme or custom CSS when you need finer control than the settings above.', 'baxtersweb-maps'); ?></p>
                    <table class="widefat bxtr-style-tips__table">
                        <thead>
                            <tr>
                                <th scope="col"><?php esc_html_e('Description', 'baxtersweb-maps'); ?></th>
                                <th scope="col"><?php esc_html_e('CSS class', 'baxtersweb-maps'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td><?php esc_html_e('POI label marker: padding, text size, colour, or border radius.', 'baxtersweb-maps'); ?></td><td><code>.bxtr-marker__poi</code></td></tr>
                            <tr><td><?php esc_html_e('Map marker pin: size, colour, or shadow.', 'baxtersweb-maps'); ?></td><td><code>.bxtr-marker__route</code></td></tr>
                            <tr><td><?php esc_html_e('Map marker label: number or letter colour and positioning.', 'baxtersweb-maps'); ?></td><td><code>.bxtr-marker__number</code></td></tr>
                            <tr><td><?php esc_html_e('Popup wrapper: spacing, typography, background, and border radius.', 'baxtersweb-maps'); ?></td><td><code>.bxtr-popup</code></td></tr>
                            <tr><td><?php esc_html_e('Popup description area: paragraph and content styling.', 'baxtersweb-maps'); ?></td><td><code>.bxtr-popup__description</code></td></tr>
                            <tr><td><?php esc_html_e('Map container: layout styling, height, or border radius.', 'baxtersweb-maps'); ?></td><td><code>.bxtr-map</code></td></tr>
                        </tbody>
                    </table>
                </div>
            </aside>
        </div>

        <?php submit_button(__('Save Settings', 'baxtersweb-maps')); ?>
    </form>

    <hr>

    <div class="bxtr-reference-grid">
        <section>
            <h2><?php esc_html_e('Field names used by this plugin', 'baxtersweb-maps'); ?></h2>
            <p><?php echo wp_kses_post(__('If you prefer to create fields manually, use these exact field names. The OpenStreetMap field must use <strong>Return Format: Raw data</strong> and should allow <strong>maximum 1 marker</strong> per map marker.', 'baxtersweb-maps')); ?></p>
            <table class="widefat striped">
                <thead><tr><th><?php esc_html_e('Field', 'baxtersweb-maps'); ?></th><th><?php esc_html_e('Type', 'baxtersweb-maps'); ?></th><th><?php esc_html_e('Notes', 'baxtersweb-maps'); ?></th></tr></thead>
                <tbody>
                    <tr><td><code>bxtr_map_markers</code></td><td><?php esc_html_e('Repeater', 'baxtersweb-maps'); ?></td><td><?php esc_html_e('Each row is treated as one map marker.', 'baxtersweb-maps'); ?></td></tr>
                    <tr><td><code>bxtr_marker_title</code></td><td><?php esc_html_e('Text', 'baxtersweb-maps'); ?></td><td><?php esc_html_e('Optional title for the map marker.', 'baxtersweb-maps'); ?></td></tr>
                    <tr><td><code>bxtr_marker_coordinates</code></td><td><?php esc_html_e('OpenStreetMap', 'baxtersweb-maps'); ?></td><td><?php esc_html_e('Required. Labelled Marker Coordinates. Return Format must be Raw data. Max markers should be 1.', 'baxtersweb-maps'); ?></td></tr>
                    <tr><td><code>bxtr_marker_description</code></td><td><?php esc_html_e('WYSIWYG / Textarea / Text', 'baxtersweb-maps'); ?></td><td><?php esc_html_e('Optional. Displayed inside the marker popup.', 'baxtersweb-maps'); ?></td></tr>
                    <tr><td><code>bxtr_poi_markers</code></td><td><?php esc_html_e('Repeater', 'baxtersweb-maps'); ?></td><td><?php esc_html_e('Optional supporting markers. These do not change the route line.', 'baxtersweb-maps'); ?></td></tr>
                    <tr><td><code>bxtr_poi_title</code></td><td><?php esc_html_e('Text', 'baxtersweb-maps'); ?></td><td><?php esc_html_e('Optional title for the point of interest.', 'baxtersweb-maps'); ?></td></tr>
                    <tr><td><code>bxtr_poi_type</code></td><td><?php esc_html_e('Text', 'baxtersweb-maps'); ?></td><td><?php esc_html_e('Optional label shown inside the POI marker, for example Hotel, Toilet, Gate, Viewpoint, Parking, or Cash.', 'baxtersweb-maps'); ?></td></tr>
                    <tr><td><code>bxtr_poi_coordinates</code></td><td><?php esc_html_e('OpenStreetMap', 'baxtersweb-maps'); ?></td><td><?php esc_html_e('Required for each POI row. Return Format must be Raw data. Max markers should be 1.', 'baxtersweb-maps'); ?></td></tr>
                    <tr><td><code>bxtr_poi_description</code></td><td><?php esc_html_e('WYSIWYG / Textarea / Text', 'baxtersweb-maps'); ?></td><td><?php esc_html_e('Optional. Displayed inside the POI popup.', 'baxtersweb-maps'); ?></td></tr>
                </tbody>
            </table>
        </section>
        <section class="bxtr-card bxtr-checklist-card">
            <h2><?php esc_html_e('Test checklist', 'baxtersweb-maps'); ?></h2>
            <ul>
                <li><?php esc_html_e('Advanced Custom Fields Pro is active.', 'baxtersweb-maps'); ?></li>
                <li><?php esc_html_e('ACF OpenStreetMap Field is active.', 'baxtersweb-maps'); ?></li>
                <li><?php esc_html_e('The field group has been created or updated.', 'baxtersweb-maps'); ?></li>
                <li><?php esc_html_e('Map markers have been saved on a post.', 'baxtersweb-maps'); ?></li>
                <li><?php esc_html_e('The shortcode has been added to the content or template.', 'baxtersweb-maps'); ?></li>
            </ul>
        </section>
    </div>

</div>
