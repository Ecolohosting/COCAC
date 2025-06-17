<?php
/**
 * Plugin Name: COCAC Gallery
 * Description: Permite crear galerías de imágenes y mostrarlas mediante un shortcode.
 * Version: 1.0.0
 * Author: Ecolohosting
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Registro del custom post type para galerías.
 */
function cocac_gallery_register_cpt() {
    $labels = array(
        'name'               => __( 'Galerías', 'cocac' ),
        'singular_name'      => __( 'Galería', 'cocac' ),
        'add_new'            => __( 'Añadir nueva', 'cocac' ),
        'add_new_item'       => __( 'Añadir nueva galería', 'cocac' ),
        'edit_item'          => __( 'Editar galería', 'cocac' ),
        'new_item'           => __( 'Nueva galería', 'cocac' ),
        'view_item'          => __( 'Ver galería', 'cocac' ),
        'search_items'       => __( 'Buscar galerías', 'cocac' ),
        'not_found'          => __( 'No se encontraron galerías', 'cocac' ),
        'not_found_in_trash' => __( 'No se encontraron galerías en la papelera', 'cocac' ),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'show_ui'            => true,
        'menu_icon'          => 'dashicons-format-gallery',
        'supports'           => array( 'title' ),
    );

    register_post_type( 'cocac_gallery', $args );
}
add_action( 'init', 'cocac_gallery_register_cpt' );

/**
 * Añade la metabox para seleccionar imágenes.
 */
function cocac_gallery_add_metabox() {
    add_meta_box(
        'cocac_gallery_images',
        __( 'Imágenes de la galería', 'cocac' ),
        'cocac_gallery_metabox_html',
        'cocac_gallery'
    );
}
add_action( 'add_meta_boxes', 'cocac_gallery_add_metabox' );

/**
 * HTML de la metabox de imágenes.
 */
function cocac_gallery_metabox_html( $post ) {
    $image_ids = get_post_meta( $post->ID, '_cocac_gallery_image_ids', true );
    ?>
    <div>
        <button type="button" class="button cocac-add-gallery-images"><?php esc_html_e( 'Agregar imágenes', 'cocac' ); ?></button>
        <input type="hidden" id="cocac_gallery_image_ids" name="cocac_gallery_image_ids" value="<?php echo esc_attr( $image_ids ); ?>" />
        <ul class="cocac-gallery-preview">
            <?php
            if ( $image_ids ) {
                $ids = explode( ',', $image_ids );
                foreach ( $ids as $id ) {
                    $src = wp_get_attachment_image_src( $id, 'thumbnail' );
                    if ( $src ) {
                        echo '<li data-id="' . esc_attr( $id ) . '"><img src="' . esc_url( $src[0] ) . '" /></li>';
                    }
                }
            }
            ?>
        </ul>
    </div>
    <?php
}

/**
 * Guarda los IDs de las imágenes seleccionadas.
 */
function cocac_gallery_save_post( $post_id ) {
    if ( isset( $_POST['cocac_gallery_image_ids'] ) ) {
        update_post_meta( $post_id, '_cocac_gallery_image_ids', sanitize_text_field( $_POST['cocac_gallery_image_ids'] ) );
    }
}
add_action( 'save_post_cocac_gallery', 'cocac_gallery_save_post' );

/**
 * Encola scripts y estilos en el administrador.
 */
function cocac_gallery_admin_assets( $hook ) {
    if ( 'post.php' !== $hook && 'post-new.php' !== $hook ) {
        return;
    }
    $screen = get_current_screen();
    if ( 'cocac_gallery' !== $screen->post_type ) {
        return;
    }
    wp_enqueue_media();
    wp_enqueue_script( 'cocac-gallery-admin', plugin_dir_url( __FILE__ ) . 'assets/js/admin.js', array( 'jquery' ), '1.0', true );
    wp_enqueue_style( 'cocac-gallery-admin', plugin_dir_url( __FILE__ ) . 'assets/css/admin.css' );
}
add_action( 'admin_enqueue_scripts', 'cocac_gallery_admin_assets' );

/**
 * Shortcode para mostrar la galería.
 * Uso: [cocac_gallery id="123"]
 */
function cocac_gallery_shortcode( $atts ) {
    $atts = shortcode_atts( array( 'id' => 0 ), $atts );
    $post_id = intval( $atts['id'] );
    if ( ! $post_id ) {
        return '';
    }
    $image_ids = get_post_meta( $post_id, '_cocac_gallery_image_ids', true );
    if ( ! $image_ids ) {
        return '';
    }
    $ids    = explode( ',', $image_ids );
    $output = '<div class="cocac-gallery">';
    foreach ( $ids as $id ) {
        $src = wp_get_attachment_image_src( $id, 'large' );
        if ( $src ) {
            $output .= '<img src="' . esc_url( $src[0] ) . '" class="cocac-gallery-image" />';
        }
    }
    $output .= '</div>';
    return $output;
}
add_shortcode( 'cocac_gallery', 'cocac_gallery_shortcode' );

