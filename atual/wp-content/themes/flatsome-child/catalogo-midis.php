<?php
/**
 * Catálogo completo de MIDIs — "/midis/" (página da loja WooCommerce).
 *
 * A loja (página "Midis", id 9) é o catálogo com TODOS os MIDIs do acervo.
 * O catálogo A-Z de_artistas vive em "/artistas/" (plugin Central MIDI).
 *
 * Usa o mesmo componente nativo do resto do site (template-parts/card-midi.php)
 * e a mesma moldura das páginas de taxonomia (hero + toolbar + grade +
 * paginação), então o visual é idêntico ao da busca e ao do catálogo de
 * artista/gênero/mês.
 *
 * @package CentralMidi_Child
 */

defined( 'ABSPATH' ) || exit;

get_header();

global $wp_query;

$cm_total     = (int) $wp_query->found_posts;
$cm_paged     = max( 1, (int) get_query_var( 'paged' ) );
$cm_max_pages = (int) $wp_query->max_num_pages;
$cm_ordem     = isset( $_GET['ordem'] ) ? sanitize_key( wp_unslash( $_GET['ordem'] ) ) : 'nome'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
if ( ! in_array( $cm_ordem, array( 'nome', 'nome-desc', 'data' ), true ) ) {
	$cm_ordem = 'nome';
}
?>

<div class="catalogo-midis-page cm-archive-page">
	<div class="cm-search-hero cm-archive-hero">
		<div class="shop-container">
			<nav class="cm-search-breadcrumb" aria-label="Navegação">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><i class="ri-home-4-line"></i> Início</a>
				<span class="cm-breadcrumb-sep"><i class="ri-arrow-right-s-line"></i></span>
				<span class="cm-breadcrumb-current">Catálogo de MIDIs</span>
			</nav>

			<span class="cm-search-badge"><i class="ri-music-2-line"></i> Catálogo Completo</span>
			<h1 class="cm-search-title">Todos os <strong>MIDIs</strong></h1>
			<p class="cm-search-subtitle">
				<?php if ( $cm_total > 0 ) : ?>
					<strong><?php echo esc_html( number_format_i18n( $cm_total ) ); ?></strong> arquivos MIDI no acervo Central MIDI — ordene ou navegue por artista, gênero e mês de lançamento.
				<?php else : ?>
					Nenhum MIDI publicado no acervo.
				<?php endif; ?>
			</p>
		</div>
	</div>

	<div class="shop-container">
		<div class="cm-search-toolbar">
			<div class="cm-toolbar-info">
				<i class="ri-music-2-line"></i>
				<span>
					Exibindo <strong><?php echo esc_html( number_format_i18n( (int) $wp_query->post_count ) ); ?></strong>
					de <strong><?php echo esc_html( number_format_i18n( $cm_total ) ); ?></strong> músicas
				</span>
			</div>
			<div class="cm-toolbar-actions">
				<form class="cm-catalog-sort" method="get" action="<?php echo esc_url( home_url( '/midis/' ) ); ?>">
					<label for="cm-ordem-select">Ordenar por</label>
					<select id="cm-ordem-select" name="ordem" onchange="this.form.submit();">
						<option value="nome" <?php selected( $cm_ordem, 'nome' ); ?>>Nome (A–Z)</option>
						<option value="nome-desc" <?php selected( $cm_ordem, 'nome-desc' ); ?>>Nome (Z–A)</option>
						<option value="data" <?php selected( $cm_ordem, 'data' ); ?>>Mais recentes</option>
					</select>
					<noscript><button type="submit" class="cm-btn-catalog-link">Aplicar</button></noscript>
				</form>
				<a href="<?php echo esc_url( home_url( '/artistas/' ) ); ?>" class="cm-btn-catalog-link">
					<i class="ri-user-star-line"></i> Por artista
				</a>
			</div>
		</div>

		<?php if ( have_posts() ) : ?>
			<?php
			// Uma única leitura em lote para todos os cards da página.
			$cm_result_ids = array();
			foreach ( $wp_query->posts as $cm_post ) {
				$cm_result_ids[] = (int) $cm_post->ID;
			}
			$cm_result_map = ( $cm_result_ids && class_exists( 'CentralMidi_DB' ) )
				? CentralMidi_DB::get_midis_by_products( $cm_result_ids )
				: array();
			if ( function_exists( 'cmidi_prime_cards' ) ) {
				cmidi_prime_cards( $cm_result_ids );
			}
			?>
			<div class="cm-tracks-grid">
				<?php
				while ( have_posts() ) :
					the_post();
					$cm_pid = get_the_ID();
					get_template_part(
						'template-parts/card-midi',
						null,
						array(
							'product_id' => $cm_pid,
							'midi'       => isset( $cm_result_map[ $cm_pid ] ) ? $cm_result_map[ $cm_pid ] : null,
						)
					);
				endwhile;
				?>
			</div>

			<?php if ( $cm_max_pages > 1 ) : ?>
				<div class="cm-pagination-wrapper">
					<?php
					$big = 999999999;
					echo paginate_links(
						array(
							'base'      => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
							'format'    => '',
							'current'   => $cm_paged,
							'total'     => $cm_max_pages,
							'mid_size'  => 2,
							'end_size'  => 1,
							'prev_text' => '<i class="ri-arrow-left-s-line"></i>',
							'next_text' => '<i class="ri-arrow-right-s-line"></i>',
							'type'      => 'plain',
						)
					);
					?>
				</div>
			<?php endif; ?>
		<?php else : ?>
			<div class="cm-search-empty">
				<i class="ri-music-2-line" style="font-size: 3rem; color: #64748b; display: block; margin-bottom: 15px;"></i>
				<p>Nenhum MIDI encontrado no catálogo.</p>
				<a href="<?php echo esc_url( home_url( '/artistas/' ) ); ?>" class="cm-btn cm-btn-primary" style="margin-top: 15px; display: inline-flex;">
					Ver catálogo de artistas
				</a>
			</div>
		<?php endif; ?>
	</div>
</div>

<?php
get_footer();
