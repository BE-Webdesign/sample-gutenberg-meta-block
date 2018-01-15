<?php
/*
 * Plugin Name: Sample Meta Block!
 * Version: 0.1.0
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
 * Register meta field for the rest API.
 */
function meta_block_init() {
	// Register a post meta
	register_meta( 'post', 'notes', array(
		'show_in_rest' => true,
		'single'       => true,
	) );
}

add_action( 'init', 'meta_block_init' );

/**
 * A hack, that at some point will not be necessary and will instead have a
 * better API.
 */
function meta_block_change_post_block_template() {
	global $wp_post_types;

	// Create a template of our is great block.
	$is_great_block = array(
		'sample/secret-notes',
		array(
			'template_lock' => 'all',
		),
	);

	// Check if template exists and if not add template.
	if ( ! isset( $wp_post_types['post']->template ) || ! is_array( isset( $wp_post_types['post']->template ) ) ) {
		$wp_post_types['post']->template = array();
	}

	array_unshift( $wp_post_types['post']->template, $is_great_block );
}

add_action( 'init', 'meta_block_change_post_block_template' );
