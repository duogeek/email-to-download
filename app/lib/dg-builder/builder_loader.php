<?php

function dg_builder_class( $class ) {
    
    $path = ED_FILES_DIR . '/lib/dg-builder/' . strtolower( $class ) . '.php';
    if( file_exists( $path ) ) {
        require_once $path;
    }
    
}

spl_autoload_register( 'dg_builder_class' );