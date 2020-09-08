<?php

/**
 * Trigger the 
 */
 if( ! defined( 'WP_UNINSTALL_PLUGIN')) or die ("Sorry ! You don't have access to this page");

 $propositions = get_posts( array( 'post_type' => 'proposition', 'numberposts' => -1 ));

 foreach( $propositions as $prop){

    wp_delete_post($prop->ID, true);
 }