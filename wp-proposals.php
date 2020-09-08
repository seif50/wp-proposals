<?php

/**
 * Plugin Name: WP Proposals
 * Description: WP Proposals permet aux internautes de pouvoir ajouter des propositions qui seront publié sur le site après modération de l’admin
 * Version: 1.0
 * Author: Seif Sendi
 */

 defined('ABSPATH') or die( "Sorry ! You don't have access to this page");
 

 class Proposals{

     function __construct(){

        add_action('init', array($this, 'custom_post_type'));

     }

     function register (){

        add_action('wp_enqueue_scripts', array($this, 'enqueue'));
        
     }

    function activate(){

        $this->custom_post_type();

        flush_rewrite_rules();

        $current_user = wp_get_current_user();
    
 
    /* Page qui contient le formulaire pour ajouter une proposition */
    $page_ajout_proposition = array(
      'post_title'  => __( 'Ajouter une proposition' ),
      'post_status' => 'publish',
      'post_author' => $current_user->ID,
      'post_type'   => 'page',
      'post_slug' => 'add-prop',
      'page_template'  => 'add-prop-template.php'
    );

    /* Page qui contient la liste des propositions */
    $page_list_propositions = array(
        'post_title'  => __( 'Propositions' ),
        'post_slug' => 'propostions',
        'post_status' => 'publish',
        'post_author' => $current_user->ID,
        'post_type'   => 'page',
        'page_template'  => 'propositions-template.php'
  
      );
    
    /* Inserer les deux nouvelles pages (propositions et ajouter une proposition) avec leurs templates */
    wp_insert_post( $page_ajout_proposition );
    wp_insert_post( $page_list_propositions );

  
    }
 
    function deactivate(){

        flush_rewrite_rules();
        
    }

    function enqueue(){

        wp_enqueue_style('mypluginstyle', plugins_url('/assests/mystyle.css', __FILE__));
        wp_enqueue_script('mypluginscript', plugins_url('/assests/myscript.js', __FILE__));

     
        
    }

    /* Le nouveau CPT proposition */
    function custom_post_type() {
        register_post_type('proposition',
            array(
                'labels'      => array(
                    'name'          => __('Propositions', 'textdomain'),
                    'singular_name' => __('Proposition', 'textdomain'),
                ),
                'supports' => array( 
                    'title', 'editor','author', 'thumbnail', 'comments', 
                ),
                'taxonomies' => array('Vie scolaire', 'Vie Politique', 'Vie personnelle', 'Juridique', 'Vie en Entreprise', 'Vie de famille'),

                    'public'      => true,
                    'has_archive' => true,
            )
        );

        /* L'ajout du CPT proposition avec quelques thématiques par défaut */
        $thematique = array(
            'name' => _x( 'Thématiques', 'taxonomy general name' ),
            'singular_name' => _x( 'Thématique', 'taxonomy singular name' ),
            'search_items' =>  __( 'Search in Thématiques' ),
            'all_items' => __( 'All Thématiques' ),
            'most_used_items' => null,
            'parent_item' => null,
            'parent_item_colon' => null,
            'edit_item' => __( 'Edit Thématique' ), 
            'update_item' => __( 'Update Thématique' ),
            'add_new_item' => __( 'Add new Thématique' ),
            'new_item_name' => __( 'New Thématique' ),
            'menu_name' => __( 'Thématiques' ),
        );
        $args = array(
            'hierarchical' => true,
            'labels' => $thematique,
            'show_ui' => true,
            'query_var' => true,
            'rewrite' => array( 'slug' => 'thematique' )
        );
        register_taxonomy( 'thematique', 'proposition', $args );

         $tags = array('Vie scolaire', 'Vie Politique', 'Vie personnelle', 'Juridique', 'Vie en Entreprise', 'Vie de famille');

        wp_set_post_terms( $tags, $tags,'thematique',false );
        

    }

    
 }

 if( class_exists('Proposals')){
     $proposals = new Proposals();
     $proposals->register();

     /* Ajout des nouvelles templates dans le theme */
     function add_page_template ($templates) {
        $templates['propositions-template.php'] = 'Propositions';
        $templates['add-prop-template.php'] = 'Add Proposition';
        return $templates;
        }
    add_filter ('theme_page_templates', 'add_page_template'); 

    /* Spécifier l'emplacement de chaque template ajoutée */

    function redirect_page_template ($template) {
        global $post;
        $post_slug=$post->post_name;
        if ($post_slug == 'propositions')
            $template = plugin_dir_path( __FILE__ ).'/templates/propositions-template.php';

        if ($post_slug == 'ajouter-une-proposition')
            $template = plugin_dir_path( __FILE__ ).'/templates/add-prop-template.php';
        return $template;
        }
    add_filter ('page_template', 'redirect_page_template');
}
 

 register_activation_hook(__FILE__, array($proposals, 'activate'));

 register_deactivation_hook(__FILE__, array($proposals, 'deactivate'));

 
 /* En cliquant sur le boutton Like on incrémente le nombre des likes dans une proposition */

add_action( 'wp_ajax_like_post', 'like_post' );
function like_post() {
    if ( ! wp_verify_nonce( $_REQUEST['nonce'], 'pt_like_it_nonce' ) || ! isset( $_REQUEST['nonce'] ) ) {
        exit( "No naughty business please" );
    }
 
    $likes = get_post_meta( $_REQUEST['post_id'], '_pt_likes', true );
    $likes = ( empty( $likes ) ) ? 0 : $likes;
    $new_likes = $likes + 1;
 
    update_post_meta( $_REQUEST['post_id'], '_pt_likes', $new_likes );

    wp_redirect(site_url()."/propositions");
    exit();
   
    
}

/* En cliquant sur le boutton Dislike on incrémente le nombre des dislikes dans une proposition */

add_action( 'wp_ajax_dislike_post', 'dislike_post' );
function dislike_post() {
    if ( ! wp_verify_nonce( $_REQUEST['nonce'], 'pt_like_it_nonce' ) || ! isset( $_REQUEST['nonce'] ) ) {
        exit( "No naughty business please" );
    }
 
    $dislikes = get_post_meta( $_REQUEST['post_id'], '_pt_dislikes', true );
    $dislikes = ( empty( $dislikes ) ) ? 0 : $dislikes;
    $new_dislikes = $dislikes + 1;
 
    update_post_meta( $_REQUEST['post_id'], '_pt_dislikes', $new_dislikes );

    wp_redirect(site_url()."/propositions");
    exit();
   
    
}

/* L'ajout d'un bouton dans le menu du theme qui redirige vers la page des propositions */

add_filter( 'wp_nav_menu_items', 'your_custom_menu_item', 10, 2 );
function your_custom_menu_item ( $items, $args ) {
    
        $items .= '<li class="button_prop"><a href="/propositions" style="color: white !important;">Propositions</a></li>';
   
    return $items;
}
