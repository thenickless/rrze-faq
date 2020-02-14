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
            'blocktype' => 'rrze-faq/glossary', // dieser Wert muss angepasst werden
			'blockname' => 'glossary', // dieser Wert muss angepasst werden
			'title' => 'RRZE Glossary', // Der Titel, der in der Blockauswahl im Gutenberg Editor angezeigt wird
			'category' => 'widgets', // Die Kategorie, in der der Block im Gutenberg Editor angezeigt wird
            'icon' => 'admin-users',  // Das Icon des Blocks
            'show_block' => 'content', // 'right' or 'content' : Anzeige des Blocks im Content-Bereich oder in der rechten Spalte
			'message' => __( 'Find the settings on the right side', 'rrze-faq' ) // erscheint bei Auswahl des Blocks, wenn "show_block" auf 'right' gesetzt ist
		],
		'category' => [
			'default' => '',
			'field_type' => 'text', // Art des Feldes im Gutenberg Editor
			'label' => __( 'Glossary', 'rrze-faq' ),
			'type' => 'text' // Variablentyp der Eingabe
        ],
		'id' => [
			'default' => NULL,
			'field_type' => 'text', // Art des Feldes im Gutenberg Editor
			'label' => __( 'ID', 'rrze-faq' ),
			'type' => 'number' // Variablentyp der Eingabe
		],
		'color' => [
			'default' => '',
			'field_type' => 'text', // Art des Feldes im Gutenberg Editor
			'label' => __( 'Color', 'rrze-faq' ),
			'type' => 'text' // Variablentyp der Eingabe
        ],
		'domain' => [
			'default' => '',
			'field_type' => 'text', // Art des Feldes im Gutenberg Editor
			'label' => __( 'Domain', 'rrze-faq' ),
			'type' => 'text' // Variablentyp der Eingabe
        ],
        'rest' => [
            'field_type' => 'toggle',
            'label' => __( 'Rest', 'rrze-faq' ),
            'type' => 'boolean',
            'checked'   => FALSE
        ]
    ];
}

