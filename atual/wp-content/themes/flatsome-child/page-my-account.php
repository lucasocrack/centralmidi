<?php
/**
 * Template Name: WooCommerce - My Account (Central MIDI Desacoplado)
 *
 * Sobrescreve flatsome/page-my-account.php eliminando o wrapper legado do Flatsome
 * (.page-wrapper.my-account, .row.vertical-tabs, .large-3.col.col-border) que duplicava
 * a sidebar de navegação e causava margens indesejadas no topo.
 *
 * @package CentralMidi_Child
 */

get_header();

// Header visual moderno da Minha Conta
wc_get_template( 'myaccount/header.php' );
?>

<div id="content" role="main" class="content-area">
	<?php while ( have_posts() ) : the_post(); ?>
		<?php the_content(); ?>
	<?php endwhile; ?>
</div>

<?php get_footer(); ?>
