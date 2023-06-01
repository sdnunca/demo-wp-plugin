<?php

use DemoWPPlugin\Clients\OpenAI;

class OpenAI_Test extends WP_Mock\Tools\TestCase {
	public function setUp(): void {
		\WP_Mock::setUp();
	}

	public function tearDown(): void {
		\WP_Mock::tearDown();
	}

	public function test_empty_api_key() {
		update_option( OpenAI::OPENAI_API_KEY_OPTION, null );
		$client = new OpenAI();
		$client->setup();
		$response = $client->get_content( 'aaa' );

		$this->assertTrue( is_wp_error( $response ) );
	}

	public function test_error_openapi_response() {
		update_option( OpenAI::OPENAI_API_KEY_OPTION, 'aaa' );
		add_filter( 'pre_http_request', function () {
			return new WP_Error( 'bad request', 'bad request' );
		}, 10, 3 );
		$client = new OpenAI();
		$client->setup();
		$response = $client->get_content( 'bbb' );

		$this->assertTrue( is_wp_error( $response ) );
		$this->assertEquals( 'bad request', $response->get_error_code(), );
	}

	public function test_wrong_status_openapi_response() {
		update_option( OpenAI::OPENAI_API_KEY_OPTION, 'aaa' );
		add_filter( 'pre_http_request', function () {
			return array(
				'headers'  => '',
				'body'     => '',
				'response' => array( 'code' => 301 ),
				'cookies'  => '',
				'filename' => ''
			);
		}, 10, 3 );
		$client = new OpenAI();
		$client->setup();
		$response = $client->get_content( 'bbb' );

		$this->assertTrue( is_wp_error( $response ) );
		$this->assertEquals( 'Could not fetch OpenAPI response, status code: 301', $response->get_error_message() );
	}

	public function test_wrong_content_openapi_response() {
		update_option( OpenAI::OPENAI_API_KEY_OPTION, 'aaa' );
		add_filter( 'pre_http_request', function () {
			return array(
				'headers'  => '',
				'body'     => '',
				'response' => array( 'code' => 200 ),
				'cookies'  => '',
				'filename' => ''
			);
		}, 10, 3 );
		$client = new OpenAI();
		$client->setup();
		$response = $client->get_content( 'bbb' );

		$this->assertTrue( is_wp_error( $response ) );
		$this->assertEquals( 'Could not fetch OpenAPI response content', $response->get_error_message() );
	}

	public function test_ok_response() {
		update_option( OpenAI::OPENAI_API_KEY_OPTION, 'aaa' );
		wp_cache_delete( 'i18n_openai_' . md5( 'aaa' ) );
		add_filter( 'pre_http_request', function () {
			return array(
				'headers'  => '',
				'body'     => wp_json_encode( array(
					'choices' => array(
						array(
							'message' => array(
								'content' => 'content'
							)
						)
					)
				) ),
				'response' => array( 'code' => 200 ),
				'cookies'  => '',
				'filename' => ''
			);
		}, 10, 3 );
		$client = new OpenAI();
		$client->setup();
		$response = $client->get_content( 'aaa' );
		$this->assertEquals( 'content', $response );
	}
}