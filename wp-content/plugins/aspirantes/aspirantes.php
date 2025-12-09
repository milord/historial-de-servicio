<?php
/**
 * Plugin Name: Aspirantes
 * Description: Intake form for applicants via shortcode [aspirantes_form]. Stores submissions as a custom post type for follow-up.
 * Version: 0.1.0
 * Author: CETis 112
 * Text Domain: aspirantes
 */

if (!defined('ABSPATH')) {
    exit;
}

const ASP_POST_TYPE = 'aspirante';
const ASP_META_PREFIX = '_asp_';

/**
 * Register the aspirante custom post type.
 */
function asp_register_post_type(): void
{
    $labels = [
        'name' => 'Aspirantes',
        'singular_name' => 'Aspirante',
        'add_new_item' => 'Añadir aspirante',
        'edit_item' => 'Editar aspirante',
        'new_item' => 'Nuevo aspirante',
        'view_item' => 'Ver aspirante',
        'search_items' => 'Buscar aspirantes',
        'not_found' => 'No se encontraron aspirantes',
        'menu_name' => 'Aspirantes',
    ];

    $args = [
        'labels' => $labels,
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_position' => 22,
        'menu_icon' => 'dashicons-id',
        'supports' => ['title', 'editor'],
        'has_archive' => false,
        'rewrite' => false,
    ];

    register_post_type(ASP_POST_TYPE, $args);
}
add_action('init', 'asp_register_post_type');

/**
 * Fields captured by the intake form.
 *
 * @return array<string, string>
 */
function asp_applicant_fields(): array
{
    return [
        'nombre' => 'Nombre completo',
        'correo' => 'Correo electrónico',
        'programa' => 'Programa de interés',
        'comentarios' => 'Comentarios',
    ];
}

/**
 * Shortcode handler: [aspirantes_form]
 */
function asp_render_form_shortcode(array $atts = []): string
{
    $atts = shortcode_atts(
        [
            'redirect' => '',
        ],
        $atts,
        'aspirantes_form'
    );

    $fields = asp_applicant_fields();
    $redirect = $atts['redirect'] ?: add_query_arg([], home_url($_SERVER['REQUEST_URI'] ?? ''));
    $status = sanitize_text_field(wp_unslash($_GET['asp_status'] ?? ''));
    $message = '';

    if ($status === 'success') {
        $message = '<div class="asp-alert asp-success">Gracias. Hemos recibido tu solicitud.</div>';
    } elseif ($status === 'error') {
        $message = '<div class="asp-alert asp-error">No se pudo enviar el formulario. Verifica los campos obligatorios.</div>';
    }

    ob_start();
    ?>
    <div class="asp-form-wrapper">
        <?php echo $message; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <div class="asp-tabs" role="tablist">
            <button class="asp-tab active" type="button" data-tab="acceso" aria-selected="true">Generar Acceso</button>
            <button class="asp-tab" type="button" data-tab="continuar" aria-selected="false">Continuar con el registro</button>
            <button class="asp-tab" type="button" data-tab="tutoriales" aria-selected="false">Tutoriales</button>
        </div>

        <div class="asp-panel active" data-panel="acceso">
            <h3>Generar Acceso</h3>
            <p>Inicia tu registro creando un acceso. Aquí capturaremos tus datos básicos y te enviaremos las instrucciones para continuar.</p>
            <p><button type="button" class="asp-button">Comenzar</button></p>
        </div>

        <div class="asp-panel" data-panel="continuar">
            <h3>Continuar con el registro</h3>
            <p>Si ya generaste tu acceso, ingresa para completar o actualizar tu información.</p>
            <p><button type="button" class="asp-button asp-secondary">Ingresar</button></p>
        </div>

        <div class="asp-panel" data-panel="tutoriales">
            <h3>Tutoriales</h3>
            <p>Consulta guías rápidas y videos para completar tu registro sin problemas.</p>
            <ul class="asp-list">
                <li>Cómo crear tu acceso.</li>
                <li>Cómo completar tu información académica.</li>
                <li>Preguntas frecuentes de aspirantes.</li>
            </ul>
        </div>
    </div>
    <style>
        .asp-form-wrapper { max-width: 720px; margin: 16px auto; padding: 16px; background: #fff; border: 1px solid #ddd; }
        .asp-tabs { display: flex; gap: 6px; margin-bottom: 12px; }
        .asp-tab { border: 1px solid #ccc; background: #f8f8f8; padding: 10px 14px; cursor: pointer; font-weight: 600; }
        .asp-tab.active { background: #0073aa; color: #fff; border-color: #0073aa; }
        .asp-panel { display: none; border: 1px solid #eee; padding: 14px; border-radius: 2px; }
        .asp-panel.active { display: block; }
        .asp-panel h3 { margin-top: 0; }
        .asp-list { padding-left: 18px; }
        .asp-button { background: #0073aa; color: #fff; border: none; padding: 10px 16px; cursor: pointer; }
        .asp-button:hover { background: #005f8d; }
        .asp-button.asp-secondary { background: #444; }
        .asp-alert { margin-bottom: 12px; padding: 10px; border-radius: 2px; }
        .asp-success { background: #f0f8f0; border: 1px solid #8bc48b; color: #225522; }
        .asp-error { background: #fdf4f4; border: 1px solid #e99; color: #992222; }
    </style>
    <script>
        (function() {
            var tabs = document.querySelectorAll('.asp-tab');
            var panels = document.querySelectorAll('.asp-panel');
            function activate(name) {
                tabs.forEach(function(tab) {
                    var active = tab.dataset.tab === name;
                    tab.classList.toggle('active', active);
                    tab.setAttribute('aria-selected', active ? 'true' : 'false');
                });
                panels.forEach(function(panel) {
                    panel.classList.toggle('active', panel.dataset.panel === name);
                });
            }
            tabs.forEach(function(tab) {
                tab.addEventListener('click', function() {
                    activate(tab.dataset.tab);
                });
            });
        })();
    </script>
    <?php
    return (string) ob_get_clean();
}
add_shortcode('aspirantes_form', 'asp_render_form_shortcode');

/**
 * Handle intake form submissions.
 */
function asp_handle_submit_applicant(): void
{
    if (!isset($_POST['asp_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['asp_nonce'])), 'asp_submit_applicant')) {
        wp_die('Solicitud no válida.');
    }

    $redirect = isset($_POST['asp_redirect']) ? esc_url_raw(wp_unslash($_POST['asp_redirect'])) : home_url();
    $fields = asp_applicant_fields();

    $nombre = sanitize_text_field(wp_unslash($_POST['nombre'] ?? ''));
    $correo = sanitize_email(wp_unslash($_POST['correo'] ?? ''));
    $programa = sanitize_text_field(wp_unslash($_POST['programa'] ?? ''));
    $comentarios = sanitize_textarea_field(wp_unslash($_POST['comentarios'] ?? ''));

    if ($nombre === '' || $correo === '') {
        wp_safe_redirect(add_query_arg('asp_status', 'error', $redirect));
        exit;
    }

    $title = $nombre;
    $content_parts = [
        $fields['correo'] . ': ' . $correo,
        $fields['programa'] . ': ' . $programa,
        $fields['comentarios'] . ":\n" . $comentarios,
    ];

    $post_id = wp_insert_post([
        'post_type' => ASP_POST_TYPE,
        'post_title' => $title,
        'post_content' => implode("\n\n", array_filter($content_parts)),
        'post_status' => 'pending',
        'meta_input' => [
            ASP_META_PREFIX . 'correo' => $correo,
            ASP_META_PREFIX . 'programa' => $programa,
            ASP_META_PREFIX . 'comentarios' => $comentarios,
        ],
    ]);

    if (is_wp_error($post_id)) {
        wp_safe_redirect(add_query_arg('asp_status', 'error', $redirect));
        exit;
    }

    wp_safe_redirect(add_query_arg('asp_status', 'success', $redirect));
    exit;
}
add_action('admin_post_nopriv_asp_submit_applicant', 'asp_handle_submit_applicant');
add_action('admin_post_asp_submit_applicant', 'asp_handle_submit_applicant');
