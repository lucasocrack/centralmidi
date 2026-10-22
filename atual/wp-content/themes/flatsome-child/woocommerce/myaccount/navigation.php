<?php
/**
 * Navegação de Minha Conta - Central MIDI
 * Substitui o template legado com visual moderno em tabs/pills com ícones Remix.
 *
 * @package CentralMidi
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'woocommerce_before_account_navigation' );

$menu_icons = array(
	'dashboard'        => 'ri-dashboard-3-line',
	'ferramentas-admin' => 'ri-settings-4-line',
	'orders'           => 'ri-shopping-bag-3-line',
	'downloads'        => 'ri-download-2-line',
	'edit-address'     => 'ri-map-pin-user-line',
	'payment-methods'  => 'ri-bank-card-line',
	'edit-account'     => 'ri-user-settings-line',
	'customer-logout'  => 'ri-logout-box-r-line',
);
?>

<nav class="cm-account-navigation" aria-label="<?php esc_html_e( 'Navegação da Conta', 'central-midi' ); ?>">
	<div class="cm-account-nav-card">
		<ul class="cm-account-nav-list">
			<?php foreach ( wc_get_account_menu_items() as $endpoint => $label ) : 
				$icon = isset( $menu_icons[ $endpoint ] ) ? $menu_icons[ $endpoint ] : 'ri-arrow-right-s-line';
				$is_current = wc_is_current_account_menu_item( $endpoint );
			?>
				<li class="cm-account-nav-item <?php echo wc_get_account_menu_item_classes( $endpoint ); ?> <?php echo $is_current ? 'is-active' : ''; ?>">
					<a href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>" 
					   class="cm-account-nav-link"
					   <?php echo $is_current ? 'aria-current="page"' : ''; ?>>
						<i class="<?php echo esc_attr( $icon ); ?> cm-account-nav-icon"></i>
						<span class="cm-account-nav-text"><?php echo esc_html( $label ); ?></span>
						<i class="ri-arrow-right-s-line cm-account-nav-arrow"></i>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</nav>

<?php do_action( 'woocommerce_after_account_navigation' ); ?>
