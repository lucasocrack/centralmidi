<?php
/**
 * Formulário de Login / Cadastro - Central MIDI
 * Substitui o template legado com visual moderno, seguro e com suporte total aos modos Escuro e Claro.
 *
 * @package CentralMidi
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'woocommerce_before_customer_login_form' );
$enable_registration = ( 'yes' === get_option( 'woocommerce_enable_myaccount_registration' ) );
?>

<div class="cm-auth-wrapper <?php echo $enable_registration ? 'cm-has-registration' : ''; ?>">
	<div class="cm-auth-grid">

		<!-- Painel de Login -->
		<div class="cm-auth-card cm-login-card">
			<div class="cm-auth-card-header">
				<div class="cm-auth-badge">
					<i class="ri-login-box-line"></i>
					<span>Já sou cliente</span>
				</div>
				<h2 class="cm-auth-title">Acessar Conta</h2>
				<p class="cm-auth-desc">Entre com seu e-mail ou nome de usuário para acessar seus pedidos.</p>
			</div>

			<form class="woocommerce-form woocommerce-form-login login cm-auth-form" method="post" novalidate>
				<?php do_action( 'woocommerce_login_form_start' ); ?>

				<div class="cm-form-field">
					<label for="username"><?php esc_html_e( 'Usuário ou e-mail', 'central-midi' ); ?> <span class="required">*</span></label>
					<div class="cm-input-icon-wrap">
						<i class="ri-mail-line cm-field-icon"></i>
						<input type="text" class="woocommerce-Input woocommerce-Input--text input-text cm-input" name="username" id="username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) && is_string( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" required placeholder="seuemail@exemplo.com" />
					</div>
				</div>

				<div class="cm-form-field">
					<div class="cm-field-header-row">
						<label for="password"><?php esc_html_e( 'Senha', 'central-midi' ); ?> <span class="required">*</span></label>
						<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>" class="cm-forgot-password-link">Esqueceu a senha?</a>
					</div>
					<div class="cm-input-icon-wrap">
						<i class="ri-lock-2-line cm-field-icon"></i>
						<input class="woocommerce-Input woocommerce-Input--text input-text cm-input" type="password" name="password" id="password" autocomplete="current-password" required placeholder="Sua senha secreta" />
					</div>
				</div>

				<?php do_action( 'woocommerce_login_form' ); ?>

				<div class="cm-auth-form-options">
					<label class="cm-checkbox-label">
						<input class="woocommerce-form__input woocommerce-form__input-checkbox" name="rememberme" type="checkbox" id="rememberme" value="forever" />
						<span>Lembrar de mim</span>
					</label>
				</div>

				<div class="cm-auth-form-actions">
					<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
					<button type="submit" class="cm-btn cm-btn-primary-auth woocommerce-form-login__submit" name="login" value="Acessar">
						<span>Acessar Conta</span>
						<i class="ri-arrow-right-line"></i>
					</button>
				</div>

				<?php do_action( 'woocommerce_login_form_end' ); ?>
			</form>
		</div>

		<?php if ( $enable_registration ) : ?>
		<!-- Painel de Cadastro -->
		<div class="cm-auth-card cm-register-card">
			<div class="cm-auth-card-header">
				<div class="cm-auth-badge cm-badge-register">
					<i class="ri-user-add-line"></i>
					<span>Novo por aqui?</span>
				</div>
				<h2 class="cm-auth-title">Criar Conta</h2>
				<p class="cm-auth-desc">Cadastre-se para baixar seus MIDIs instantaneamente a qualquer momento.</p>
			</div>

			<form method="post" class="woocommerce-form woocommerce-form-register register cm-auth-form" <?php do_action( 'woocommerce_register_form_tag' ); ?>>
				<?php do_action( 'woocommerce_register_form_start' ); ?>

				<?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?>
					<div class="cm-form-field">
						<label for="reg_username"><?php esc_html_e( 'Nome de usuário', 'central-midi' ); ?> <span class="required">*</span></label>
						<div class="cm-input-icon-wrap">
							<i class="ri-user-line cm-field-icon"></i>
							<input type="text" class="woocommerce-Input woocommerce-Input--text input-text cm-input" name="username" id="reg_username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" required placeholder="Escolha seu usuário" />
						</div>
					</div>
				<?php endif; ?>

				<div class="cm-form-field">
					<label for="reg_email"><?php esc_html_e( 'Endereço de e-mail', 'central-midi' ); ?> <span class="required">*</span></label>
					<div class="cm-input-icon-wrap">
						<i class="ri-mail-line cm-field-icon"></i>
						<input type="email" class="woocommerce-Input woocommerce-Input--text input-text cm-input" name="email" id="reg_email" autocomplete="email" value="<?php echo ( ! empty( $_POST['email'] ) ) ? esc_attr( wp_unslash( $_POST['email'] ) ) : ''; ?>" required placeholder="seuemail@exemplo.com" />
					</div>
				</div>

				<?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?>
					<div class="cm-form-field">
						<label for="reg_password"><?php esc_html_e( 'Senha', 'central-midi' ); ?> <span class="required">*</span></label>
						<div class="cm-input-icon-wrap">
							<i class="ri-lock-2-line cm-field-icon"></i>
							<input type="password" class="woocommerce-Input woocommerce-Input--text input-text cm-input" name="password" id="reg_password" autocomplete="new-password" required placeholder="Crie uma senha forte" />
						</div>
					</div>
				<?php else : ?>
					<p class="cm-register-notice">
						<i class="ri-information-line"></i>
						<span>Um link de confirmação para definir a sua senha será enviado para o seu e-mail.</span>
					</p>
				<?php endif; ?>

				<?php do_action( 'woocommerce_register_form' ); ?>

				<div class="cm-auth-form-actions">
					<?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
					<button type="submit" class="cm-btn cm-btn-secondary-auth woocommerce-form-register__submit" name="register" value="Cadastre-se">
						<span>Cadastre-se Gratuitamente</span>
						<i class="ri-user-add-line"></i>
					</button>
				</div>

				<?php do_action( 'woocommerce_register_form_end' ); ?>
			</form>
		</div>
		<?php endif; ?>

	</div>
</div>

<?php do_action( 'woocommerce_after_customer_login_form' ); ?>
