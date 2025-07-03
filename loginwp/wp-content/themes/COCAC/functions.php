<?php
/* CALL STYLES FROM PARENT */
function theme_enqueue_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
}
add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );

/***** JQUERY *****/
function customjs(){
    /* JS CUSTOM */
    wp_enqueue_script('custom', get_stylesheet_directory_uri() . '/js/custom.js', array(), '1.0.0', true);

    /* CSS OWL CAROUSEL */
    // wp_enqueue_script('owlcarouseljs', get_stylesheet_directory_uri() . '/js/owl.carousel.min.js', '', '', true);
}
add_action('wp_enqueue_scripts', 'customjs');

/***** CUSTOM CSS *****/
function customcss(){
    /* CSS CUSTOM */
    wp_enqueue_style( 'custom-style', get_stylesheet_directory_uri() . '/css/custom.css' );
    // wp_enqueue_style( 'custom-min-style', get_stylesheet_directory_uri() . '/css/custom-min.css' );

    /* JS OWL CAROUSEL */
    // wp_enqueue_style('owlcarousel-css', get_stylesheet_directory_uri() . '/css/owl.carousel.min.css'); 

    /* CDN FONT AWESOME */
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css', array(), '6.0.0-beta3');
   
}
add_action('wp_enqueue_scripts','customcss');

/***** MISCELANEOUS *****/

/*  CHANGE LOGO IN LOGIN */
function mostrar_logo_divi_en_login() {
    $logo_id = et_get_option( 'divi_logo' );
    if ( $logo_id && is_string($logo_id) ) {
        $logo_url = $logo_id ;
    } else {
        $logo_url = 'https://ecolohosting.com/images/logo-noborrar.png';
    }
    echo '<style type="text/css">
            body.login div#login h1 a {
                background-image: url("' . esc_url( $logo_url ) . '");
                background-size: contain;
                background-repeat: no-repeat;
                width: 100%;
            }
        </style>';
}
add_action( 'login_enqueue_scripts', 'mostrar_logo_divi_en_login' );


/***** PRACTICAL SHORTCODES *****/

/* CALL MENU BY SHORTCODE */
/* [menu name="-your menu name-" class="-your class-"] */
add_filter( 'the_title', function( $title, $item_id ) {
    if ( 'nav_menu_item' === get_post_type( $item_id ) ) {
        return do_shortcode( $title );
    }
    return $title;
}, 10, 2 );
function print_menu_shortcode($atts, $content = null) {
extract(shortcode_atts(array( 'name' => null, 'class' => null ), $atts));
return wp_nav_menu( array( 'menu' => $name, 'menu_class' => $class, 'echo' => false ) );
}
add_shortcode('menu', 'print_menu_shortcode');

/* CALL SITE DOMAIN BY SHORTCODE TO AVOID ABSOLUTE LINKS */
/* [url page="slug"] */
function url_shortcode( $atts ) {
    $atts = shortcode_atts( array(
        'page' => '',
    ), $atts );
    
    return home_url( '/' . $atts['page'] );
}
add_shortcode('url', 'url_shortcode');

/* CALL YEAR BY SHORTCODE */
/* [year] */
function year_shortcode() {
    return date('Y');
}
add_shortcode('year', 'year_shortcode');

/* SHORTCODE INSIDE MODULES */ 
/* [showmodule id="module id number"] */ 
function showmodule_shortcode($moduleid) {
    extract(shortcode_atts(array('id' =>'*'),$moduleid)); 
    return do_shortcode('[et_pb_section global_module="'.$id.'"][/et_pb_section]');
}
add_shortcode('showmodule', 'showmodule_shortcode');



/***** WP HIDE ERRORS *****/

/* REMOVE LOGIN ERRORS */
function login_errors_message() {
    return 'Ooooops!';
}
add_filter('login_errors', 'login_errors_message');

/* LIMIT THE USER FOR WRONG LOGIN ATTEMPTS */ 
function limit_login_attempts() {
    if (!session_id()) {
        session_start();
    }

    $max_attempts = 5;
    $lockout_time = 15 * 60;

    if (isset($_SESSION['login_lockout']) && time() < $_SESSION['login_lockout']) {
        $time_remaining = $_SESSION['login_lockout'] - time();
        $minutes = ceil($time_remaining / 60);
        $message = "Has alcanzado el número máximo de intentos de inicio de sesión fallidos. Inténtalo de nuevo en $minutes minutos.";
        return new WP_Error('too_many_attempts', $message);
    }

    add_action('wp_login_failed', function($username) use ($max_attempts, $lockout_time) {
        if (!isset($_SESSION['login_attempts'])) {
            $_SESSION['login_attempts'] = 0;
        }

        $_SESSION['login_attempts']++;

        if ($_SESSION['login_attempts'] >= $max_attempts) {
            $_SESSION['login_lockout'] = time() + $lockout_time;
        }
    });

    add_action('wp_login', function() {
        unset($_SESSION['login_attempts']);
        unset($_SESSION['login_lockout']);
    });
}
add_action('init', 'limit_login_attempts');


/*  REMOVE CRAP FROM HEADER */
remove_action( 'wp_head',               'adjacent_posts_rel_link',       10, 0);
remove_action( 'wp_head',               'feed_links',                    2     );
remove_action( 'wp_head',               'feed_links_extra',              3     );
remove_action( 'wp_head',               'rsd_link'                             );
remove_action( 'wp_head',               'wlwmanifest_link'                     );
remove_action( 'wp_head',               'index_rel_link'                       );
remove_action( 'wp_head',               'parent_post_rel_link',          10, 0 );
remove_action( 'wp_head',               'start_post_rel_link',           10, 0 );
remove_action( 'wp_head',               'adjacent_posts_rel_link_wp_head', 10, 0 );
remove_action( 'wp_head',               'locale_stylesheet'                    );
remove_action( 'publish_future_post',   'check_and_publish_future_post', 10, 1 );
remove_action( 'wp_head',               'wp_generator'                         );
remove_action( 'wp_footer',             'wp_print_footer_scripts'              );
remove_action( 'wp_head',               'wp_shortlink_wp_head',          10, 0 );
remove_action( 'template_redirect',     'wp_shortlink_header',           11, 0 );
remove_action( 'wp_head',               'print_emoji_detection_script', 7);
remove_action( 'wp_print_styles',       'print_emoji_styles');



/* DEACTIVATE HTML ON COMMENTS */
add_filter('pre_comment_content', 'wp_specialchars');

/* REMOVE WP VERSION FROM RSS*/
add_filter('the_generator', '__return_empty_string');

/* REMOVE SCRIPTS VERSIONS */
function shapeSpace_remove_version_scripts_styles($src) {
    if (strpos($src, 'ver=')) {
        $src = remove_query_arg('ver', $src);
    }
    return $src;
}
add_filter('style_loader_src', 'shapeSpace_remove_version_scripts_styles', 9999);
add_filter('script_loader_src', 'shapeSpace_remove_version_scripts_styles', 9999);

/* PROTECT SYSTEM FILES */
function protect_important_files() {
    if ( !is_admin() && !is_user_logged_in() ) {
        $blocked_files = array( '.htaccess', 'wp-config.php' );
        foreach ( $blocked_files as $file ) {
            if ( file_exists( ABSPATH . $file ) ) {
                @chmod( ABSPATH . $file, 0644 );
            }
        }
    }
}
add_action('init', 'protect_important_files');

/* PROTECTS USERS FOR BEING NUMBERED */
if (!is_admin()) {
    add_action('init', 'block_user_enum');
    function block_user_enum() {
        if (!empty($_GET['author'])) {
            wp_redirect(site_url());
            exit;
        }
    }
}

/* START CODING FROM HERE */

// Shortcode to display an ACF iframe field
// Usage: [acf_iframe]
// Make sure you have an ACF field with the name 'ubicacion_evento' set up in your ACF settings.
function shortcode_iframe_acf() {
    // Obtener el valor del campo ACF
    $iframe = get_field('ubicacion_evento');

    // Si existe el iframe, lo mostramos
    if ($iframe) {
        return $iframe; // Si confías en el contenido tal cual
        // O más seguro (permite solo ciertas etiquetas)
        /*
        return wp_kses($iframe, array(
            'iframe' => array(
                'src' => true,
                'width' => true,
                'height' => true,
                'frameborder' => true,
                'allowfullscreen' => true,
            ),
        ));
        */
    }

    return ''; // Si no hay iframe
}
add_shortcode('acf_iframe', 'shortcode_iframe_acf');





// Shortcode para mostrar cualquier campo ACF: [acf field="nombre_del_campo"]
function acf_field_shortcode($atts) {
    $atts = shortcode_atts( array(
        'field' => '',
        'post_id' => get_the_ID(),
    ), $atts );

    if ( empty( $atts['field'] ) ) return '';

    $value = get_field( $atts['field'], $atts['post_id'] );
    return $value ? $value : '';
}
add_shortcode('acf', 'acf_field_shortcode');

// Shortcode para mostrar tabs de galería y cargar CSS/JS personalizados
function gallery_tabs_shortcode($atts, $content = null) {
    // Enqueue CSS y JS solo cuando se usa el shortcode
    wp_enqueue_style('gallery-tabs-css', get_stylesheet_directory_uri() . '/css/estilos-tab-gallery.css', array(), '1.0.0');
    wp_enqueue_style('magnific-popup-css', get_stylesheet_directory_uri() . '/css/magnific-popup.css', array(), '1.0.0');
    wp_enqueue_script('gallery-tabs-js', get_stylesheet_directory_uri() . '/js/gallery-tab.js', array('jquery'), '1.0.0', true);
    wp_enqueue_script('magnific-popup-js', get_stylesheet_directory_uri() . '/js/jquery.magnific-popup.min.js', array('jquery'), '1.0.0', true);
    wp_enqueue_script('imagesloaded-js', 'https://unpkg.com/imagesloaded@5/imagesloaded.pkgd.min.js', array('jquery'), null, true);
    wp_enqueue_script('masonry-js', 'https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.min.js', array('jquery'), null, true);

    // Estructura HTML básica para los tabs
    ob_start();
    ?>
    <div id="tabs-container">
      <ul id="tabs-nav"></ul>
      <div id="tabs-content"></div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('gallery_tabs', 'gallery_tabs_shortcode');

/* Google Analytic */
add_action('wp_head','my_analytics', 20);
function my_analytics() {
?>
<!-- Google tag (gtag.js) | cocacbc.com -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-42BPFHETDR"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-42BPFHETDR');
</script>

<?php
}
