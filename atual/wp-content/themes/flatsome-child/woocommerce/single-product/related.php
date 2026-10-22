<?php
/**
 * Related Products Template - Central MIDI
 *
 * Utiliza o grid moderno e o componente card-midi.php nativo do Central MIDI
 *
 * @package CentralMidi_Child
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!empty($related_products)) :
    $heading = apply_filters('woocommerce_product_related_products_heading', __('Músicas Relacionadas', 'centralmidi'));

    $cm_rel_ids = array();
    foreach ($related_products as $rel_prod) {
        $cm_rel_ids[] = (int) $rel_prod->get_id();
    }

    $cm_rel_map = ($cm_rel_ids && class_exists('CentralMidi_DB'))
        ? CentralMidi_DB::get_midis_by_products($cm_rel_ids)
        : array();

    if (function_exists('cmidi_prime_cards')) {
        cmidi_prime_cards($cm_rel_ids);
    }
?>

    <div class="cm-related-section">
        <?php if ($heading) : ?>
            <div class="cm-related-header">
                <h3 class="cm-related-title">
                    <i class="ri-music-2-line"></i> <?php echo esc_html($heading); ?>
                </h3>
                <span class="cm-related-sub">Arquivos MIDI sugeridos para você</span>
            </div>
        <?php endif; ?>

        <div class="cm-tracks-grid">
            <?php foreach ($related_products as $related_product) : ?>
                <?php
                $rel_id = $related_product->get_id();
                get_template_part('template-parts/card-midi', null, array(
                    'product_id' => $rel_id,
                    'midi'       => isset($cm_rel_map[$rel_id]) ? $cm_rel_map[$rel_id] : null,
                ));
                ?>
            <?php endforeach; ?>
        </div>
    </div>

<?php
endif;

wp_reset_postdata();
