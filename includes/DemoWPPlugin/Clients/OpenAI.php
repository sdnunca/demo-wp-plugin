<?php

namespace DemoWPPlugin\Clients;

use DemoWPPlugin\Singleton;

/**
 * Client class that interfaces with ChatGPT.
 *
 * We're using it to generate blog post content based on the user prompt
 */
class OpenAI {
	use Singleton;

	const OPENAI_API_KEY_OPTION = 'sdn-demo-openai-key';
	protected $openai_api_key;

	/**
	 * Load the API key.
	 *
	 * @return void
	 */
	public function setup() {
		$this->openai_api_key = get_option( self::OPENAI_API_KEY_OPTION );
	}

	/**
	 * Fetch a blog post from OpenAI based on the provided prompt.
	 *
	 * @param $prompt
	 *
	 * @return array|mixed
	 */
	public function get_content( $prompt ) {
		$cache_key = 'i18n_openai_' . md5( $prompt );
		$cached    = wp_cache_get( $cache_key );
		if ( $cached ) {
			return $cached;
		}
		if ( empty( $this->openai_api_key ) ) {
			return new \WP_Error( 'OpenAI error', 'The OpenAI API key is empty' );
		}
		$system_prompt      = "You're an author writing articles for your blog. Provide a blog post content based on the user prompt.";
		$openai_temperature = 0.2;

		$messages = array(
			array(
				'role'    => 'system',
				'content' => $system_prompt,
			),
			array(
				'role'    => 'user',
				'content' => $prompt,
			),
		);

		$openai_response = wp_remote_post(
			'https://api.openai.com/v1/chat/completions',
			array(
				'timeout' => 60, // phpcs:ignore WordPressVIPMinimum.Performance.RemoteRequestTimeout.timeout_timeout
				'headers' => array(
					'Content-Type'  => 'application/json',
					'Authorization' => 'Bearer ' . $this->openai_api_key,
				),
				'body'    => wp_json_encode(
					array(
						'model'       => 'gpt-3.5-turbo',
						'max_tokens'  => 1000,
						'n'           => 1,
						'messages'    => $messages,
						'temperature' => $openai_temperature,
					)
				),
			)
		);
		if ( is_wp_error( $openai_response ) ) {
			return $openai_response;
		}
		$response_status = wp_remote_retrieve_response_code( $openai_response );
		if ( 200 !== $response_status ) {
			return new \WP_Error( 'OpenAI error', 'Could not fetch OpenAPI response, status code: ' . $response_status );
		}
		$output = json_decode( wp_remote_retrieve_body( $openai_response ), true );
		if ( empty( $output['choices'][0]['message']['content'] ) ) {
			return new \WP_Error( 'OpenAI error', 'Could not fetch OpenAPI response content' );
		}
		$message = trim( $output['choices'][0]['message']['content'] );
		wp_cache_set( $cache_key, $message );

		return $message;
	}
}
