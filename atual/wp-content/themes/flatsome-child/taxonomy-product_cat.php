<?php
/**
 * Custom Taxonomy Template for Product Categories (Artistas)
 * Layout Moderno Central MIDI usando o componente reutilizável card-midi.php
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Desativa o header antigo de categoria do tema pai Flatsome
remove_action('flatsome_after_header', 'flatsome_category_header');

get_header();

global $wp_query;
$term = get_queried_object();
$term_id = $term->term_id;
$term_name = $term->name;

// Foto do artista: prioriza o registro do plugin (tabela wp_centralmidi_artistas)
// e só depois o thumbnail_id do termo — antes a foto do plugin era ignorada.
$thumbnail_id = (int) get_term_meta( $term_id, 'thumbnail_id', true );
$artist_row   = ( class_exists( 'CentralMidi_DB' ) ? CentralMidi_DB::get_artista_by_nome( $term_name ) : null );
$is_artist    = ! empty( $artist_row );
if ( ! $thumbnail_id && $is_artist && ! empty( $artist_row->foto_id ) ) {
    $thumbnail_id = (int) $artist_row->foto_id;
}
$image_url = $thumbnail_id ? wp_get_attachment_image_url( $thumbnail_id, 'medium' ) : '';

// Iniciais para o avatar quando o artista não tem foto cadastrada.
$cm_initials = '';
foreach ( preg_split( '/\s+/', trim( $term_name ) ) as $cm_word ) {
    if ( '' !== $cm_word ) {
        $cm_initials .= mb_substr( $cm_word, 0, 1 );
    }
}
$cm_initials = mb_substr( $cm_initials, 0, 2 );

$artist_description = term_description( $term_id, 'product_cat' );
$paged = max(1, get_query_var('paged'));
?>

<div class="artista-detalhe-page cm-archive-page">
    <div class="cm-search-hero cm-archive-hero cm-artist-hero">
        <div class="shop-container">
            <div class="cm-artist-hero-inner">
            <nav class="cm-search-breadcrumb" aria-label="Navegação">
                <a href="<?php echo esc_url(home_url('/')); ?>"><i class="ri-home-4-line"></i> Início</a>
                <span class="cm-breadcrumb-sep"><i class="ri-arrow-right-s-line"></i></span>
                <a href="<?php echo esc_url(home_url('/midis/')); ?>">Catálogo de MIDIs</a>
                <span class="cm-breadcrumb-sep"><i class="ri-arrow-right-s-line"></i></span>
                <a href="<?php echo esc_url(home_url('/artistas/')); ?>">Artistas</a>
                <span class="cm-breadcrumb-sep"><i class="ri-arrow-right-s-line"></i></span>
                <span class="cm-breadcrumb-current"><?php echo esc_html($term_name); ?></span>
            </nav>

            <div class="cm-artist-profile">
                <div class="cm-artist-profile-media">
                    <?php if ($image_url) : ?>
                        <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($term_name); ?>">
                    <?php elseif ($is_artist) : ?>
                        <span><?php echo esc_html($cm_initials); ?></span>
                    <?php else : ?>
                        <i class="ri-folder-music-line"></i>
                    <?php endif; ?>
                </div>

                <div class="cm-artist-profile-info">
                    <span class="cm-search-badge">
                        <i class="ri-<?php echo $is_artist ? 'user-voice-line' : 'folder-music-line'; ?>"></i>
                        <?php echo $is_artist ? 'Artista / Banda' : 'Categoria'; ?>
                    </span>
                    <h1 class="cm-search-title"><?php echo esc_html($term_name); ?></h1>
                    <p class="cm-search-subtitle">
                        <?php if ($wp_query->found_posts > 0) : ?>
                            <?php
                            printf(
                                /* translators: 1: quantidade de MIDIs, 2: nome do artista/categoria. */
                                esc_html__( '%1$s MIDIs disponíveis de %2$s.', 'centralmidi' ),
                                '<strong>' . esc_html( number_format_i18n( $wp_query->found_posts ) ) . '</strong>',
                                esc_html( $term_name )
                            );
                            ?>
                        <?php else : ?>
                            Nenhum MIDI encontrado para este artista.
                        <?php endif; ?>
                    </p>
                    <?php if ($artist_description) : ?>
                        <div class="cm-term-desc"><?php echo wp_kses_post(wpautop($artist_description)); ?></div>
                    <?php endif; ?>
                </div>
            </div>
            </div>
        </div>
    </div>

    <div class="shop-container">
        <?php if (have_posts()) : ?>
            <div class="cm-search-toolbar">
                <div class="cm-toolbar-info">
                    <i class="ri-music-2-line"></i>
                    <span>Exibindo <strong><?php echo esc_html($wp_query->post_count); ?></strong> de <strong><?php echo esc_html($wp_query->found_posts); ?></strong> músicas</span>
                </div>
                <div class="cm-toolbar-actions">
                    <button type="button" class="cm-btn cm-btn-primary cm-play-monthly-playlist" title="Reproduzir todas as músicas deste artista">
                        <i class="ri-play-list-2-fill"></i> Reproduzir Todas
                    </button>
                    <a href="<?php echo esc_url(home_url('/artistas/')); ?>" class="cm-btn-catalog-link">
                        <i class="ri-arrow-left-line"></i> Lista de artistas
                    </a>
                </div>
            </div>

            <!-- Grade de MIDIs usando o componente centralizado card-midi -->
            <?php
            // Leitura em lote para otimizar queries
            $cm_result_ids = array();
            if (have_posts()) {
                foreach ($wp_query->posts as $cm_post) {
                    $cm_result_ids[] = (int) $cm_post->ID;
                }
            }
            $cm_result_map = ($cm_result_ids && class_exists('CentralMidi_DB'))
                ? CentralMidi_DB::get_midis_by_products($cm_result_ids)
                : array();
            if (function_exists('cmidi_prime_cards')) {
                cmidi_prime_cards($cm_result_ids);
            }
            ?>
            <div class="cm-tracks-grid">
                <?php while (have_posts()) : the_post(); ?>
                    <?php 
                    $pid = get_the_ID();
                    get_template_part('template-parts/card-midi', null, array(
                        'product_id' => $pid,
                        'midi'       => isset($cm_result_map[$pid]) ? $cm_result_map[$pid] : null,
                    )); 
                    ?>
                <?php endwhile; ?>
            </div>

            <!-- Paginação Moderna -->
            <?php if ($wp_query->max_num_pages > 1) : ?>
                <div class="cm-pagination-wrapper">
                    <?php
                    $big = 999999999;
                    echo paginate_links(array(
                        'base'      => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
                        'format'    => '?paged=%#%',
                        'current'   => $paged,
                        'total'     => $wp_query->max_num_pages,
                        'prev_text' => '<i class="ri-arrow-left-s-line"></i>',
                        'next_text' => '<i class="ri-arrow-right-s-line"></i>',
                    ));
                    ?>
                </div>
            <?php endif; ?>

        <?php else : ?>
            <div class="cm-search-empty">
                <i class="ri-music-2-line" style="font-size: 3rem; color: #64748b; display: block; margin-bottom: 15px;"></i>
                <p>Nenhum MIDI encontrado para este artista.</p>
                <a href="<?php echo esc_url(home_url('/artistas/')); ?>" class="cm-btn cm-btn-primary" style="margin-top: 15px; display: inline-flex;">
                    Voltar para lista de artistas
                </a>
            </div>
        <?php endif; ?>

        <div class="back-to-list" style="margin-top: 35px; margin-bottom: 25px; text-align: center;">
            <a href="<?php echo esc_url(home_url('/artistas/')); ?>" class="cm-btn-catalog-link" style="display: inline-flex; align-items: center; gap: 8px;">
                <i class="ri-arrow-left-line"></i> Voltar para a lista de artistas
            </a>
        </div>
    </div>
</div>

<?php get_footer(); ?>