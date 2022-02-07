<?php
/**
 * Studio Metabox options.
 *
 * @author     ThemeFusion
 * @copyright  (c) Copyright by ThemeFusion
 * @link       https://theme-fusion.com
 * @package    Avada
 * @subpackage Core
 */

/**
 * Studio Page Settings.
 *
 * @param array $sections An array of our sections.
 * @return array
 */
function avada_page_options_tab_studio( $sections ) {

	$off_canvas_items = class_exists( 'AWB_Off_Canvas_Front_End' ) ? AWB_Off_Canvas_Front_End()->get_available_items() : [];

	$sections['studio'] = [
		'label'    => esc_attr__( 'Studio', 'Avada' ),
		'id'       => 'studio',
		'alt_icon' => 'fusiona-footer',
		'fields'   => [
			'studio_replace_params' => [
				'id'          => 'studio_replace_params',
				'label'       => esc_html__( 'Replace Global Params', 'Avada' ),
				'choices'     => [
					'yes' => esc_attr__( 'Yes', 'Avada' ),
					'no'  => esc_attr__( 'No', 'Avada' ),
				],
				'description' => esc_html__( 'Choose to enable or disable element global params replacements.', 'Avada' ),
				'type'        => 'radio-buttonset',
				'map'         => 'yesno',
				'transport'   => 'postMessage',
				'default'     => 'yes',
			],
			'exclude_form_studio'   => [
				'id'          => 'exclude_form_studio',
				'label'       => esc_html__( 'Exclude from Studio', 'Avada' ),
				'choices'     => [
					'yes' => esc_attr__( 'Yes', 'Avada' ),
					'no'  => esc_attr__( 'No', 'Avada' ),
				],
				'description' => esc_html__( 'Choose to include or exclude this template from studio content.', 'Avada' ),
				'type'        => 'radio-buttonset',
				'map'         => 'yesno',
				'transport'   => 'postMessage',
				'default'     => 'no',
			],
			'off_canvases'          => [
				'type'        => 'multiple_select',
				'label'       => esc_html__( 'Select Referenced Off Canvases', 'Avada' ),
				'description' => esc_html__( 'Select off canvases which are referenced in this item. Leaving blank if none.', 'Avada' ),
				'id'          => 'off_canvases',
				'choices'     => $off_canvas_items,
				'transport'   => 'postMessage',
			],
		],
	];

	return $sections;
}

/* Omit closing PHP tag to avoid "Headers already sent" issues. */
