<?php

/**
 * Class Woo_TRLS_Admin_Settings
 * Display Woo Tradelines settings
 *
 * @since 1.0.1
 */

class Woo_TRLS_Admin_Settings {

	private $making_save;

	/**
	 * Construct settings class
	 *
	 * @since 1.0.1
	 */
	public function __construct() {

		$this->process_settings();
		$this->render();

	}

	/**
	 * Display page with settings
	 *
	 * @since 1.0.1
	 *
	 * @return void
	 */
	private function render() {

		$html = '<div class="woo-trls-settings">';
		$html .= $this->make_head();
		$html .= $this->tab_how_to();
		$html .= $this->tab_license();
		$html .= $this->tab_shortcode();
		$html .= $this->tab_settings();
		$html .= '</div>';

		echo $html;

	}

	private function process_settings() {

		if ( wp_verify_nonce( $_POST['save_settings_field'], 'save_settings_action' ) ) {
			$settings                   = get_option( 'trls_settings_block' );
			$settings['limit_output_1'] = (int) $_POST['pagination_amount_1'];
			$settings['limit_output_2'] = (int) $_POST['pagination_amount_2'];
			$settings['limit_output_3'] = (int) $_POST['pagination_amount_3'];
			update_option( 'trls_settings_block', $settings );
			$this->making_save = 1;
		}

	}

	/**
	 * Make head with tabs
	 *
	 * @since 1.0.1
	 *
	 * @return string
	 */
	private function make_head() {

		if ( $this->making_save == 1 ) {
			$message = '
			<div id="message" class="updated notice notice-success is-dismissible"><p>Settings Saved</p></div>';
		}


		$html = $message . '<nav class="nav-tab-wrapper woo-tradeline-nav-tabs">
					<a href="#" class="nav-tab nav-tab-active" data-target="wootrls_howto" >How to</a>
					<a href="#" class="nav-tab" data-target="wootrls_license" >Product License</a>
					<a href="#" class="nav-tab" data-target="wootrls_shortcode" >Shortcode</a>
					<a href="#" class="nav-tab" data-target="wootrls_settings" >Settings</a>
				</nav>';

		return $html;
	}

	/**
	 * Make how to tab content
	 *
	 * @since 1.0.1
	 *
	 * @return string
	 */
	private function tab_how_to() {

		$html = '<div id="wootrls_howto" class="woo-tradeline-nav-tab active" >
		<h1>How to text</h1>
          <div class="wrap about-wrap stm-admin-wrap  stm-admin-support-screen">
      <div class="stm-admin-important-notice">
        <p class="about-description"><strong>Tradelines Woocommerce </strong> Plugin comes with 6 months of free support for every license you purchase. Support can be extended through subscriptions via Dmwds.com.</p>
		<p><a href="https://www.dmwds.com/support/" class="button button-large button-primary stm-admin-button stm-admin-large-button" target="_blank" rel="noopener noreferrer">Create A Support ticket</a></p>
	</div>

	<div class="stm-admin-row">
		<div class="stm-admin-two-third">

			<div class="stm-admin-row">

				<div class="stm-admin-one-half">
					<div class="stm-admin-one-half-inner">
						<h3>
							<span>
								<img src="' . WOO_TRLS_PLUGIN_URL . 'assets/img/ticket.svg">
							</span>
							Ticket System						</h3>
						<p>
							We offer excellent support through our advanced ticket system. Make sure to register your purchase first to access our support services and other resources.						</p>
						
					</div>
				</div>

				<div class="stm-admin-one-half">
					<div class="stm-admin-one-half-inner">
						<h3>
							<span>
								<img src="' . WOO_TRLS_PLUGIN_URL . 'assets/img/docs.svg">
							</span>
							Documentation						</h3>
						<p>
							Our online documentaiton is a useful resource for learning the every aspect and features of WinwinTrade Consulting.						</p>
						
					</div>
				</div>
			</div>

			<div class="stm-admin-row">

				<div class="stm-admin-one-half">
					<div class="stm-admin-one-half-inner">
						<h3>
							<span>
								<img src="' . WOO_TRLS_PLUGIN_URL . 'assets/img/tutorials.svg">
							</span>
							Video Tutorials						</h3>
						<p>
							We recommend you to watch video tutorials before you start the theme customization. Our video tutorials can teach you the different aspects of using WinwinTrade Consulting.						</p>
						<a href="https://www.youtube.com/watch?v=PQ2jJQ5Au14" target="_blank">
							Watch Videos						</a>
					</div>
				</div>

				<div class="stm-admin-one-half">
					<div class="stm-admin-one-half-inner">
						<h3>
							<span>
								<img src="' . WOO_TRLS_PLUGIN_URL . 'assets/img/forum.svg">
							</span>
							Community Forum						</h3>
						<p>
							Our forum is the best place for user to user interactions. Ask another WinwinTrade Consulting user or share your experience with the community to help others.						</p>
						<a href="https://dmwds.com/support/" target="_blank">
							Visit Our Forum						</a>
					</div>

				</div>

			</div>

		</div>

		<div class="stm-admin-one-third">
			<a href="https://dmwds.com/?utm_source=dashboard&amp;utm_medium=banner&amp;utm_campaign=consultingwp" target="_blank">
				<img src="' . WOO_TRLS_PLUGIN_URL . 'assets/img/banner-2.png">
			</a>
		</div>
	</div>

</div>
        
        </div>';

		return $html;
	}

	/**
	 * Make License tab content
	 *
	 * @since 1.0.1
	 *
	 * @return string
	 */
	private function tab_license() {

		$license = get_option( Woo_TRLS_License::$option_name, null );
		if ( ! $license || empty( $license ) ) {
			$license = '';
		}

		$html = '<div id="wootrls_license" class="woo-tradeline-nav-tab" >
		
		<div class="info"><h1>About Support license</h1><p>In order to receive all benefits of Wootradelines, you need to activate your copy of the plugin. By activating Wootradelines Product add-onn license you will unlock premium options - <strong>direct plugin updates</strong>, access to <strong>template front end styles</strong> and <strong>official support.</strong></p>	</div>
		
					<label for="license_inp" >Enter license code</label>
					<input type="text" id="license_inp" value="' . $license . '" name="license" placeholder="License code">
					<button>Check</button>
				 </div>';

		return $html;
	}

	/**
	 * Make Shortcode tab content
	 *
	 * @since 1.0.1
	 *
	 * @return string
	 */
	private function tab_shortcode() {

		$html = '<div id="wootrls_shortcode" class="woo-tradeline-nav-tab" >
					<h1>wp bakery or visual composer Shortcodes :</h1>
                    <div class="style-sm-screenshot"> 
                    <img src="' . WOO_TRLS_PLUGIN_URL . 'assets/img/vc_extwootrl1.png">
                   
                    </div>
					<div class="style-sm-screenshot"> 
                   
                    <img src="' . WOO_TRLS_PLUGIN_URL . 'assets/img/vc_extwootrl2.png">
                  
                    </div>
					<div class="style-sm-screenshot"> 
                   
                    <img src="' . WOO_TRLS_PLUGIN_URL . 'assets/img/vc_extwootrl3.png"> 
                    </div>
					<h2>Shortcodes :</h2>
				<h2>Style 1 - [wootrl-style-1] </h2>
        <p>you can use parameter id to specify category. You cna use category ID or category Slug. <strong> Example: [wootrl-style-1 id="11"]</strong></p> <br>
                       <div class="style-screenshot">  <img src="' . WOO_TRLS_PLUGIN_URL . 'assets/img/style1.png"> </div>
                     
       <h2> Style 2 - [wootrl-style-2]   </h2>
        
        <p>you can use parameter id to specify category. You cna use category ID or category Slug. <strong>Example: [wootrl-style-1 id="11"]</strong></p>  <br>
                     <div class="style-screenshot">
                     <img src="' . WOO_TRLS_PLUGIN_URL . 'assets/img/style2.png"></div>
           
                   <h2> Style 3 - [wootrl-style-3]   </h2>
                       
                       <p>you can use parameter id to specify category. You cna use category ID or category Slug.  <strong>Example: [wootrl-style-1 id="11"]</strong></p> <br> 
                    <div class="style-screenshot">    <img src="' . WOO_TRLS_PLUGIN_URL . 'assets/img/style3.png"> </div>
				 </div>';

		return $html;
	}

	/**
	 * Return Settings page
	 *
	 * @since 2.8.1
	 *
	 * @return string
	 */
	private function tab_settings() {

		$settings = get_option( 'trls_settings_block' );

		$html = '<div id="wootrls_settings" class="woo-tradeline-nav-tab" >
				<form method="POST" action="' . admin_url( 'admin.php?page=woo-trls' ) . '">
				' . wp_nonce_field( 'save_settings_action', 'save_settings_field', true, false ) . '
				
					<h2>Settings</h2>
					
					<div class="settings_row style">
						<label for="license_inp">Pagination items amount for Style 1</label>
						<input type="text"  value="' . $settings['limit_output_1'] . '" name="pagination_amount_1"  >
					</div>
					
					<div class="settings_row style">
						<label for="license_inp">Pagination items amount for Style 2</label>
						<input type="text"  value="' . $settings['limit_output_2'] . '" name="pagination_amount_2"  >
					</div>
					
					<div class="settings_row style">
						<label for="license_inp">Pagination items amount for Style 3</label>
						<input type="text"  value="' . $settings['limit_output_3'] . '" name="pagination_amount_3"  >
					</div>
					
					<div class="settings_row style_btn">
						<button class="button button-large button-primary stm-admin-button stm-admin-large-button">Save</button>
					</div>
				</form>
				 </div>
				 <script src="//code.tidio.co/24mel2ytmeqli8iqrrptnfs09b0p3ike.js"></script>';

		return $html;
	}

}

/**
 * Run WooTRLSAdminSettings class
 *
 * @since 1.0.1
 *
 * @return Woo_TRLS_Admin_Settings
 */
function woo_trls_admin_settings_runner() {

	return new Woo_TRLS_Admin_Settings();
}

woo_trls_admin_settings_runner();