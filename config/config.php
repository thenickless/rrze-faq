<?php

namespace RRZE\FAQ\Config;

defined('ABSPATH') || exit;

/**
 * Gibt der Name der Option zurück.
 * @return array [description]
 */
function getOptionName()
{
    return 'rrze_faq';
}

/**
 * Gibt die Einstellungen des Menus zurück.
 * @return array [description]
 */
// function getMenuSettings()
// {
//     return [
//         'page_title'    => __('RRZE FAQ', 'rrze-faq'),
//         'menu_title'    => __('RRZE FAQ', 'rrze-faq'),
//         'capability'    => 'manage_options',
//         'menu_slug'     => 'rrze-faq',
//         'title'         => __('RRZE FAQ Settings', 'rrze-faq'),
//     ];
// }

/**
 * Gibt die Einstellungen der Inhaltshilfe zurück.
 * @return array [description]
 */
// function getHelpTab()
// {
//     return [
//         [
//             'id'        => 'rrze-faq-help',
//             'content'   => [
//                 '<p>' . __('Here comes the Context Help content.', 'rrze-faq') . '</p>'
//             ],
//             'title'     => __('Overview', 'rrze-faq'),
//             'sidebar'   => sprintf('<p><strong>%1$s:</strong></p><p><a href="https://blogs.fau.de/webworking">RRZE Webworking</a></p><p><a href="https://github.com/RRZE Webteam">%2$s</a></p>', __('For more information', 'rrze-faq'), __('RRZE Webteam on Github', 'rrze-faq'))
//         ]
//     ];
// }

/**
 * Gibt die Einstellungen der Optionsbereiche zurück.
 * @return array [description]
 */
// function getSections()
// {
//     return [
//         [
//             'id'    => 'basic',
//             'title' => __('Basic Settings', 'rrze-faq')
//         ],
//         [
//             'id'    => 'advanced',
//             'title' => __('Advanced Settings', 'rrze-faq')
//         ]
//     ];
// }

/**
 * Gibt die Einstellungen der Optionsfelder zurück.
 * @return array [description]
 */
// function getFields()
// {
//     return [
//         'basic' => [
//             [
//                 'name'              => 'text_input',
//                 'label'             => __('Text Input', 'rrze-faq'),
//                 'desc'              => __('Text input description.', 'rrze-faq'),
//                 'placeholder'       => __('Text Input placeholder', 'rrze-faq'),
//                 'type'              => 'text',
//                 'default'           => 'Title',
//                 'sanitize_callback' => 'sanitize_text_field'
//             ],
//             [
//                 'name'              => 'number_input',
//                 'label'             => __('Number Input', 'rrze-faq'),
//                 'desc'              => __('Number input description.', 'rrze-faq'),
//                 'placeholder'       => '5',
//                 'min'               => 0,
//                 'max'               => 100,
//                 'step'              => '1',
//                 'type'              => 'number',
//                 'default'           => 'Title',
//                 'sanitize_callback' => 'floatval'
//             ],
//             [
//                 'name'        => 'textarea',
//                 'label'       => __('Textarea Input', 'rrze-faq'),
//                 'desc'        => __('Textarea description', 'rrze-faq'),
//                 'placeholder' => __('Textarea placeholder', 'rrze-faq'),
//                 'type'        => 'textarea'
//             ],
//             [
//                 'name'  => 'checkbox',
//                 'label' => __('Checkbox', 'rrze-faq'),
//                 'desc'  => __('Checkbox description', 'rrze-faq'),
//                 'type'  => 'checkbox'
//             ],
//             [
//                 'name'    => 'multicheck',
//                 'label'   => __('Multiple checkbox', 'rrze-faq'),
//                 'desc'    => __('Multiple checkbox description.', 'rrze-faq'),
//                 'type'    => 'multicheck',
//                 'default' => [
//                     'one' => 'one',
//                     'two' => 'two'
//                 ],
//                 'options'   => [
//                     'one'   => __('One', 'rrze-faq'),
//                     'two'   => __('Two', 'rrze-faq'),
//                     'three' => __('Three', 'rrze-faq'),
//                     'four'  => __('Four', 'rrze-faq')
//                 ]
//             ],
//             [
//                 'name'    => 'radio',
//                 'label'   => __('Radio Button', 'rrze-faq'),
//                 'desc'    => __('Radio button description.', 'rrze-faq'),
//                 'type'    => 'radio',
//                 'options' => [
//                     'yes' => __('Yes', 'rrze-faq'),
//                     'no'  => __('No', 'rrze-faq')
//                 ]
//             ],
//             [
//                 'name'    => 'selectbox',
//                 'label'   => __('Dropdown', 'rrze-faq'),
//                 'desc'    => __('Dropdown description.', 'rrze-faq'),
//                 'type'    => 'select',
//                 'default' => 'no',
//                 'options' => [
//                     'yes' => __('Yes', 'rrze-faq'),
//                     'no'  => __('No', 'rrze-faq')
//                 ]
//             ]
//         ],
//         'advanced' => [
//             [
//                 'name'    => 'color',
//                 'label'   => __('Color', 'rrze-faq'),
//                 'desc'    => __('Color description.', 'rrze-faq'),
//                 'type'    => 'color',
//                 'default' => ''
//             ],
//             [
//                 'name'    => 'password',
//                 'label'   => __('Password', 'rrze-faq'),
//                 'desc'    => __('Password description.', 'rrze-faq'),
//                 'type'    => 'password',
//                 'default' => ''
//             ],
//             [
//                 'name'    => 'wysiwyg',
//                 'label'   => __('Advanced Editor', 'rrze-faq'),
//                 'desc'    => __('Advanced Editor description.', 'rrze-faq'),
//                 'type'    => 'wysiwyg',
//                 'default' => ''
//             ],
//             [
//                 'name'    => 'file',
//                 'label'   => __('File', 'rrze-faq'),
//                 'desc'    => __('File description.', 'rrze-faq'),
//                 'type'    => 'file',
//                 'default' => '',
//                 'options' => [
//                     'button_label' => __('Choose an Image', 'rrze-faq')
//                 ]
//             ]
//         ]
//     ];
// }


/**
 * Gibt die Einstellungen der Parameter für Shortcode für den klassischen Editor und für Gutenberg zurück.
 * @return array [description]
 */

function getShortcodeSettings(){
	return [
		'block' => [
            'blocktype' => 'rrze-faq/faq',
			'blockname' => 'faq',
			'title' => 'RRZE FAQ',
			'category' => 'widgets',
            'icon' => 'editor-help',
            'show_block' => 'content',
			'message' => __( 'Find the settings on the right side', 'rrze-faq' )
		],
        'glossaryStyle' => [
			'values' => [
				'' => __( '-- none --', 'rrze-faq' ),
				'a-z' => __( 'A - Z', 'rrze-faq' ),
				'tagcloud' => __( 'Tagcloud', 'rrze-faq' )
			],
			'default' => 'a-z',
			'field_type' => 'select',
			'label' => __( 'Glossary style', 'rrze-faq' ),
			'type' => 'string'
		],
        'glossary' => [
			'values' => [
				'category' => __( 'Categories', 'rrze-faq' ),
				'tag' => __( 'Tags', 'rrze-faq' )
			],
			'default' => 'category',
			'field_type' => 'select',
			'label' => __( 'Glossary content', 'rrze-faq' ),
			'type' => 'string'
		],
		'category' => [
			'default' => '',
			'field_type' => 'text',
			'label' => __( 'Category', 'rrze-faq' ),
			'type' => 'text'
        ],
		'tag' => [
			'default' => '',
			'field_type' => 'text',
			'label' => __( 'Tag', 'rrze-faq' ),
			'type' => 'text'
        ],
		'id' => [
			'default' => NULL,
			'field_type' => 'text',
			'label' => __( 'ID', 'rrze-faq' ),
			'type' => 'number'
		],
		'color' => [
			'values' => [
				'medfak' => __( 'Buttered Rum (medfak)', 'rrze-faq' ),
				'natfak' => __( 'Eastern Blue (natfak)', 'rrze-faq' ),
				'rwfak' => __( 'Flame Red (rwfak)', 'rrze-faq' ),
				'philfak' => __( 'Observatory (philfak)', 'rrze-faq' ),
				'' => __( 'Prussian Blue', 'rrze-faq' ),
				'techfak' => __( 'Raven (techfak)', 'rrze-faq' )
			],
			'default' => '',
			'field_type' => 'select',
			'label' => __( 'Color', 'rrze-faq' ),
			'type' => 'string'
        ],
		'domain' => [
			'default' => '',
			'field_type' => 'text',
			'label' => __( 'Domain', 'rrze-faq' ),
			'type' => 'text'
        ],
        'rest' => [
			'default' => TRUE,
            'field_type' => 'checkbox',
            'label' => __( 'Rest', 'rrze-faq' ),
            'type' => 'boolean'
        ]
    ];
}

