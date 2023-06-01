<?php

namespace DemoWPPlugin;

use DemoWPPlugin\Clients\OpenAI;
use \WP_REST_Response;

class RestAPI {
	use Singleton;

	protected OpenAI $open_AI;

	public function setup() {
		$this->open_AI = OpenAI::instance();
	}

	public function run() {
		add_action( 'rest_api_init', array( $this, 'register_openai' ) );
	}

	public function register_openai() {
		register_rest_route(
			'demo-wp-plugin',
			'/openai',
			array(
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'get_openai_response' ),
				'permission_callback' => function () {
					return current_user_can( 'edit_posts' );
				},
			)
		);
	}

	public function get_openai_response( \WP_REST_Request $request ) {
		if ( empty( $request['prompt'] ) ) {
			return new WP_REST_Response( null, 400 );
		}
		$response = $this->open_AI->get_content( $request['prompt'] );

		if ( is_wp_error( $response ) ) {
			return new WP_REST_Response( null, 500 );
		}

		return array(
			'content' => $response
		);
	}

}
