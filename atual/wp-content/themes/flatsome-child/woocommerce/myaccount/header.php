<?php
/**
 * Header da Minha Conta - Central MIDI
 * Substitui o template legado do Flatsome com suporte nativo a Tema Escuro e Claro.
 *
 * @package CentralMidi
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$current_user = wp_get_current_user();
$is_logged_in = is_user_logged_in();

global $wp;
$endpoint_label = '';
$current_url    = home_url( $wp->request );

if ( function_exists( 'wc_get_account_menu_items' ) ) {
	foreach ( wc_get_account_menu_items() as $endpoint => $label ) {
		if ( untrailingslashit( wc_get_account_endpoint_url( $endpoint ) ) === $current_url ) {
			$endpoint_label = $label;
			break;
		}
	}
}
?>

<div class="cm-account-header">
	<div class="cm-account-header-container">
		<div class="cm-account-header-content">
			<div class="cm-account-header-badge">
				<i class="ri-shield-user-line"></i>
				<span>Área do Cliente</span>
			</div>
			<h1 class="cm-account-header-title">
				<?php if ( $is_logged_in ) : ?>
					Olá, <?php echo esc_html( $current_user->display_name ? $current_user->display_name : $current_user->user_login ); ?>
				<?php else : ?>
					Minha Conta
				<?php endif; ?>
			</h1>
			<p class="cm-account-header-subtitle">
				<?php if ( $is_logged_in ) : ?>
					<?php if ( ! empty( $endpoint_label ) && $endpoint_label !== 'Painel' ) : ?>
						Navegando em: <strong><?php echo esc_html( $endpoint_label ); ?></strong>
					<?php else : ?>
						Gerencie seus pedidos, dados cadastrais e compras com segurança e rapidez.
					<?php endif; ?>
				<?php else : ?>
					Acesse sua conta ou cadastre-se para acompanhar pedidos e baixar seus arquivos MIDI.
				<?php endif; ?>
			</p>
		</div>

		<?php if ( $is_logged_in ) : ?>
			<div class="cm-account-header-actions">
				<a href="<?php echo esc_url( wc_logout_url() ); ?>" class="cm-account-logout-btn" title="Sair da Conta">
					<i class="ri-logout-box-r-line"></i>
					<span>Sair</span>
				</a>
			</div>
		<?php endif; ?>
	</div>
</div>
