<?php
/**
 * Página Principal da Minha Conta - Central MIDI
 * Substitui o template legado com um layout de 2 colunas moderno (Sidebar de Navegação + Conteúdo).
 *
 * @package CentralMidi
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="cm-my-account-page-wrapper">
	<div class="cm-my-account-layout">
		
		<!-- Coluna da Esquerda: Navegação -->
		<aside class="cm-my-account-sidebar">
			<?php
			/**
			 * My Account navigation.
			 *
			 * @since 2.6.0
			 */
			do_action( 'woocommerce_account_navigation' );
			?>
		</aside>

		<!-- Coluna da Direita: Conteúdo Ativo -->
		<main class="cm-my-account-main" id="cm-account-content">
			<div class="cm-my-account-card">
				<?php
				/**
				 * My Account content.
				 *
				 * @since 2.6.0
				 */
				do_action( 'woocommerce_account_content' );
				?>
			</div>
		</main>

	</div>
</div>
