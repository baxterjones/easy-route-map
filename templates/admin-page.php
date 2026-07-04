<div class="wrap erm-admin-page">

    <div class="erm-admin-header">
        <div>
            <h1>Easy Route Map</h1>
            <p class="erm-admin-page__intro">
                Create interactive Leaflet route maps from route points saved on posts, pages, or custom post type entries.
            </p>
        </div>
        <span class="erm-version">Version <?php echo esc_html(ERM_VERSION); ?></span>
    </div>

    <?php if (!empty($this->message)) : ?>
        <div class="notice notice-<?php echo esc_attr($this->message_type); ?> is-dismissible">
            <p><?php echo esc_html($this->message); ?></p>
        </div>
    <?php endif; ?>

    <section class="erm-card erm-requirements-card">
        <h2>Requirements</h2>
        <ul class="erm-status-list">
            <li class="<?php echo $acf_active ? 'is-ok' : 'is-missing'; ?>">
                <span><?php echo $acf_active ? '✓' : '×'; ?></span>
                Advanced Custom Fields
            </li>
            <li class="<?php echo $osm_active ? 'is-ok' : 'is-missing'; ?>">
                <span><?php echo $osm_active ? '✓' : '×'; ?></span>
                ACF OpenStreetMap Field
            </li>
        </ul>
        <?php if (!$acf_active || !$osm_active) : ?>
            <p>Install and activate the missing plugins first. Easy Route Map currently uses ACF fields for route point editing.</p>
        <?php else : ?>
            <p>Requirements are active. You can create the recommended ACF fields automatically.</p>
        <?php endif; ?>
    </section>

    <div class="erm-setup-grid">
        <div class="erm-card">
            <h2>1. Setup fields</h2>
            <p>Create the recommended ACF field group instead of manually setting up repeater and map fields.</p>

            <form method="post" action="">
                <?php wp_nonce_field('erm_setup_fields', 'erm_setup_fields_nonce'); ?>

                <fieldset>
                    <legend><strong>Show route fields on:</strong></legend>

                    <?php foreach ($post_types as $post_type => $label) : ?>
                        <label class="erm-checkbox-row">
                            <input
                                type="checkbox"
                                name="erm_field_post_types[]"
                                value="<?php echo esc_attr($post_type); ?>"
                                <?php checked(in_array($post_type, (array) $selected_post_types, true)); ?>>
                            <?php echo esc_html($label); ?>
                            <code><?php echo esc_html($post_type); ?></code>
                        </label>
                    <?php endforeach; ?>
                </fieldset>

                <?php submit_button($field_group_exists ? 'ACF fields already created' : 'Set up ACF fields for me', 'primary', 'submit', false, (!$acf_active || !$osm_active || $field_group_exists) ? ['disabled' => 'disabled'] : []); ?>
            </form>

            <?php if ($field_group_exists) : ?>
                <p class="erm-note">The Easy Route Map ACF field group already exists. Duplicate field groups are blocked to keep the setup clean.</p>
            <?php endif; ?>
        </div>

        <div class="erm-card">
            <h2>2. Add route points</h2>
            <p>Edit a selected post type and look for the <strong>Route Points</strong> field group.</p>
            <ol>
                <li>Add one row per route point.</li>
                <li>Drop one marker in the <strong>Point Map</strong> field.</li>
                <li>Drag rows to control the route order.</li>
                <li>Save or update the post.</li>
            </ol>
        </div>

        <div class="erm-card">
            <h2>3. Display the map</h2>
            <p>Add this shortcode to the same post, page, CPT content, or template output:</p>
            <div class="erm-shortcode-row">
                <code id="erm-shortcode-default">[easy_route_map]</code>
                <button type="button" class="button erm-copy-shortcode" data-copy-target="erm-shortcode-default">Copy</button>
            </div>
            <p>Target a specific post ID:</p>
            <div class="erm-shortcode-row">
                <code id="erm-shortcode-id">[easy_route_map id=&quot;123&quot;]</code>
                <button type="button" class="button erm-copy-shortcode" data-copy-target="erm-shortcode-id">Copy</button>
            </div>
        </div>
    </div>

    <hr>

    <h2>Settings</h2>

    <form method="post" action="">
        <?php wp_nonce_field('erm_save_settings', 'erm_settings_nonce'); ?>

        <table class="form-table" role="presentation">
            <tbody>
                <tr>
                    <th scope="row"><label for="erm_marker_color">Marker colour</label></th>
                    <td>
                        <input type="text" id="erm_marker_color" name="erm_marker_color" value="<?php echo esc_attr($marker_color); ?>" class="regular-text" placeholder="#3d874d" pattern="^#[a-fA-F0-9]{6}$">
                        <p class="description">Use a six-character web hex colour, for example <code>#3d874d</code>.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="erm_route_color">Route colour</label></th>
                    <td>
                        <input type="text" id="erm_route_color" name="erm_route_color" value="<?php echo esc_attr($route_color); ?>" class="regular-text" placeholder="#3388ff" pattern="^#[a-fA-F0-9]{6}$">
                        <p class="description">Use a six-character web hex colour. Default is Leaflet blue: <code>#3388ff</code>.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="erm_map_height">Map height</label></th>
                    <td>
                        <input type="text" id="erm_map_height" name="erm_map_height" value="<?php echo esc_attr($map_height); ?>" class="regular-text" placeholder="500px">
                        <p class="description">Enter any valid CSS height value. Examples: <code>500px</code>, <code>70vh</code>, <code>40rem</code>. Include the unit.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="erm_border_radius">Border radius</label></th>
                    <td>
                        <input type="text" id="erm_border_radius" name="erm_border_radius" value="<?php echo esc_attr($border_radius); ?>" class="regular-text" placeholder="12px">
                        <p class="description">Enter any valid CSS border-radius value. Examples: <code>12px</code>, <code>1rem</code>, <code>50%</code>.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="erm_marker_label">Marker label</label></th>
                    <td>
                        <select id="erm_marker_label" name="erm_marker_label">
                            <?php foreach (['Stop', 'Point', 'Day', 'Location', 'Custom'] as $label_option) : ?>
                                <option value="<?php echo esc_attr($label_option); ?>" <?php selected($marker_label, $label_option); ?>><?php echo esc_html($label_option); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <p class="description">Controls the popup label before the route number, for example <code>Day 1</code>, <code>Stop 1</code>, or <code>Location 1</code>.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="erm_custom_marker_label">Custom marker label</label></th>
                    <td>
                        <input type="text" id="erm_custom_marker_label" name="erm_custom_marker_label" value="<?php echo esc_attr($custom_marker_label); ?>" class="regular-text" placeholder="Stage">
                        <p class="description">Used only when Marker label is set to Custom. Example: <code>Stage</code> becomes <code>Stage 1</code>.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Plugin data on uninstall</th>
                    <td>
                        <fieldset>
                            <label>
                                <input type="radio" name="erm_uninstall_mode" value="keep" <?php checked($uninstall_mode, 'keep'); ?>>
                                Keep all data <span class="description">Recommended. Preserves route points, generated ACF fields, and settings if the plugin is deleted.</span>
                            </label><br>
                            <label>
                                <input type="radio" name="erm_uninstall_mode" value="remove" <?php checked($uninstall_mode, 'remove'); ?>>
                                Remove plugin data <span class="description">Deletes Easy Route Map settings and the generated ACF field group on uninstall. Existing post meta is preserved for safety.</span>
                            </label>
                        </fieldset>
                    </td>
                </tr>
            </tbody>
        </table>

        <?php submit_button('Save Settings'); ?>
    </form>

    <hr>

    <div class="erm-reference-grid">
        <section>
            <h2>Field names used by this plugin</h2>
            <p>If you prefer to create fields manually, use these exact field names. The OpenStreetMap field must use <strong>Return Format: Raw data</strong> and should allow <strong>maximum 1 marker</strong> per route point.</p>
            <table class="widefat striped">
                <thead><tr><th>Field</th><th>Type</th><th>Notes</th></tr></thead>
                <tbody>
                    <tr><td><code>erm_route_points</code></td><td>Repeater</td><td>Each row is treated as one route point.</td></tr>
                    <tr><td><code>erm_point_title</code></td><td>Text</td><td>Optional title for the route point.</td></tr>
                    <tr><td><code>erm_point_location</code></td><td>OpenStreetMap</td><td>Required. Labelled Point Map. Return Format must be Raw data. Max markers should be 1.</td></tr>
                    <tr><td><code>erm_point_description</code></td><td>WYSIWYG / Textarea / Text</td><td>Optional. Displayed inside the marker popup.</td></tr>
                </tbody>
            </table>
        </section>
        <section class="erm-card erm-checklist-card">
            <h2>Test checklist</h2>
            <ul>
                <li>ACF is active.</li>
                <li>ACF OpenStreetMap Field is active.</li>
                <li>The field group has been created or updated.</li>
                <li>Route points have been saved on a post.</li>
                <li>The shortcode has been added to the content or template.</li>
            </ul>
        </section>
    </div>

</div>
