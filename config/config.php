<?php

namespace RRZE\FAQ\Config;

defined('ABSPATH') || exit;

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
			'id'    => 'faqsync',
			'title' => __('Synchronize', 'rrze-faq' )
		],
		[
		  	'id' => 'faqlog',
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
		'faqsync' => [
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
				'name' => 'donotsync',
				'label' => __('Synchronize', 'rrze-faq' ),
				'desc' => __('Do not synchronize', 'rrze-faq' ),
				'type' => 'checkbox',
			],
			// [
			// 	'name' => 'mode',
			// 	'label' => __('Synchronize', 'rrze-faq' ),
			// 	'desc' => __( 'All FAQ that match to the selected categories will be updated or inserted. Already synchronized FAQ that refer to categories which are not selected will be deleted. FAQ that have been deleted at the remote website will be deleted on this website, too.', 'rrze-faq' ),
			// 	'default' => '',
			// 	'options' => [
			// 		'' => __('Do not synchronize.', 'rrze-faq' ),
			// 		'manual' => __('Synchronize one time now.', 'rrze-faq' ),
			// 		'auto' => __('Synchronize now and then automatically.', 'rrze-faq' ),
			// 	],
			// 	'type' => 'radio'
			// ],
			[
				'name' => 'hr',
				'label' => '',
				'desc' => '',
				'type' => 'line'
			],
			[
				'name' => 'info',
				'label' => __('Info', 'rrze-faq' ),
				'desc' => __( 'All FAQ that match to the selected categories will be updated or inserted. Already synchronized FAQ that refer to categories which are not selected will be deleted. FAQ that have been deleted at the remote website will be deleted on this website, too.', 'rrze-faq' ),
				'type' => 'plaintext',
				'default' => __( 'All FAQ that match to the selected categories will be updated or inserted. Already synchronized FAQ that refer to categories which are not selected will be deleted. FAQ that have been deleted at the remote website will be deleted on this website, too.', 'rrze-faq' ),
			],
			[
				'name' => 'autosync',
				'label' => __('Mode', 'rrze-faq' ),
				'desc' => __('Synchronize automatically', 'rrze-faq' ),
				'type' => 'checkbox',
			],
			[
				'name' => 'frequency',
				'label' => __('Frequency', 'rrze-faq' ),
				'desc' => '',
				'default' => 'daily',
				'options' => [
					'daily' => __('daily', 'rrze-faq' ),
					'twicedaily' => __('twicedaily', 'rrze-faq' )
				],
				'type' => 'select'
			],
		],		
    	'faqlog' => [
        	[
          		'name' => 'faqlogfile',
          		'type' => 'logfile',
          		'default' => FAQLOGFILE
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
			'field_type' => 'select',
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
		'hide_accordeon' => [
			'field_type' => 'toggle',
			'label' => __( 'Hide accordeon', 'rrze-faq' ),
			'type' => 'boolean',
			'default' => FALSE,
			'checked'   => FALSE
		],	  
		'hide_title' => [
			'field_type' => 'toggle',
			'label' => __( 'Hide title', 'rrze-faq' ),
			'type' => 'boolean',
			'default' => FALSE,
			'checked'   => FALSE
		],	  
		'expand_all_link' => [
			'field_type' => 'toggle',
			'label' => __( 'Show "expand all" button', 'rrze-faq' ),
			'type' => 'boolean',
			'default' => FALSE,
			'checked'   => FALSE
		],	  
		'load_open' => [
			'field_type' => 'toggle',
			'label' => __( 'Load website with opened accordeons', 'rrze-faq' ),
			'type' => 'boolean',
			'default' => FALSE,
			'checked'   => FALSE
		],	  
		'color' => [
			'values' => [
				'med' => 'med',
				'nat' => 'nat',
				'rw' => 'rw',
				'phil' => 'phil',
				'tk' => 'tk'
			],
			'default' => 'tk',
			'field_type' => 'select',
			'label' => __( 'Color', 'rrze-faq' ),
			'type' => 'string'
		],
		'additional_class' => [
			'default' => '',
			'field_type' => 'text',
			'label' => __( 'Additonal CSS-class(es) for sourrounding DIV', 'rrze-faq' ),
			'type' => 'text'
		],
        'sort' => [
			'values' => [
				'title' => __( 'Title', 'rrze-faq' ),
				'id' => __( 'ID', 'rrze-faq' ),
				'sort_criterion' => __( 'Sort criterion', 'rrze-faq' )
			],
			'default' => 'title',
			'field_type' => 'select',
			'label' => __( 'Sort', 'rrze-faq' ),
			'type' => 'string'
		],
        'order' => [
			'values' => [
				'ASC' => __( 'ASC', 'rrze-faq' ),
				'DESC' => __( 'DESC', 'rrze-faq' )
			],
			'default' => 'ASC',
			'field_type' => 'select',
			'label' => __( 'Order', 'rrze-faq' ),
			'type' => 'string'
		]				
    ];
}

function logIt( $msg ){
	date_default_timezone_set('Europe/Berlin');
	$msg = date("Y-m-d H:i:s") . ' | ' . $msg;
	if ( file_exists( FAQLOGFILE ) ){
		$content = file_get_contents( FAQLOGFILE );
		$content = $msg . "\n" . $content;
	}else {
		$content = $msg;
	}
	file_put_contents( FAQLOGFILE, $content, LOCK_EX);
}
  
function deleteLogfile(){
	unlink( FAQLOGFILE );
}
  

