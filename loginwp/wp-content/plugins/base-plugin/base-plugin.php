<?php
/**
 * Plugin Name: COCAC Base Plugin
 * Description: Plantilla base para el desarrollo de nuevos plugins.
 * Version: 1.0.0
 * Author: Ecolohosting
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Código a ejecutar al activar el plugin.
 */
function cocac_base_plugin_activate() {
    // Aquí puede ir la lógica de activación.
}
register_activation_hook( __FILE__, 'cocac_base_plugin_activate' );

/**
 * Código a ejecutar al desactivar el plugin.
 */
function cocac_base_plugin_deactivate() {
    // Aquí puede ir la lógica de desactivación.
}
register_deactivation_hook( __FILE__, 'cocac_base_plugin_deactivate' );

/**
 * Inicializar funcionalidades principales del plugin.
 */
function cocac_base_plugin_init() {
    // Código principal del plugin.
}
add_action( 'init', 'cocac_base_plugin_init' );

