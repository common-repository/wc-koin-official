<?php

namespace WKO\Helpers;

/**
 * Name: Utils
 * Has the statics methods
 * @package Helpers
 * @since 1.0.0
 */
class Utils 
{
    /**
     * Parse view file name to constroller
     * 
     * @since 1.0.0
     * 
     * @param string $controller
     * @param string $namespace
     * @return string
     */
    public static function parse_controller( $vew, $namespace = "Actions" ) 
    {

        $split = str_split( $vew );
        $namespace = WKO_PLUGIN_NAMESPACE . "\\Controllers\\$namespace\\";
        $class_name = '';
        $count = 0;

        $next_upper = false;

        foreach ( $split as $letter ) {

            if ( $count === 0 ) {
                $class_name .= strtoupper( $letter );
                
            } else {

                if ( $letter === '_' ) {
                    $next_upper = true;
    
                } else {
                    
                    if ( $next_upper ) {
                        $class_name .= strtoupper( $letter );
    
                    } else {
                        $class_name .= $letter;
                    }
    
                    $next_upper = false;
                }
            }

            $count++;
        }

        $controller = $namespace .= $class_name;

        return $controller;
    }

    /**
     * List payment methods slugs
     * 
     * @since 1.0.0
     * @return array
     */
    public static function koin_payment_methods()
    {
        return [
            'wc-koin-billet',
            'wc-koin-pix',
            'wc-koin-credit'
        ];
    }

}