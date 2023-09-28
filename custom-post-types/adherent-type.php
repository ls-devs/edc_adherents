<?php

/* ==================================================

Adherent Post Type Functions

================================================== */
    
add_action('init', 'adherentpost_register');  
  
function adherentpost_register() {  

    $labels = array(
        'name' => __('Article adhérent', "bilmylife"),
        'singular_name' =>__('Article adhérent', "bilmylife"),
        'add_new' => __('Ajouter', "bilmylife"),
        'add_new_item' => __('Ajouter un nouvel article adhérent', "bilmylife"),
        'edit_item' => __('Modifier un article adhérent', "bilmylife"),
        'new_item' => __('Nouvel Article adhérent', "bilmylife"),
        'view_item' => __('Consulter', "bilmylife"),
        'search_items' => __('Chercher', "bilmylife"),
        'not_found' =>  __('Aucun élément actuellement', "bilmylife"),
        'not_found_in_trash' => __('Aucun élément actuellement', "bilmylife"),
        'parent_item_colon' => ''
    );

    $args = array(  
		'description' => __( 'Article adhérent' ),
        'labels' => $labels,  
        'hierarchical' => false,
        'public' => true,  
        'show_ui' => true,
        'show_in_menu' => true, // affiche dans la colonne de gauche admin
        'show_in_nav_menus' => true,
        'rewrite' => array(
			'slug' => 'adherent',
			'with_front' => true
			),
		'menu_icon' => 'dashicons-calendar-alt',
        'supports' => array('title', 'thumbnail', 'editor', 'excerpt', 'revisions'),
		'taxonomies' => array('post_tag'),
		'exclude_from_search' => false,
        'has_archive' => true,
		'show_in_rest' => true,
       );  
  
    register_post_type( 'adherentpost' , $args );  
}  
?>