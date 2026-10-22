<?php
/**
 * Single Product Template - Central MIDI
 *
 * Template 100% autônomo e desacoplado do Flatsome para exibição de arquivo MIDI.
 * Substitui o layout UX Builder legado por uma experiência Dark/Light moderna com tokens.
 *
 * @package CentralMidi_Child
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();

while (have_posts()) : the_post();
    global $product;
    if (!$product) {
        $product = wc_get_product(get_the_ID());
    }

    $product_id = $product->get_id();
    $midi = class_exists('CentralMidi_DB') ? CentralMidi_DB::get_midi_by_product($product_id) : null;
    $audio_url = $midi ? $midi['demo_url'] : '';
    $classificacao = $midi ? $midi['classificacao'] : '';
    $formato = $midi ? $midi['formato'] : 'MIDI';
    $artista_nome = $midi ? $midi['artista_nome'] : '';

    // Artista fallback via categorias se não vier do DB proprietário
    if (empty($artista_nome)) {
        $terms = get_the_terms($product_id, 'product_cat');
        if (!empty($terms) && !is_wp_error($terms)) {
            $artista_nome = $terms[0]->name;
        }
    }

    // Gênero musical
    $generos = get_the_terms($product_id, 'genero_musical');
    $genero_nome = (!empty($generos) && !is_wp_error($generos)) ? $generos[0]->name : '';
    $genero_link = (!empty($generos) && !is_wp_error($generos)) ? get_term_link($generos[0]) : '';

    // Mês de lançamento
    $meses = get_the_terms($product_id, 'mes_de_lancamento');
    $mes_nome = (!empty($meses) && !is_wp_error($meses)) ? $meses[0]->name : '';
    $mes_link = (!empty($meses) && !is_wp_error($meses)) ? get_term_link($meses[0]) : '';

    $permalink = get_permalink($product_id);
    $share_title = urlencode(get_the_title());
    $share_url = urlencode($permalink);
?>

<div class="cm-single-product-page">
    <div class="shop-container">
        <!-- Breadcrumbs Moderno -->
        <nav class="cm-search-breadcrumb cm-product-breadcrumb" aria-label="Navegação">
            <a href="<?php echo esc_url(home_url('/')); ?>"><i class="ri-home-4-line"></i> Início</a>
            <span class="cm-breadcrumb-sep"><i class="ri-arrow-right-s-line"></i></span>
            <a href="<?php echo esc_url(home_url('/midis/')); ?>">Catálogo de MIDIs</a>
            <?php if (!empty($artista_nome)) : ?>
                <span class="cm-breadcrumb-sep"><i class="ri-arrow-right-s-line"></i></span>
                <a href="<?php echo esc_url(home_url('/artistas/')); ?>"><?php echo esc_html($artista_nome); ?></a>
            <?php endif; ?>
            <span class="cm-breadcrumb-sep"><i class="ri-arrow-right-s-line"></i></span>
            <span class="cm-breadcrumb-current"><?php the_title(); ?></span>
        </nav>

        <div class="cm-product-layout">
            <!-- Coluna Principal / Informações e Player -->
            <div class="cm-product-main">
                <div class="cm-product-card-hero">
                    <div class="cm-product-meta-badges">
                        <span class="cm-badge cm-badge-format"><i class="ri-file-music-line"></i> <?php echo esc_html($formato ?: 'MIDI'); ?></span>
                        <?php if (!empty($classificacao)) : ?>
                            <span class="cm-badge cm-badge-rlm">#<?php echo esc_html($classificacao); ?></span>
                        <?php endif; ?>
                        <?php if (!empty($genero_nome)) : ?>
                            <a href="<?php echo esc_url($genero_link); ?>" class="cm-badge cm-badge-genre">
                                <i class="ri-disc-line"></i> <?php echo esc_html($genero_nome); ?>
                            </a>
                        <?php endif; ?>
                    </div>

                    <h1 class="cm-product-title"><?php the_title(); ?></h1>

                    <?php if (!empty($artista_nome)) : ?>
                        <div class="cm-product-artist">
                            <i class="ri-user-star-line"></i> Artista: <strong><?php echo esc_html($artista_nome); ?></strong>
                        </div>
                    <?php endif; ?>

                    <!-- Bloco do Player Demo em Destaque -->
                    <?php if (!empty($audio_url)) : ?>
                        <div class="cm-product-player-box">
                            <div class="cm-player-header">
                                <span class="cm-player-tag"><i class="ri-volume-up-line"></i> Demonstração em Áudio</span>
                                <span class="cm-player-hint">Ouça o preview antes de comprar</span>
                            </div>
                            <div class="cm-player-controls-wrap">
                                <button type="button" class="cm-single-play-btn cm-play-trigger"
                                        data-audio="<?php echo esc_url($audio_url); ?>"
                                        data-id="<?php echo esc_attr($product_id); ?>"
                                        aria-label="Reproduzir demonstração de <?php echo esc_attr(get_the_title()); ?>">
                                    <i class="ri-play-fill cm-play-icon"></i>
                                    <i class="ri-pause-fill cm-pause-icon" style="display: none;"></i>
                                </button>
                                <div class="cm-player-track-info">
                                    <span class="cm-track-title-preview"><?php the_title(); ?></span>
                                    <span class="cm-track-status-text">Clique para ouvir a demo</span>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Informação / Classificação RLM Oficial -->
                    <div class="cm-product-rlm-box">
                        <div class="cm-rlm-title"><i class="ri-information-line"></i> Classificação Musical (#RLM)</div>
                        <ul class="cm-rlm-list">
                            <li><strong>#M</strong> — Somente Melodia gravada</li>
                            <li><strong>#L</strong> — Somente Letra sincronizada</li>
                            <li><strong>#RLM</strong> — Melodia e Letra perfeitamente sincronizadas</li>
                        </ul>
                        <span class="cm-rlm-note">Sem classificação indicada? Considera-se formato instrumental sem letra embutida.</span>
                    </div>

                    <!-- Compartilhamento Social -->
                    <div class="cm-product-share">
                        <span class="cm-share-label">Compartilhar:</span>
                        <div class="cm-share-buttons">
                            <a href="https://wa.me/?text=<?php echo $share_title . '%20' . $share_url; ?>" target="_blank" rel="noopener noreferrer" class="cm-share-btn cm-share-wa" title="WhatsApp">
                                <i class="ri-whatsapp-line"></i>
                            </a>
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $share_url; ?>" target="_blank" rel="noopener noreferrer" class="cm-share-btn cm-share-fb" title="Facebook">
                                <i class="ri-facebook-fill"></i>
                            </a>
                            <a href="https://twitter.com/intent/tweet?text=<?php echo $share_title; ?>&url=<?php echo $share_url; ?>" target="_blank" rel="noopener noreferrer" class="cm-share-btn cm-share-tw" title="X / Twitter">
                                <i class="ri-twitter-x-line"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Coluna Lateral de Compra / Preço & Checkout -->
            <div class="cm-product-sidebar">
                <div class="cm-buy-card">
                    <div class="cm-buy-price-wrap">
                        <span class="cm-buy-price-label">Valor do arquivo:</span>
                        <div class="cm-buy-price">
                            <?php echo $product->get_price_html(); ?>
                        </div>
                    </div>

                    <!-- Formulário de Compra Direta do WooCommerce -->
                    <form class="cart cm-single-cart-form" action="<?php echo esc_url(apply_filters('woocommerce_add_to_cart_form_action', $permalink)); ?>" method="post" enctype='multipart/form-data'>
                        <input type="hidden" name="quantity" value="1" />
                        <button type="submit" name="add-to-cart" value="<?php echo esc_attr($product_id); ?>" class="cm-btn cm-btn-buy-single">
                            <i class="ri-shopping-cart-2-line"></i> Comprar Agora
                        </button>
                    </form>

                    <!-- Lista de Vantagens e Garantias -->
                    <div class="cm-buy-features">
                        <div class="cm-feature-item">
                            <i class="ri-download-cloud-2-line"></i>
                            <div>
                                <strong>Download Imediato</strong>
                                <span>Disponibilizado na sua conta logo após a confirmação.</span>
                            </div>
                        </div>
                        <div class="cm-feature-item">
                            <i class="ri-shield-check-line"></i>
                            <div>
                                <strong>Pagamento 100% Seguro</strong>
                                <span>PIX, Cartão de Crédito e Mercado Pago.</span>
                            </div>
                        </div>
                        <div class="cm-feature-item">
                            <i class="ri-customer-service-2-line"></i>
                            <div>
                                <strong>Suporte Dedicado</strong>
                                <span>Atendimento direto pelo WhatsApp Central MIDI.</span>
                            </div>
                        </div>
                    </div>

                    <!-- Ficha Técnica Rápida -->
                    <div class="cm-specs-list">
                        <?php if (!empty($mes_nome)) : ?>
                            <div class="cm-spec-row">
                                <span class="cm-spec-k">Lançamento:</span>
                                <span class="cm-spec-v"><a href="<?php echo esc_url($mes_link); ?>"><?php echo esc_html($mes_nome); ?></a></span>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($genero_nome)) : ?>
                            <div class="cm-spec-row">
                                <span class="cm-spec-k">Gênero:</span>
                                <span class="cm-spec-v"><a href="<?php echo esc_url($genero_link); ?>"><?php echo esc_html($genero_nome); ?></a></span>
                            </div>
                        <?php endif; ?>
                        <div class="cm-spec-row">
                            <span class="cm-spec-k">Compatibilidade:</span>
                            <span class="cm-spec-v">Teclados, PCs, VSTs e Players MIDI</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Músicas Relacionadas / Mais do Artista -->
        <?php
        woocommerce_related_products(array(
            'posts_per_page' => 8,
            'columns'        => 4,
            'orderby'        => 'rand',
        ));
        ?>
    </div>
</div>

<script>
// Integração instantânea com a engine Howler/Audio existente
document.addEventListener('DOMContentLoaded', function() {
    var playBtn = document.querySelector('.cm-single-play-btn');
    if (!playBtn) return;

    playBtn.addEventListener('click', function(e) {
        e.preventDefault();
        var audio = this.getAttribute('data-audio');
        if (audio && typeof playAudio === 'function') {
            playAudio(audio, null, this);
        }
    });
});
</script>

<?php
endwhile;

get_footer();
