<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class AffiliateWP_PayPal_Payouts_Payouts_Admin
 *
 * @since 1.2
 */
class AffiliateWP_PayPal_Payouts_Payouts_Admin {

	/**
	 * Sets up the payouts admin.
	 *
	 * @since 1.2
	 *
	 * @return void
	 */
	public function __construct() {

		if ( ! affiliate_wp_paypal()->has_2_4() ) {
			return;
		}

		$mode = affiliate_wp()->settings->get( 'paypal_payout_mode', 'masspay' );

		switch ( $mode ) {

			case 'masspay':
				$this->api = new \AffiliateWP_PayPal_MassPay();

				break;

			case 'api':
			default:
				$this->api = new \AffiliateWP_PayPal_API();

				break;

		}

		$this->api->credentials = affiliate_wp_paypal()->get_api_credentials();

		add_filter( 'affwp_payout_methods', array( $this, 'add_payout_method' ) );
		add_filter( 'affwp_is_payout_method_enabled', array( $this, 'is_paypal_enabled' ), 10, 2 );

		add_action( 'affwp_preview_payout_note_paypal', array( $this, 'preview_payout_note' ) );
		add_filter( 'affwp_preview_payout_invalid_affiliates_paypal', array( $this, 'preview_payout_invalid_affiliates' ), 10, 2 );
		add_action( 'affwp_process_payout_paypal', array( $this, 'process_bulk_paypal_payout' ), 10, 5 );
	}

	/**
	 * Add PayPal as a payout method to AffiliateWP.
	 *
	 * @since 1.2
	 *
	 * @param array $payout_methods Payout methods.
	 * @return array Filtered payout methods.
	 */
	public function add_payout_method( $payout_methods ) {

		if ( ! affiliate_wp_paypal()->has_api_credentials() ) {
			/* translators: 1: PayPal settings link */
			$payout_methods['paypal'] = sprintf( __( 'PayPal - <a href="%s">Provide</a> your PayPal credentials to enable this payout method', 'affwp-paypal-payouts' ), affwp_admin_url( 'settings', array( 'tab' => 'paypal' ) ) );
		} else {
			$payout_methods['paypal'] = __( 'PayPal', 'affwp-paypal-payouts' );
		}

		return $payout_methods;

	}

	/**
	 * Check if 'PayPal' payout method is enabled.
	 *
	 * @since 1.2.1
	 *
	 * @param bool   $enabled       True if the payout method is enabled. False otherwise.
	 * @param string $payout_method Payout method.
	 * @return bool True if the payout method is enabled. False otherwise.
	 */
	public function is_paypal_enabled( $enabled, $payout_method ) {

		if ( 'paypal' === $payout_method && ! affiliate_wp_paypal()->has_api_credentials() ) {
			$enabled = false;
		}

		return $enabled;
	}

	/**
	 * Add a note to the Payout preview page for a PayPal payout.
	 *
	 * @since 1.2
	 *
	 * @return void
	 */
	public function preview_payout_note() {
		?>
		<h2><?php esc_html_e( 'Note', 'affwp-paypal-payouts' ); ?></h2>
		<p><?php esc_html_e( 'If you receive a "denied" receipt from PayPal after processing a payout, the affiliate&#8217;s account may be suspended, cannot receive your site&#8217;s currency, or cannot receive payments from your country.', 'affwp-paypal-payouts' ); ?></p>
		<p><?php esc_html_e( 'If the affiliate does not have a PayPal account they will receive a PayPal invitation to create an account. If the affiliate does not accept the invitation, the funds will be returned to your PayPal account.', 'affwp-paypal-payouts' ); ?></p>
		<p><?php esc_html_e( 'You must have a sufficient balance already present in your PayPal account to cover the payouts being processed.', 'affwp-paypal-payouts' ); ?></p>
		<?php
	}

	/**
	 * Filter out the list of invalid affiliates on the payout preview page.
	 *
	 * @since 1.2
	 *
	 * @param array $invalid_affiliates Invalid affiliates.
	 * @param array $data               Payout data.
	 * @return array Modified array of invalid affiliates.
	 */
	public function preview_payout_invalid_affiliates( $invalid_affiliates, $data ) {

		foreach ( $data as $affiliate_id => $payout_data ) {
			$user_name = affwp_get_affiliate_username( $affiliate_id );

			if ( ! $user_name ) {
				$invalid_affiliates[] = $affiliate_id;
			}
		}

		return $invalid_affiliates;
	}

	/**
	 * Payout referrals in bulk for a specified timeframe via PayPal.
	 *
	 * @since 1.2
	 *
	 * @param string $start         Referrals start date.
	 * @param string $end           Referrals end date data.
	 * @param float  $minimum       Minimum payout.
	 * @param int    $affiliate_id  Affiliate ID.
	 * @param string $payout_method Payout method.
	 *
	 * @return void
	 */
	public function process_bulk_paypal_payout( $start, $end, $minimum, $affiliate_id, $payout_method ) {

		if ( ! current_user_can( 'manage_payouts' ) ) {
			wp_die( __( 'You do not have permission to process payouts', 'affwp-paypal-payouts' ) );
		}

		if ( ! affiliate_wp_paypal()->has_api_credentials() ) {
			wp_die( __( 'Please enter your API credentials in Affiliates &rarr; Settings &rarr; PayPal Payouts before attempting to process payments', 'affwp-paypal-payouts' ) );
		}

		$args = array(
			'status'       => 'unpaid',
			'number'       => -1,
			'affiliate_id' => $affiliate_id,
			'date'         => array(
				'start' => $start,
				'end'   => $end,
			),
		);

		// Final  affiliate / referral data to be paid out.
		$data = array();

		// The affiliates that have earnings to be paid.
		$affiliates = array();

		// Retrieve the referrals from the database.
		$referrals = affiliate_wp()->referrals->get_referrals( $args );

		if ( $referrals ) {

			foreach ( $referrals as $referral ) {

				$user_name = affwp_get_affiliate_username( $referral->affiliate_id );
				if ( ! $user_name ) {
					continue;
				}

				if ( in_array( $referral->affiliate_id, $affiliates ) ) {

					// Add the amount to an affiliate that already has a referral in the export.
					$amount = $data[ $referral->affiliate_id ]['amount'] + $referral->amount;

					$data[ $referral->affiliate_id ]['amount']      = $amount;
					$data[ $referral->affiliate_id ]['referrals'][] = $referral->referral_id;

				} else {

					$email = affwp_get_affiliate_payment_email( $referral->affiliate_id );

					$data[ $referral->affiliate_id ] = array(
						'email'     => $email,
						'amount'    => $referral->amount,
						'currency'  => ! empty( $referral->currency ) ? $referral->currency : affwp_get_currency(),
						'referrals' => array( $referral->referral_id ),
					);

					$affiliates[] = $referral->affiliate_id;

				}
			}

			$payouts = array();

			$i = 0;

			foreach ( $data as $affiliate_id => $payout ) {

				if ( $minimum > 0 && $payout['amount'] < $minimum ) {

					// Ensure the minimum amount was reached.
					unset( $data[ $affiliate_id ] );

					// Skip to the next affiliate.
					continue;
				}

				$payouts[ $affiliate_id ] = array(
					'email'       => $payout['email'],
					'amount'      => $payout['amount'],
					/* translators: 1: Referrals start date, 2: Referrals end date, 3: Home URL */
					'description' => sprintf( __( 'Payment for referrals between %1$s and %2$s from %3$s', 'affwp-paypal-payouts' ), $start, $end, home_url() ),
					'referrals'   => $payout['referrals'],
				);

				$i++;
			}

			$redirect_args = array(
				'affwp_notice' => 'paypal_bulk_pay_success',
			);

			$success = $this->api->send_bulk_payment( $payouts );

			if ( is_wp_error( $success ) ) {

				$redirect_args['affwp_notice'] = 'paypal_error';
				$redirect_args['message']      = $success->get_error_message();
				$redirect_args['code']         = $success->get_error_code();

			} else {

				// We now know which referrals should be marked as paid.
				foreach ( $payouts as $affiliate_id => $payout ) {
					if ( function_exists( 'affwp_add_payout' ) ) {
						affwp_add_payout( array(
							'affiliate_id'  => $affiliate_id,
							'referrals'     => $payout['referrals'],
							'amount'        => $payout['amount'],
							'payout_method' => 'paypal',
						) );
					} else {
						foreach ( $payout['referrals'] as $referral ) {
							affwp_set_referral_status( $referral, 'paid' );
						}
					}
				}

			}

			$redirect = affwp_admin_url( 'referrals', $redirect_args );

			// A header is used here instead of wp_redirect() due to the esc_url() bug that removes [] from URLs.
			header( 'Location:' . $redirect );
			exit;

		}

	}

}
new AffiliateWP_PayPal_Payouts_Payouts_Admin();
