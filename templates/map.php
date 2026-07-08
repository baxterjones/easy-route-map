<?php if (!defined('ABSPATH')) exit; ?>

<?php if (empty($stops) && empty($pois)) : ?>

    <div class="bxtr-map__notice" role="status">
        <?php esc_html_e('Baxtersweb Maps found no map markers or points of interest.', 'baxtersweb-maps'); ?>
    </div>

<?php else : ?>

    <?php if (!empty($diagnostics) && current_user_can('edit_posts')) : ?>
        <div class="bxtr-map__notice bxtr-map__notice--info" role="status">
            <?php foreach ($diagnostics as $bxtr_diagnostic) : ?>
                <p><?php echo esc_html($bxtr_diagnostic); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div
        id="<?php echo esc_attr($map_id); ?>"
        class="bxtr-map"
        style="--bxtr-marker-color: <?php echo esc_attr($config['marker_color']); ?>; --bxtr-marker-number-color: <?php echo esc_attr($config['marker_number_color']); ?>; --bxtr-poi-marker-color: <?php echo esc_attr($config['poi_marker_color']); ?>; --bxtr-route-color: <?php echo esc_attr($config['route_color']); ?>; --bxtr-map-height: <?php echo esc_attr($config['map_height']); ?>; --bxtr-border-radius: <?php echo esc_attr($config['border_radius']); ?>;"
        data-template="<?php echo esc_attr($config['template']); ?>"
        data-route-color="<?php echo esc_attr($config['route_color']); ?>"
        data-marker-color="<?php echo esc_attr($config['marker_color']); ?>"
        data-marker-number-color="<?php echo esc_attr($config['marker_number_color']); ?>"
        data-poi-marker-color="<?php echo esc_attr($config['poi_marker_color']); ?>"
        data-marker-sequence="<?php echo esc_attr($config['marker_sequence']); ?>"
        data-poi-label="<?php esc_attr_e('Point of Interest', 'baxtersweb-maps'); ?>"
        data-draw-route="<?php echo esc_attr($config['draw_route']); ?>"
        data-tile-style="<?php echo esc_attr($config['map_tile_style']); ?>"
        data-stops="<?php echo esc_attr(wp_json_encode($stops)); ?>"
        data-pois="<?php echo esc_attr(wp_json_encode($pois)); ?>">
    </div>

<?php endif; ?>
