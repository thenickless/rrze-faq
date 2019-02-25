<?php

namespace RRZE\Glossar\Server;

/*
 *  $content = wp_remote_get("https://wordpress.dev/wp-json/wp/v2/glossary?filter[glossary_category]=studium-a-z&per_page=200", $args );
 */

Class FaqListTableHelper {

    public static function getGlossaryForWPListTable() {
        
        $args = array(
            'sslverify'   => false,
        );
        
        $registeredDomains = get_option('registerDomain');
        
        $flag = 0;
        
        if($registeredDomains) { 
        
            foreach($registeredDomains as $k => $v) {
		
		if (strpos($v, 'http') === 0) {
		    $domainurl = $v;
		} else {
		    $domainurl = 'https://'.$v;
		}

		$getfrom = $domainurl.'/wp-json/wp/v2/glossary?per_page=2000';
                $content = wp_remote_get($getfrom, $args );

                $status_code = wp_remote_retrieve_response_code( $content );

                if ( 200 === $status_code ) {

                    $response[] = $content['body'];
                    $getfrom = $domainurl.'/wp-json/wp/v2/glossary_category?per_page=100';
                    $category = wp_remote_get($getfrom, $args);

                    $categories[] = $category['body'];
                    
                    $clean1 = array_filter($categories);

                    $o = array();
                    
                    foreach($categories as $a => $q) {
                        $cat = json_decode($clean1[$a], true);
                    }
                    
                    for($z = 0;  $z < sizeof($cat); $z++) {
                        $o[$z]['id'] = $cat[$z]['id'];
                        $o[$z]['slug'] = $cat[$z]['slug'];
                        $o[$z]['domain'] = $domainurl;
                        
                    }
                    
                    $t[] = $o;
                    $flag = 1;
                    
                }
            }
            
            if($flag == 1) {
                $clean = array_filter($response);

                foreach($clean as $c => $v) {
                    $list[$c] = json_decode($clean[$c], true);
                }

                $i = 1;
                $o = 0;
                $separator = '';
                foreach($list as $k => $b) {
                    foreach($b as $b => $c) {
                        $item[$i]['id']         = $c['id'];
                        $item[$i]['title']      = $c['title']['rendered'];
                        $item[$i]['content']    = $c['content']['rendered'];
                        $url = parse_url($c['guid']['rendered']);
                        $item[$i]['domain']     = $url['host'];
                        $host = $url['host'];
                        $output = '';
                        $item[$i]['glossary'] = $c['glossary_category'];
                        foreach($item[$i]['glossary'] as $d => $z) {
                            for($w = 0; $w < sizeof($t); $w++) {
                                for($j = 0; $j < count($t[$w]); $j++) {
                                    if($z == $t[$w][$j]['id'] && $t[$w][$j]['domain'] == $host) {
                                        if($o > 1) $separator = ',';
                                        $output .= $t[$w][$j]['slug'] . $separator ;
                                        $o++;
                                    }
                                   
                                }
                               
                            }
                        }
                        $u = substr($output, -1, 1);
                        if($u == ',') {
                           $out = substr($output, 0, -1);
                        }else {
                            $out = $output;
                        }
                        $item[$i]['category'] = $out;
                        $i++;
                    }
                }
                return $item;
            } else {
                return;
            }
        } else {
            return;
        } 
    }
}