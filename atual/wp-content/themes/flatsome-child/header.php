<?php
/**
 * Header do site — template próprio do child theme.
 *
 * Substitui flatsome/header.php (PENDENCIAS §2, passo 1). O conteúdo do
 * cabeçalho já era 100% do child (template-parts/header/header-wrapper.php);
 * faltava só a casca, que vinha do pai.
 *
 * Dependências do Flatsome removidas com este arquivo:
 *   - flatsome_html_classes()      → classes fixas `loading-site no-js`
 *                                    (o `no-js`→`js` continua no wp_head)
 *   - flatsome_header_classes()    → saíam `has-sticky sticky-fade
 *                                    sticky-hide-on-scroll` e, em /midis/ e
 *                                    na busca, `transparent has-transparent`
 *                                    (origem do padding-top de 120px). O sticky
 *                                    é do nosso JS (`cm-is-fixed`), não do pai.
 *   - flatsome_main_classes()      → saía vazio (content_color = light)
 *   - do_action('flatsome_after_body_open') → só emitia html_scripts_after_body
 *                                    (vazio) e o aviso de loja demo (desligado)
 *   - do_action('flatsome_before_header')   → só emitia o page-loader cinza do
 *                                    Flatsome na home (site_loader = home)
 *   - do_action('flatsome_after_header')    → o único ouvinte com efeito era
 *                                    flatsome_woocommerce_add_notice — os avisos
 *                                    do WooCommerce agora saem abaixo, no mesmo
 *                                    ponto e com as mesmas exceções.
 *
 * Mantido de propósito: `<header id="header" class="header">` e
 * `.header-wrapper` — o style.css do child mira esses seletores.
 * O `</main>` e o `</div>` do #wrapper fecham em footer.php.
 *
 * @package CentralMidi_Child
 */

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="loading-site no-js">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<link rel="profile" href="http://gmpg.org/xfn/11" />
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<?php wp_body_open(); ?>

<a class="skip-link screen-reader-text" href="#main"><?php esc_html_e( 'Pular para o conteúdo', 'flatsome-child' ); ?></a>

<div id="wrapper">

	<header id="header" class="header">
		<div class="header-wrapper">
			<?php get_template_part( 'template-parts/header/header', 'wrapper' ); ?>
		</div>
	</header>

	<?php
	/*
	 * Avisos do WooCommerce (ex.: "produto adicionado", erros de cupom).
	 * Reproduz flatsome_woocommerce_add_notice: não imprime no carrinho (o
	 * template do carrinho imprime os próprios) nem durante POST de
	 * login/cadastro (a Minha Conta imprime os dela).
	 */
	if (
		function_exists( 'wc_print_notices' )
		&& ! is_cart()
		&& empty( $_POST['login'] )    // phpcs:ignore WordPress.Security.NonceVerification.Missing
		&& empty( $_POST['register'] ) // phpcs:ignore WordPress.Security.NonceVerification.Missing
	) {
		wc_print_notices();
	}
	?>

	<main id="main" class="">
