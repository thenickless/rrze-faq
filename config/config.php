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
function getMenuSettings()
{
    return [
        'page_title'    => __('RRZE FAQ', 'rrze-faq'),
        'menu_title'    => __('RRZE FAQ', 'rrze-faq'),
        'capability'    => 'manage_options',
        'menu_slug'     => 'rrze-faq',
        'title'         => __('RRZE FAQ Settings', 'rrze-faq'),
    ];
}

/**
 * Gibt die Einstellungen der Inhaltshilfe zurück.
 * @return array [description]
 */
function getHelpTab()
{
    return [
        [
            'id'        => 'rrze-faq-help',
            'content'   => [
                '<p>' . __('Here comes the Context Help content.', 'rrze-faq') . '</p>'
            ],
            'title'     => __('Overview', 'rrze-faq'),
            'sidebar'   => sprintf('<p><strong>%1$s:</strong></p><p><a href="https://blogs.fau.de/webworking">RRZE Webworking</a></p><p><a href="https://github.com/RRZE Webteam">%2$s</a></p>', __('For more information', 'rrze-faq'), __('RRZE Webteam on Github', 'rrze-faq'))
        ]
    ];
}

/**
 * Gibt die Einstellungen der Optionsbereiche zurück.
 * @return array [description]
 */
function getSections()
{
    return [
        [
            'id'    => 'basic',
            'title' => __('Basic Settings', 'rrze-faq')
        ],
        [
            'id'    => 'advanced',
            'title' => __('Advanced Settings', 'rrze-faq')
        ]
    ];
}

/**
 * Gibt die Einstellungen der Optionsfelder zurück.
 * @return array [description]
 */
function getFields()
{
    return [
        'basic' => [
            [
                'name'              => 'text_input',
                'label'             => __('Text Input', 'rrze-faq'),
                'desc'              => __('Text input description.', 'rrze-faq'),
                'placeholder'       => __('Text Input placeholder', 'rrze-faq'),
                'type'              => 'text',
                'default'           => 'Title',
                'sanitize_callback' => 'sanitize_text_field'
            ],
            [
                'name'              => 'number_input',
                'label'             => __('Number Input', 'rrze-faq'),
                'desc'              => __('Number input description.', 'rrze-faq'),
                'placeholder'       => '5',
                'min'               => 0,
                'max'               => 100,
                'step'              => '1',
                'type'              => 'number',
                'default'           => 'Title',
                'sanitize_callback' => 'floatval'
            ],
            [
                'name'        => 'textarea',
                'label'       => __('Textarea Input', 'rrze-faq'),
                'desc'        => __('Textarea description', 'rrze-faq'),
                'placeholder' => __('Textarea placeholder', 'rrze-faq'),
                'type'        => 'textarea'
            ],
            [
                'name'  => 'checkbox',
                'label' => __('Checkbox', 'rrze-faq'),
                'desc'  => __('Checkbox description', 'rrze-faq'),
                'type'  => 'checkbox'
            ],
            [
                'name'    => 'multicheck',
                'label'   => __('Multiple checkbox', 'rrze-faq'),
                'desc'    => __('Multiple checkbox description.', 'rrze-faq'),
                'type'    => 'multicheck',
                'default' => [
                    'one' => 'one',
                    'two' => 'two'
                ],
                'options'   => [
                    'one'   => __('One', 'rrze-faq'),
                    'two'   => __('Two', 'rrze-faq'),
                    'three' => __('Three', 'rrze-faq'),
                    'four'  => __('Four', 'rrze-faq')
                ]
            ],
            [
                'name'    => 'radio',
                'label'   => __('Radio Button', 'rrze-faq'),
                'desc'    => __('Radio button description.', 'rrze-faq'),
                'type'    => 'radio',
                'options' => [
                    'yes' => __('Yes', 'rrze-faq'),
                    'no'  => __('No', 'rrze-faq')
                ]
            ],
            [
                'name'    => 'selectbox',
                'label'   => __('Dropdown', 'rrze-faq'),
                'desc'    => __('Dropdown description.', 'rrze-faq'),
                'type'    => 'select',
                'default' => 'no',
                'options' => [
                    'yes' => __('Yes', 'rrze-faq'),
                    'no'  => __('No', 'rrze-faq')
                ]
            ]
        ],
        'advanced' => [
            [
                'name'    => 'color',
                'label'   => __('Color', 'rrze-faq'),
                'desc'    => __('Color description.', 'rrze-faq'),
                'type'    => 'color',
                'default' => ''
            ],
            [
                'name'    => 'password',
                'label'   => __('Password', 'rrze-faq'),
                'desc'    => __('Password description.', 'rrze-faq'),
                'type'    => 'password',
                'default' => ''
            ],
            [
                'name'    => 'wysiwyg',
                'label'   => __('Advanced Editor', 'rrze-faq'),
                'desc'    => __('Advanced Editor description.', 'rrze-faq'),
                'type'    => 'wysiwyg',
                'default' => ''
            ],
            [
                'name'    => 'file',
                'label'   => __('File', 'rrze-faq'),
                'desc'    => __('File description.', 'rrze-faq'),
                'type'    => 'file',
                'default' => '',
                'options' => [
                    'button_label' => __('Choose an Image', 'rrze-faq')
                ]
            ]
        ]
    ];
}


/**
 * Gibt die Einstellungen der Parameter für Shortcode für den klassischen Editor und für Gutenberg zurück.
 * @return array [description]
 */

function getShortcodeSettings(){
	return [
		'block' => [
            'blocktype' => 'rrze-faq/SHORTCODE-NAME', // dieser Wert muss angepasst werden
			'blockname' => 'SHORTCODE-NAME', // dieser Wert muss angepasst werden
			'title' => 'SHORTCODE-TITEL', // Der Titel, der in der Blockauswahl im Gutenberg Editor angezeigt wird
			'category' => 'widgets', // Die Kategorie, in der der Block im Gutenberg Editor angezeigt wird
            'icon' => 'admin-users',  // Das Icon des Blocks
            'show_block' => 'content', // 'right' or 'content' : Anzeige des Blocks im Content-Bereich oder in der rechten Spalte
			'message' => __( 'Find the settings on the right side', 'rrze-faq' ) // erscheint bei Auswahl des Blocks, wenn "show_block" auf 'right' gesetzt ist
		],
		'Beispiel-Textfeld-Text' => [
			'default' => 'ein Beispiel-Wert',
			'field_type' => 'text', // Art des Feldes im Gutenberg Editor
			'label' => __( 'Beschriftung', 'rrze-faq' ),
			'type' => 'text' // Variablentyp der Eingabe
		],
		'Beispiel-Textfeld-Number' => [
			'default' => 0,
			'field_type' => 'text', // Art des Feldes im Gutenberg Editor
			'label' => __( 'Beschriftung', 'rrze-faq' ),
			'type' => 'number' // Variablentyp der Eingabe
		],
		'Beispiel-Textarea-String' => [
			'default' => 'ein Beispiel-Wert',
			'field_type' => 'textarea',
			'label' => __( 'Beschriftung', 'rrze-faq' ),
			'type' => 'string',
			'rows' => 5 // Anzahl der Zeilen 
		],
		'Beispiel-Radiobutton' => [
			'values' => [
				'wert1' => __( 'Wert 1', 'rrze-faq' ), // wert1 mit Beschriftung
				'wert2' => __( 'Wert 2', 'rrze-faq' )
			],
			'wert2' => 'DESC', // vorausgewählter Wert
			'field_type' => 'radio',
			'label' => __( 'Order', 'rrze-faq' ), // Beschriftung der Radiobutton-Gruppe
			'type' => 'string' // Variablentyp des auswählbaren Werts
		],
		'Beispiel-Checkbox' => [
			'field_type' => 'checkbox',
			'label' => __( 'Beschriftung', 'rrze-faq' ),
			'type' => 'boolean',
			'checked'   => true // Vorauswahl: Haken gesetzt
        ],
        'Beispiel-Toggle' => [
            'field_type' => 'toggle',
            'label' => __( 'Beschriftung', 'rrze-faq' ),
            'type' => 'boolean',
            'checked'   => true // Vorauswahl: ausgewählt
        ],
        'Beispiel-Select' => [
			'values' => [
				'wert1' => __( 'Wert 1', 'rrze-faq' ),
				'wert2' => __( 'Wert 2', 'rrze-faq' )
			],
			'default' => 'wert1', // vorausgewählter Wert: Achtung: string, kein array!
			'field_type' => 'select',
			'label' => __( 'Beschrifung', 'rrze-faq' ),
			'type' => 'string' // Variablentyp des auswählbaren Werts
		],
        'Beispiel-Multi-Select' => [
			'values' => [
				'wert1' => __( 'Wert 1', 'rrze-faq' ),
				'wert2' => __( 'Wert 2', 'rrze-faq' ),
				'wert3' => __( 'Wert 2', 'rrze-faq' )
			],
			'default' => ['wert1','wert3'], // vorausgewählte(r) Wert(e): Achtung: array, kein string!
			'field_type' => 'multi_select',
			'label' => __( 'Beschrifung', 'rrze-faq' ),
			'type' => 'array',
			'items'   => [
				'type' => 'string' // Variablentyp der auswählbaren Werte
			]
        ]
    ];
}

