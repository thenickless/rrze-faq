<?php

namespace RRZE\FAQ\Config;

defined('ABSPATH') || exit;

define( 'FAQ_LOGFILE', plugin_dir_path( __FILE__) . '../../rrze-faq.log' );


/**
 * Gibt der Name der Option zurück.
 * @return array [description]
 */
function getOptionName() {
    return 'rrze-faq';
}




/**
 * Gibt die Einstellungen des Menus zurück.
 * @return array [description]
 */
function getMenuSettings() {
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
function getHelpTab() {
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

function getSections() {
	return [ 
		[
			'id'    => 'doms',
			'title' => __('Domains', 'rrze-faq' )
		],
		[
			'id'    => 'sync',
			'title' => __('Synchronize', 'rrze-faq' )
		],
		[
		  	'id' => 'log',
		  	'title' => __('Logfile', 'rrze-faq' )
		]
	];   
}

/**
 * Gibt die Einstellungen der Optionsfelder zurück.
 * @return array [description]
 */

function getFields() {
	return [
		'doms' => [
			[
				'name' => 'new_name',
				'label' => __('Short name', 'rrze-faq' ),
				'desc' => __('Enter a short name for this domain.', 'rrze-faq' ),
				'type' => 'text'
			],
			[
				'name' => 'new_url',
				'label' => __('URL', 'rrze-faq' ),
				'desc' => __('Enter the domain\'s URL you want to receive FAQ from.', 'rrze-faq' ),
				'type' => 'text'
			]
		],
		'sync' => [
			[
				'name' => 'shortname',
				'label' => __('Short name', 'rrze-faq' ),
				'desc' => __('Use this name as attribute \'domain\' in shortcode [faq]', 'rrze-faq' ),
				'type' => 'plaintext',
				'default' => ''
			],
			[
				'name' => 'url',
				'label' => __('URL', 'rrze-faq' ),
				'desc' => '',
				'type' => 'plaintext',
				'default' => ''
			],
			[
				'name' => 'categories',
				'label' => __('Categories', 'rrze-faq' ),
				'desc' => __('Please select the categories you\'d like to fetch FAQ to.', 'rrze-faq' ),
				'type' => 'multiselect',
				'options' => []
			],
			[
				'name' => 'mode',
				'label' => __('Synchronize', 'rrze-faq' ),
				'desc' => '',
				'default' => '',
				'options' => [
					'' => __('Do not synchronize.', 'rrze-faq' ),
					'manuel' => __('Synchronize one time now.', 'rrze-faq' ),
					'auto' => __('Synchronize now and then automatically.', 'rrze-faq' ),
				],
				'type' => 'radio'
			],
			[
				'name' => 'hr',
				'label' => '',
				'desc' => '',
				'type' => 'line'
			]
		],		
    	'log' => [
        	[
          		'name' => 'logfile',
          		'type' => 'logfile',
          		'default' => FAQ_LOGFILE
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
            'blocktype' => 'rrze-faq/faq',
			'blockname' => 'faq',
			'title' => 'RRZE FAQ',
			'category' => 'widgets',
            'icon' => 'editor-help',
            'show_block' => 'content',
			'message' => __( 'Find the settings on the right side', 'rrze-faq' )
		],
		'domain' => [
			'default' => '',
			'field_type' => 'text',
			'label' => __( 'Domain', 'rrze-faq' ),
			'type' => 'text'
        ],
        'glossary' => [
			'values' => [
				'' => __( 'none', 'rrze-faq' ),
				'category' => __( 'Categories', 'rrze-faq' ),
				'tag' => __( 'Tags', 'rrze-faq' )
			],
			'default' => '',
			'field_type' => 'select',
			'label' => __( 'Glossary content', 'rrze-faq' ),
			'type' => 'string'
		],
        'glossarystyle' => [
			'values' => [
				'' => __( '-- hidden --', 'rrze-faq' ),
				'a-z' => __( 'A - Z', 'rrze-faq' ),
				'tagcloud' => __( 'Tagcloud', 'rrze-faq' ),
				'tabs' => __( 'Tabs', 'rrze-faq' )
			],
			'default' => 'a-z',
			'field_type' => 'radio',
			'label' => __( 'Glossary style', 'rrze-faq' ),
			'type' => 'string'
		],
		'category' => [
			'default' => '',
			'field_type' => 'text',
			'label' => __( 'Categories', 'rrze-faq' ),
			'type' => 'text'
        ],
		'tag' => [
			'default' => '',
			'field_type' => 'text',
			'label' => __( 'Tags', 'rrze-faq' ),
			'type' => 'text'
        ],
		'id' => [
			'default' => NULL,
			'field_type' => 'text',
			'label' => __( 'FAQ', 'rrze-faq' ),
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
        ]
    ];
}

function logIt( $msg ){
	$content = file_get_contents( FAQ_LOGFILE );
	file_put_contents( FAQ_LOGFILE, $msg . "\n" . $content, LOCK_EX);
}
  
function deleteLogfile(){
	unlink( FAQ_LOGFILE );
}
  

