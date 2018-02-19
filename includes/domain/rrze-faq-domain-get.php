<?php

namespace RRZE\Synonym\Server;

Class DomainWPListTable {
    
     public static function listDomains() {
        
        $i = 0;
        
        $z = get_option('registerServer');
        
        if(!empty($z)) {
            $t = array_flip($z);
            $s = array_search(0, $t);
            foreach($z as $k => $v) {
                $item[$i]['id'] = $k;
                $item[$i]['domain'] = $v;
                $i++;
            }
            return $item;
            
        } else {
            return;
        }
    }
}