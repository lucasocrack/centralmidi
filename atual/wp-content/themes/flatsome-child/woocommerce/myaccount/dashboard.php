<?php
/**
 * Dashboard do Usuário - Central MIDI
 *
 * @package CentralMidi
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$current_user = wp_get_current_user();
$orders_url   = wc_get_endpoint_url( 'orders' );
$address_url  = wc_get_endpoint_url( 'edit-address' );
$account_url  = wc_get_endpoint_url( 'edit-account' );
$catalog_url  = home_url( '/midis/' );
?>

<div class="cm-dashboard-welcome">
	<div class="cm-dashboard-user-card">
		<div class="cm-user-avatar-wrap">
			<?php echo get_avatar( $current_user->ID, 64, '', $current_user->display_name, array( 'class' => 'cm-user-avatar' ) ); ?>
		</div>
		<div class="cm-user-info-wrap">
			<h3 class="cm-user-welcome-title">Bem-vindo(a), <?php echo esc_html( $current_user->display_name ); ?>!</h3>
			<p class="cm-user-email"><?php echo esc_html( $current_user->user_email ); ?></p>
		</div>
	</div>

	<!-- Ações de Dashboard disparadas via hook (ex: cmidi_admin_dashboard_card para admin) -->
	<?php do_action( 'woocommerce_account_dashboard' ); ?>

	<!-- Grid de Atalhos Rápidos da Conta -->
	<div class="cm-dashboard-grid">
		<a href="<?php echo esc_url( $orders_url ); ?>" class="cm-dashboard-tile">
			<div class="cm-dashboard-tile-icon cm-icon-orders">
				<i class="ri-shopping-bag-3-line"></i>
			</div>
			<div class="cm-dashboard-tile-body">
				<h4>Meus Pedidos</h4>
				<p>Acompanhe o status e histórico de compras</p>
			</div>
			<span class="cm-tile-link-icon"><i class="ri-arrow-right-line"></i></span>
		</a>

		<a href="<?php echo esc_url( $catalog_url ); ?>" class="cm-dashboard-tile">
			<div class="cm-dashboard-tile-icon cm-icon-catalog">
				<i class="ri-music-2-line"></i>
			</div>
			<div class="cm-dashboard-tile-body">
				<h4>Explorar Catálogo</h4>
				<p>Mais de 80.000 arquivos MIDI profissionais</p>
			</div>
			<span class="cm-tile-link-icon"><i class="ri-arrow-right-line"></i></span>
		</a>

		<a href="<?php echo esc_url( $address_url ); ?>" class="cm-dashboard-tile">
			<div class="cm-dashboard-tile-icon cm-icon-address">
				<i class="ri-map-pin-user-line"></i>
			</div>
			<div class="cm-dashboard-tile-body">
				<h4>Endereços</h4>
				<p>Gerencie seus dados de faturamento</p>
			</div>
			<span class="cm-tile-link-icon"><i class="ri-arrow-right-line"></i></span>
		</a>

		<a href="<?php echo esc_url( $account_url ); ?>" class="cm-dashboard-tile">
			<div class="cm-dashboard-tile-icon cm-icon-account">
				<i class="ri-user-settings-line"></i>
			</div>
			<div class="cm-dashboard-tile-body">
				<h4>Dados da Conta</h4>
				<p>Atualize nome, e-mail e altere sua senha</p>
			</div>
			<span class="cm-tile-link-icon"><i class="ri-arrow-right-line"></i></span>
		</a>
	</div>
</div>

<?php
/**
 * Hooks legados e de compatibilidade
 */
do_action( 'woocommerce_before_my_account' );
do_action( 'woocommerce_after_my_account' );
