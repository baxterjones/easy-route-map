<?php if (!defined('ABSPATH')) exit; ?>

<?php if (empty($stops)) : ?>

    <p class="irm-map__notice">No route points found.</p>

<?php else : ?>

    <div
        id="irm-map"
        class="irm-map"
        style="--irm-marker-color: <?php echo esc_attr($marker_color); ?>; --irm-route-color: <?php echo esc_attr($route_color); ?>;"
        data-route-color="<?php echo esc_attr($route_color); ?>"
        data-stops="<?php echo esc_attr(wp_json_encode($stops)); ?>">
    </div>

<?php endif; ?>
