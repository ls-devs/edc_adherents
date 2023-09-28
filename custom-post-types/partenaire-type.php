<?php

/* ==================================================

Partenaire Post Type Functions

================================================== */
    
add_action('init', 'partenairepost_register');  
  
function partenairepost_register() {  

    $labels = array(
        'name' => __('Article partenaire', "bilmylife"),
        'singular_name' =>__('Article partenaire', "bilmylife"),
        'add_new' => __('Ajouter', "bilmylife"),
        'add_new_item' => __('Ajouter un nouvel article partenaire', "bilmylife"),
        'edit_item' => __('Modifier un article partenaire', "bilmylife"),
        'new_item' => __('Nouvel Article partenaire', "bilmylife"),
        'view_item' => __('Consulter', "bilmylife"),
        'search_items' => __('Chercher', "bilmylife"),
        'not_found' =>  __('Aucun élément actuellement', "bilmylife"),
        'not_found_in_trash' => __('Aucun élément actuellement', "bilmylife"),
        'parent_item_colon' => ''
    );

    $args = array(  
		'description' => __( 'Article partenaire' ),
        'labels' => $labels,  
        'hierarchical' => false,
        'public' => true,  
        'show_ui' => true,
        'show_in_menu' => true, // affiche dans la colonne de gauche admin
        'show_in_nav_menus' => true,
        'rewrite' => array(
			'slug' => 'partenaire',
			'with_front' => true
			),
		'menu_icon' => 'dashicons-calendar-alt',
        'supports' => array('title', 'thumbnail', 'editor', 'excerpt', 'revisions'),
		'taxonomies' => array('post_tag'),
		'exclude_from_search' => false,
        'has_archive' => true,
		'show_in_rest' => true,
       );  
  
    register_post_type( 'partenairepost' , $args );  
}  
?>