<?php


/*
**  Question C
**
**  At Ormuco, we want to optimize every bits of software we write.
**  Your goal is to write a new library that can be integrated to the Ormuco stack.
**  Dealing with network issues everyday, latency is our biggest problem.
**  Thus, your challenge is to write a new Geo Distributed LRU (Least Recently Used) cache with time expiration.
**  This library will be used extensively by many of our services so it needs to meet the following criteria:
**
**
**
**    1 - Simplicity. Integration needs to be dead simple.
**    2 - Resilient to network failures or crashes.
**    3 - Near real time replication of data across Geolocation. Writes need to be in real time.
**    4 - Data consistency across regions
**    5 - Locality of reference, data should almost always be available from the closest region
**    6 - Flexible Schema
**    7 - Cache can expire
**
**
*/

/*
**
**   Solution assumptions :
**      -
**
**
**
**
**
**
**
*/

class Cash {

    // Array or resources, each resource is an array of two elements (name , last_used_datetime).
    private static $cash_content = [];

    private static $max_size = 10;

    private static $max_lifetime = 259200; // 3 days in seconds.
    
    const MY_NETWORK_CREDENTIALS = '196.23.325.25:5566' // how to reach me.

    
    // Shows, for each key, the nearest network location to find it.
    // Example : self::$network_index = [
    //        'resource_key1' => 'network_credential_1',
    //        'resource_key2' => 'network_credential_1',
    //         ...etc
    // ]
    // We assume there is not caching to do with the index.
    private static $network_index = [];
    

    public function getSize(){
        // We assume the capacity limitation is linked only to the number of entries.
        return count( self::$cash_content );
    }


    public function addResource( $resource_key ){

        // Decide first if we should keep a local copy in cach for this resource.
        // decide_on_copy() definition is missing, and no idea about its algorithme !!
        $make_local_copy = self::decide_on_copy( $resource_key );

        while( $make_local_copy && $this->getSize() >= self::$max_size ){
           $this->removeLRU();
        }

        $new_element['resource_key'] = $resource_key ;

        $d = new DateTime();
        $new_element['creation_timestamp'] = strtotime($d->format('Y-m-d\TH:i:s.u')); // value example. : 1362193965

        
        // We check if this key is available in our network nodes, so we consult the index.
        // self::is_offlinemode is also missing ! the idea is to fall offline for a while before checking back on network.        
        if( self::is_offlinemode && isset( self::$network_index[ $resource_key ] ) ){
            
            // getResourceFromRemote is not defined here, 
            // It is supposed to grab the resource from network.
            // $resource_content passed by reference to the function.
            $return_code = getResourceFromRemote( self::$network_index[ $resource_key ], $remote_resource_obj );
            
            if( $return_code = 'network_is_down' ) {
                // will have to set some local variables to manage offline mode and how long it will last ....
                // The call is static because network issues dont depend on instances !!
                self::fall_offline_for_awhile();
            } else {
                $new_element['resource_content'] = $remote_resource_obj['resource_content'];
                
                //update local index :
                self::$network_index[ $resource_key ] = $remote_resource_obj['remote_network_credentials'];
            }
        } else {
            // digVeryCostly() will return something big or interesting like an object !
            // and digVeryCostly() is the costly operation the cash is supposed to avoid.
            $new_element['resource_content'] = \Library1\Utility1::digVeryCostly( $resource_key );
            
            
            // This call will notify the other sites on network that the current site has
            // this data calculated costly on local, so they will note dig for it, instead 
            // they will ask the current site for it.
            // Every site (node) will have to update its index upon receipt of notification.
            notify_all_remote( $resource_key, self::MY_NETWORK_CREDENTIALS );
        }

        
        if( $make_local_copy ) {
            self::$cash_content[ $resource_key ] = $new_element;
        }
        
        
        return $new_element['resource_content'];


    }


    public function getResource( $resource_key ){
        
        
        if( !isset( self::$cash_content[$resource_key] ) ){
            return $this->addResource( $resource_key, $make_local_copy );
        }

        $d = new DateTime();
        if( strtotime($d->format('Y-m-d\TH:i:s.u')) -
            self::$cash_content[$resource_key]['creation_timestamp'] >
            self::$max_lifetime
            ){
            // Resource has expired.
            unset( self::$cash_content[$resource_key] );
            return $this->addResource( $resource_key );
        }

        self::$cash_content[$resource_key]['last_usedin_timestamp'] = strtotime($d->format('Y-m-d\TH:i:s.u'));

        return self::$cash_content[$resource_key]['resource_content'];

    }


    public function removeLRU(){

        $lru_datetime = 'xxx';
        $lru_resource_key = 'xxx';
        foreach( self::$cash_content as $i_key => $i_resource ){
            if( $lru_datetime == 'xxx' ||
                $i_resource['last_usedin_timestamp'] < $lru_datetime ){
                $lru_datetime = $i_resource['last_usedin_timestamp'];
                $lru_resource_key = $i_key;
            }
        }

        unset( self::$cash_content[ $lru_resource_key ] );

        return true;
    }



    // This function will have to update local index whenever we receive a notification
    // from a site that has just digged for a resource on his end.
    // it will be asyncrounous function called by a deamon-like programme ...
    public  function notification_received(){
        
    }
    
    

}