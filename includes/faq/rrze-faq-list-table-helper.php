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

                $content = wp_remote_get("https://{$v}/wp-json/wp/v2/glossary?per_page=2000", $args );

                $status_code = wp_remote_retrieve_response_code( $content );

                if ( 200 === $status_code ) {

                    $response[] = $content['body'];
                    
                    $category = wp_remote_get("https://{$v}/wp-json/wp/v2/glossary_category?per_page=100", $args);

                    $categories[] = $category['body'];
                    
                    $clean1 = array_filter($categories);

                    $o = array();
                    
                    foreach($categories as $a => $q) {
                        $cat = json_decode($clean1[$a], true);
                    }
                    
                    /*echo '<pre>';
                    print_r($cat[0]['slug']);
                    print_r(count($cat));
                    echo '</pre>';*/
                    
                    for($z = 0;  $z < sizeof($cat); $z++) {
                        $o[$z]['id'] = $cat[$z]['id'];
                        $o[$z]['slug'] = $cat[$z]['slug'];
                        $o[$z]['domain'] = $v;
                        
                    }
                    //$stack = add_option('slugStack');
                    //$stack[] = $o;
                    echo '<pre>';
                    //print_r($o);
                    echo '</pre>';
                    
                    /*if( get_option('slugStack') === false ) {
                        $stack = array();
                        $stack[1] = $o;
                        add_option('slugStack', $stack);
                    } else {
                        //echo 'zweites mal';
                        $u = get_option('slugStack');
                        for($a = 1; $a <= sizeof($u); $a++) {
                            //echo $u[$a]['wordpress.dev'][0]['slug'];
                            if(!array_key_exists($v, $u[$a])){
                          echo $u[1][$v][0]['slug'];
                            }
                        /*foreach($u as $q => $e) {
                            echo $e;
                        }*/
                      //  }
                        /*$add = get_option('slugStack');
                        for($a = 0; $a < sizeof($add); $a++) {
                            foreach($)
                            
                        }
                       
                        if(!in_array($url, $add)) {
                            array_push($server, $url);
                            update_option('slugStack', $server);
                        }*/
                    //}
                    
                    $u = get_option('slugStack');
                    
                    delete_option('slugStack');
                    
                    $t[] = $o;
                    
                }
            }
            
              /*for($e = 0; $e < sizeof($t); $e++) {
                       print_r($t[$e][$v]); 
                    }*/
                    
                    echo '<pre>';
                    print_r($t);
                    echo '</pre>';
                    $flag = 1;
                    
                    

            if($flag == 1) {
                $clean = array_filter($response);

                foreach($clean as $c => $v) {
                    $list[$c] = json_decode($clean[$c], true);
                }

                $i = 1;
                foreach($list as $k => $b) {
                    foreach($b as $b => $c) {
                        $item[$i]['id']         = $c['id'];
                        $item[$i]['title']      = $c['title']['rendered'];
                        $item[$i]['content']    = $c['content']['rendered'];
                        $url = parse_url($c['guid']['rendered']);
                        $item[$i]['domain']     = $url['host'];
                        $host = $url['host'];
                        $output = '';
                        $e = 0;
                        $item[$i]['glossary'] = $c['glossary_category'];
                        $p = $c['glossary_category'];
                        $l = count($p);
                        //print_r($p);
                        //$r = count($s);
                        //echo $r;
                        //$e = 0;
                     
                            foreach($item[$i]['glossary'] as $d => $z) {
                                        
                                    for($w = 0; $w < sizeof($t); $w++) {
                                          $p = count($t[$w]); 
                                           echo $p;
                                        
                                        for($j = 0; $j < count($t[$w]); $j++) {
                                            
                                              // echo '<pre>';
                                            if($z == $t[$w][$j]['id']) {
                                        $output .= $t[$w][$j]['slug'];
                //echo '</pre>';
                                            //$output .= $t[$w][$i]['slug'];
                                        }
                                           /* foreach($t as $u => $r) {
                                                //if($z == $r[$w]['id']) {
                                                    $output .= $r[$w]['id'];
                                                //}*/
                                            }
                                    
                                        //echo $w;
                                    //}  */          
                                    }
                                  
                                }
                               
                                //}*/
                                 //$output .= $z;
                            //}
                            //$output .= $z;
                        //}
                        //$count_items = (count($items));
                        /*foreach($items as $t => $e) {
                            foreach($o as $d => $z) {
                               //print_r($t);
                            }
                                //$output.= $e;
                          /*foreach($o as $w => $p) {
                            if ($e === $o[$w]['id']) {
                                //echo $o[$w]['slug'];
                              $output .= $o[$w]['slug'];// . ($count_items > 1 ? ',' : '');
                            }
                          }*/
                        //}
                        //$out = ($count_items > 1) ? substr($output, 0, -1) : '';
                        $item[$i]['category'] = $output;
                        $i++;
                    }
                }
                
                echo '<pre>';
                //print_r($item);
                echo '</pre>';
                
                return $item;
            } else {
                return;
            }
        } else {
            return;
        } 
    }
}