<?php
/*
 * Plugin Name: Sample Meta Block!
 * Version: 0.2.0
 * Author: Edwin Cromley
 * Author URI: https://edwincromley.com
 * License: GPL3+
 *
 * Description: A sample meta block to illustrate how easy using Gutenberg is.
 */

/**
 * Enqueue block assets.
 */
function meta_block_enqueue_block_editor_assets() {
	// Enqueue JS that registers a block.
	wp_enqueue_script(
		'meta-block',
		plugins_url( 'meta-block.js', __FILE__ ),
		// Here we declare our dependencies for creating the block.
		array( 'wp-blocks', 'wp-element', 'wp-components' )
	);
}

add_action( 'enqueue_block_editor_assets', 'meta_block_enqueue_block_editor_assets' );

/**
 * The authentication callback for our secrent notes meta key.
 *
 * @param boolean $allowed Whether the user can add the post meta. Default false.
 * @param string $meta_key The meta key.
 * @param int $post_id Post ID.
 * @param int $user_id User ID.
 * @param string $cap Capability name.
 * @param array $caps User capabilities.
 * @return boolean Whether the user has permission or not to
 */
function meta_block_secret_notes_auth_callback( $allowed, $meta_key, $post_id, $user_id, $cap, $caps ) {
	if ( current_user_can( 'edit_post', $post_id ) ) {
		return true;
	}

	return false;
}

/**
 * Register meta field for the rest API.
 */
function meta_block_init() {
	// Register a post meta
	register_meta( 'post', 'notes', array(
		'show_in_rest'  => true,
		'single'        => true,
		'auth_callback' => 'meta_block_secret_notes_auth_callback',
	) );
}

add_action( 'init', 'meta_block_init' );

/**
 * Filters out the notes meta field from rest response for unauthenticated users.
 *
 * @param WP_REST_Response $response The response object.
 * @param WP_Post          $post     Post object.
 * @param WP_REST_Request  $request  Request object.
 * @return WP_REST_Response The modified rest response.
 */
function meta_block_make_secret_notes_secret( $response, $post, $request ) {
	$data = $response->get_data();

	if ( isset( $data['meta']['notes'] ) && ! current_user_can( 'edit_post', $post->ID ) ) {
		unset( $data['meta']['notes'] );
		$response->set_data( $data );
	}

	return $response;
}

add_filter( 'rest_prepare_post', 'meta_block_make_secret_notes_secret', 10, 3 );

/**
 * Register the block template for the post post type.
 *
 * @param array  $args      Associative array of data used during registration.
 * @param string $post_type The current post type being registered.
 * @return array Associative array of data used during registration.
 */
function meta_block_change_post_block_template( $args, $post_type ) {
	if ( $post_type === 'post' ) {
		// Create a template of our is great block.
		$secret_notes_block = array(
			'sample/secret-notes',
			array(
				'template_lock' => 'all',
			),
		);

		// Check if template exists and if not add template.
		if ( ! isset( $args['template'] ) || ! is_array( $args['template'] ) ) {
			$args['template'] = array();
		}

		array_unshift( $args['template'], $secret_notes_block );
	}

	return $args;
}

add_filter( 'register_post_type_args', 'meta_block_change_post_block_template', 10, 2 );
