<?php
/**
 * Archive Product Template - Customizada para Pesquisa e Loja
 *
 * @version 8.6.0
 */

if ( is_search() ) {
    include get_stylesheet_directory() . '/search.php';
    return;
}

// Loja ("/midis/") — catálogo completo de MIDIs no layout nativo do site.
if ( is_shop() ) {
    include get_stylesheet_directory() . '/catalogo-midis.php';
    return;
}

get_header( 'shop' );

wc_get_template_part( 'layouts/category', flatsome_option( 'category_sidebar' ) );

get_footer( 'shop' );
