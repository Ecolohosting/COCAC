<?php
/*
Plugin Name: Custom Post Types Plugin
Description: A plugin to register custom post types for the COCAC theme.
Version: 1.0
Author: ivan doroteo
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Register Custom Post Types
function cocac_register_custom_post_types() {
    // Registrar el post type 'eventos'
    $args = array(
        'label' => 'Eventos',
        'labels' => array(
            'name' => 'Eventos',
            'singular_name' => 'Evento',
            'add_new' => 'Añadir nuevo',
            'add_new_item' => 'Añadir nuevo evento',
            'edit_item' => 'Editar evento',
            'new_item' => 'Nuevo evento',
            'view_item' => 'Ver evento',
            'search_items' => 'Buscar eventos',
            'not_found' => 'No se encontraron eventos',
            'not_found_in_trash' => 'No hay eventos en la papelera',
        ),
        'public' => true,
        'has_archive' => true,
        'supports' => array( 'title', 'editor', 'thumbnail' ),
        'rewrite' => array( 'slug' => 'evento' ),
        'menu_icon' => 'dashicons-calendar-alt', // Icono de calendario para eventos
    );
    register_post_type( 'eventos', $args );

    // Registrar el post type 'galeria'
    $galeria_args = array(
        'label' => 'Galerías',
        'labels' => array(
            'name' => 'Galerías',
            'singular_name' => 'Galería',
            'add_new' => 'Añadir nueva',
            'add_new_item' => 'Añadir nueva galería',
            'edit_item' => 'Editar galería',
            'new_item' => 'Nueva galería',
            'view_item' => 'Ver galería',
            'search_items' => 'Buscar galerías',
            'not_found' => 'No se encontraron galerías',
            'not_found_in_trash' => 'No hay galerías en la papelera',
        ),
        'public' => true,
        'has_archive' => true,
        'supports' => array( 'title', 'thumbnail' ),
        'rewrite' => array( 'slug' => 'galeria' ),
        'menu_icon' => 'dashicons-format-gallery', // Icono de galería
    );
    register_post_type( 'galeria', $galeria_args );

    // Add more custom post types as needed
}

add_action( 'init', 'cocac_register_custom_post_types' );



?>