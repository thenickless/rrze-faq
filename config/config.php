<?php

namespace RRZE\FAQ\Config;

defined('ABSPATH') || exit;

define( 'LOGFILE', plugin_dir_path( __FILE__) . '../../rrze-faq.log' );

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
			'id'    => 'sync',
			'title' => __('Synchronization', 'rrze-faq' )
		],
		[
			'id'    => 'domains',
			'title' => __('Domains', 'rrze-faq' )
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
		'sync' => [
			[
				'name' => 'otrs_categories',
				'label' => __('Categories from OTRS', 'rrze-faq' ),
				'desc' => __('Please select all categories you\'d like to fetch FAQ to.', 'rrze-faq' ),
				'type' => 'multiselect'
			],
			[
				'name' => 'sync_check',
				'label' => __('Automatic', 'rrze-faq' ),
				'desc' => __('Syncronize FAQ automatically.', 'rrze-faq' ),
				'type' => 'checkbox'
			],
		],
		'domains' => [
			[
				'name' => 'new_domain',
				'label' => __('Add this domain', 'rrze-faq' ),
				'desc' => __('Enter the domain you want to receive FAQ from.<br><br><font color="red">Domain . wp-json/wp/v2/faq / faq_category / faq_tag auf Existenz prüfen</font>', 'rrze-faq' ),
				'type' => 'text'
			]
		],
    	'log' => [
        	[
          		'name' => 'logfile',
          		'type' => 'logfile',
          		'default' => LOGFILE
        	],
			[
		  		'name' => 'sync_timestamp',
		  		'type' => 'hidden'
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
        'datasource' => [
			'values' => [
				'website' => __( 'This website', 'rrze-faq' ),
				'otrs' => __( 'FAQ from Helpdesk (OTRS)', 'rrze-faq' ),
			],
			'default' => 'website',
			'field_type' => 'select',
			'label' => __( 'Data source', 'rrze-faq' ),
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
        'glossarystyle' => [
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
		// 'domain' => [
		// 	'default' => '',
		// 	'field_type' => 'text',
		// 	'label' => __( 'Domain', 'rrze-faq' ),
		// 	'type' => 'text'
        // ],
        // 'rest' => [
		// 	'default' => TRUE,
        //     'field_type' => 'checkbox',
        //     'label' => __( 'Rest', 'rrze-faq' ),
        //     'type' => 'boolean'
        // ]
    ];
}

function logIt( $msg ){
	$content = file_get_contents( LOGFILE );
	file_put_contents( LOGFILE, $msg . "\n" . $content, LOCK_EX);
}
  
function deleteLogfile(){
	unlink( LOGFILE );
}
  

