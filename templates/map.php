<?php if (!defined('ABSPATH')) exit; ?>

<?php if (empty($stops)) : ?>

    <div class="erm-map__notice" role="status">
        <strong>Easy Route Map: no route points found.</strong>

        <?php if (!empty($diagnostics)) : ?>
            <ul>
                <?php foreach ($diagnostics as $diagnostic) : ?>
                    <li><?php echo esc_html($diagnostic); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>

<?php else : ?>

    <?php if (!empty($diagnostics) && current_user_can('edit_posts')) : ?>
        <div class="erm-map__notice erm-map__notice--info" role="status">
            <ul>
                <?php foreach ($diagnostics as $diagnostic) : ?>
                    <li><?php echo esc_html($diagnostic); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div
        id="erm-map"
        class="erm-map"
        style="--erm-marker-color: <?php echo esc_attr($marker_color); ?>; --erm-route-color: <?php echo esc_attr($route_color); ?>; --erm-map-height: <?php echo esc_attr($map_height); ?>; --erm-border-radius: <?php echo esc_attr($border_radius); ?>;"
        data-route-color="<?php echo esc_attr($route_color); ?>"
        data-marker-label="<?php echo esc_attr($marker_label_prefix); ?>"
        data-stops="<?php echo esc_attr(wp_json_encode($stops)); ?>">
    </div>

<?php endif; ?>
