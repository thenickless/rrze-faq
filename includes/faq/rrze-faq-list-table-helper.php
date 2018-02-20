<?php

namespace RRZE\Glossar\Server;

Class FaqListTableHelper {

    public static function getGlossaryForWPListTable() {
        
        $args = array(
            'sslverify'   => false,
        );
        
                $content = wp_remote_get("https://wordpress.dev/wp-json/wp/v2/glossary?filter[glossary_category]=studium-a-z&per_page=200", $args );
                
                $status_code = wp_remote_retrieve_response_code( $content );
                
                if ( 200 === $status_code ) {
               
                    $response[] = $content['body'];
                    $flag = 1;
                }
            //}
            
            if($flag == 1) {
                $clean = array_filter($response);
            }

                foreach($clean as $c => $v) {
                    $list[$c] = json_decode($clean[$c], true);
                }

                $i = 1;
                foreach($list as $k => $v) {
                    foreach($v as $b => $c) {
                        $item[$i]['id']         = $c['id'];
                        $item[$i]['title']      = $c['title']['rendered'];
                        $item[$i]['content']       = $c['content']['rendered'];
                        $url = parse_url($c['guid']['rendered']);
                        $item[$i]['domain']     = $url['host'];
                        $i++;
                    }
                }
                return $item;
          /*  }
        } else {
            return;
        }*/
    }
}