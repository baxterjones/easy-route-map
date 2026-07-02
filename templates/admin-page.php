<div class="wrap irm-admin-page">

    <h1>Itinerary Route Map</h1>

    <p>
        This plugin creates a single interactive Leaflet route map from an ACF repeater containing OpenStreetMap location fields.
    </p>

    <hr>

    <h2>Shortcode</h2>

    <p>Add this shortcode to an itinerary page:</p>

    <code>[itinerary_route_map]</code>

    <p>You can also target a specific post ID:</p>

    <code>[itinerary_route_map id="123"]</code>

    <h2>Settings</h2>

    <form method="post" action="">
        <?php wp_nonce_field('irm_save_settings', 'irm_settings_nonce'); ?>

        <table class="form-table" role="presentation">
            <tbody>
                <tr>
                    <th scope="row">
                        <label for="irm_marker_color">Marker colour</label>
                    </th>
                    <td>
                        <input
                            type="text"
                            id="irm_marker_color"
                            name="irm_marker_color"
                            value="<?php echo esc_attr($marker_color); ?>"
                            class="regular-text"
                            placeholder="#3d874d"
                            pattern="^#[a-fA-F0-9]{6}$">
                        <p class="description">Use a six-character web hex colour, for example <code>#3d874d</code>.</p>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="irm_route_color">Route colour</label>
                    </th>
                    <td>
                        <input
                            type="text"
                            id="irm_route_color"
                            name="irm_route_color"
                            value="<?php echo esc_attr($route_color); ?>"
                            class="regular-text"
                            placeholder="#3388ff"
                            pattern="^#[a-fA-F0-9]{6}$">
                        <p class="description">Use a six-character web hex colour. Default is Leaflet blue: <code>#3388ff</code>.</p>
                    </td>
                </tr>
            </tbody>
        </table>

        <?php submit_button('Save Settings'); ?>
    </form>

    <hr>

    <h2>Requirements</h2>

    <ul>
        <li>Advanced Custom Fields</li>
        <li>ACF OpenStreetMap Field</li>
        <li>An itinerary repeater field on the current post</li>
    </ul>

    <h2>Expected ACF Fields</h2>

    <table class="widefat striped">

        <thead>
            <tr>
                <th>Field</th>
                <th>Type</th>
                <th>Notes</th>
            </tr>
        </thead>

        <tbody>
            <tr>
                <td><code>itinerary_day_items</code></td>
                <td>Repeater</td>
                <td>Each row is treated as one route stop.</td>
            </tr>

            <tr>
                <td><code>itinerary_day_item_title</code></td>
                <td>Text</td>
                <td>Used internally as the stop title.</td>
            </tr>

            <tr>
                <td><code>itinerary_day_item_location_coordinates</code></td>
                <td>OpenStreetMap</td>
                <td>Used for the route marker coordinates.</td>
            </tr>

            <tr>
                <td><code>itinerary_day_item_location_description</code></td>
                <td>Textarea / WYSIWYG / Text</td>
                <td>Optional. Displayed inside the marker popup below the day heading.</td>
            </tr>
        </tbody>

    </table>

</div>
