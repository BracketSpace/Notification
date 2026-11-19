<?php
/**
 * Class TestEmailBodyField
 *
 * @package notification
 */

namespace BracketSpace\Notification\Tests\Carriers;

use BracketSpace\Notification\Repository\Carrier\Email;
use BracketSpace\Notification\Repository\CarrierRepository;
use BracketSpace\Notification\Repository\Field;
use BracketSpace\Notification\Store\Carrier as CarrierStore;

/**
 * Email Carrier body field test case.
 */
class TestEmailBodyField extends \WP_UnitTestCase {

	/**
	 * Settings are not available when the Carrier is created too early.
	 *
	 * @return Email
	 */
	private function createBeforeSettings() {
		add_filter( 'notification/settings/saved_settings', '__return_empty_array' );
		$email = new Email();
		remove_filter( 'notification/settings/saved_settings', '__return_empty_array' );

		return $email;
	}

	public function test_early_created_carrier_has_fallback_code_editor() {
		$email = $this->createBeforeSettings();

		$this->assertInstanceOf( Field\CodeEditorField::class, $email->getFormField( 'body' ) );
	}

	public function test_refresh_picks_up_settings_html_type() {
		$email = $this->createBeforeSettings();
		$email->refreshFormFields();

		$this->assertInstanceOf( Field\EditorField::class, $email->getFormField( 'body' ) );
		$this->assertNotNull( $email->getFormField( 'subject' ) );
		$this->assertTrue( $email->hasRecipientsField() );
		$this->assertNotNull( $email->getFormField( '_nonce' ) );
	}

	public function test_refresh_uses_code_editor_when_unfiltered_html() {
		update_option( 'notification_carriers', [ 'email' => [ 'unfiltered_html' => 'true' ] ] );

		$email = $this->createBeforeSettings();
		$email->refreshFormFields();

		$this->assertInstanceOf( Field\CodeEditorField::class, $email->getFormField( 'body' ) );
	}

	public function test_refresh_adds_headers_field_when_enabled() {
		update_option( 'notification_carriers', [ 'email' => [ 'headers' => 'true' ] ] );

		$email = $this->createBeforeSettings();
		$this->assertNull( $email->getFormField( 'headers' ) );

		$email->refreshFormFields();
		$this->assertNotNull( $email->getFormField( 'headers' ) );
		$this->assertCount( 1, array_filter( array_keys( $email->getFormFields() ), fn( $k ) => $k === 'headers' ) );
	}

	public function test_carrier_repository_refreshes_registered_email_carrier() {
		CarrierRepository::register();

		$this->assertInstanceOf( Field\EditorField::class, CarrierStore::get( 'email' )->getFormField( 'body' ) );
	}
}
