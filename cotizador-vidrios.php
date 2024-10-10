<?php
/**
 * Plugin Name: Cotizador de Vidrios
 * Description: Plugin para calcular el precio de vidrios y enviar los detalles por correo electrónico usando wp_mail.
 * Version: .1
 * Author: Datapro SpA
 */

// Encolar el script y el estilo del formulario

function cotizador_vidrios_enqueue_scripts() {
    // Encolar el archivo CSS personalizado
    wp_enqueue_style('cotizador-estilos', plugin_dir_url(__FILE__) . 'cotizador-estilos.css');

    // Encolar el archivo JS del formulario
    wp_enqueue_script('cotizador-form', plugin_dir_url(__FILE__) . 'cotizador-form.js', array('jquery'), null, true);

    // Localización para AJAX
    wp_localize_script('cotizador-form', 'cotizadorAjax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
    ));
}
add_action('wp_enqueue_scripts', 'cotizador_vidrios_enqueue_scripts');


// Shortcode para mostrar el formulario
function cotizador_vidrios_shortcode() {
    ob_start();
    ?>
    <div class="cotizador-container">
        <h1>Cálculo de Precio para Vidrios</h1>
        <form id="glassForm">
            <div>
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" required>
            </div>
            <div>
                <label for="apellido">Apellido:</label>
                <input type="text" id="apellido" required>
            </div>
            <div>
                <label for="telefono">Teléfono:</label>
                <input type="text" id="telefono" required>
            </div>
            <div>
                <label for="email">Correo Electrónico:</label>
                <input type="email" id="email" required>
            </div>
            <div>
                <label for="cristal1">Cristal 1:</label>
                <select id="cristal1">
                <option value="12866">Inc. 3</option>
                    <option value="16500">Inc. 4</option>
                    <option value="23921">Inc. 5</option>
                    <option value="39529">Inc. 6</option>
                    <option value="60590">Inc. 8</option>
                    <option value="121177">Inc. 10</option>
                    <option value="57181">Lam. 6</option>
                    <option value="95301">Lam. 8</option>
                    <option value="152477">Lam. 10</option>
                    <option value="80051">Acústico 6</option>
                    <option value="74592">Acústico 8</option>
                    <option value="129547">Lam Acu 10</option>
                    <option value="59045">Saten 4</option>
                    <option value="77554">Saten 5</option>
                    <option value="92445">Saten 6</option>
                    <option value="27485">Bronce 4</option>
                    <option value="39457">Bronce 5</option>
                    <option value="59475">Bronce 6</option>
                    <option value="27485">Semilla 4</option>
                    <option value="52000">Reflecta Float 4</option>
                    <option value="67000">Reflecta Float 6</option>
                    <option value="55952">Low-E 4</option>
                    <option value="59682">Low-E 5</option>
                    <option value="71990">Low-E 6</option>
                    <option value="3200">Metro Lineal</option>
                </select>
            </div>
            <div>
                <label for="cristal2">Cristal 2:</label>
                <select id="cristal2">
                <option value="12866">Inc. 3</option>
                    <option value="16500">Inc. 4</option>
                    <option value="23921">Inc. 5</option>
                    <option value="39529">Inc. 6</option>
                    <option value="60590">Inc. 8</option>
                    <option value="121177">Inc. 10</option>
                    <option value="57181">Lam. 6</option>
                    <option value="95301">Lam. 8</option>
                    <option value="152477">Lam. 10</option>
                    <option value="80051">Acústico 6</option>
                    <option value="74592">Acústico 8</option>
                    <option value="129547">Lam Acu 10</option>
                    <option value="59045">Saten 4</option>
                    <option value="77554">Saten 5</option>
                    <option value="92445">Saten 6</option>
                    <option value="27485">Bronce 4</option>
                    <option value="39457">Bronce 5</option>
                    <option value="59475">Bronce 6</option>
                    <option value="27485">Semilla 4</option>
                    <option value="52000">Reflecta Float 4</option>
                    <option value="67000">Reflecta Float 6</option>
                    <option value="55952">Low-E 4</option>
                    <option value="59682">Low-E 5</option>
                    <option value="71990">Low-E 6</option>
                    <option value="3200">Metro Lineal</option>
                </select>
            </div>
            <div>
                <label for="cantidad">Cantidad:</label>
                <input type="number" id="cantidad" value="1" min="1" required>
            </div>
            <div>
                <label for="alto">Alto (mm):</label>
                <input type="number" id="alto" value="500" required>
            </div>
            <div>
                <label for="ancho">Ancho (mm):</label>
                <input type="number" id="ancho" value="1000" required>
            </div>

            <button type="button" onclick="calcularPrecio()" class="btn-calcular">Calcular Precio</button>
            <button type="button" id="enviarBtn" disabled class="btn-enviar" onclick="enviarCotizacion()">Enviar Cotización</button>
        </form>

        <h2 class="precio-total">Precio Total: <span id="precioTotal">0</span></h2>
        <p id="error" class="error-message"></p>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('cotizador_vidrios', 'cotizador_vidrios_shortcode');


// Manejar la solicitud AJAX para enviar los datos por correo electrónico
add_action('wp_ajax_nopriv_enviar_cotizacion', 'cotizador_vidrios_enviar_correo');
add_action('wp_ajax_enviar_cotizacion', 'cotizador_vidrios_enviar_correo');

function cotizador_vidrios_enviar_correo() {
    // Recibir los datos del formulario
    $nombre = sanitize_text_field($_POST['nombre']);
    $apellido = sanitize_text_field($_POST['apellido']);
    $telefono = sanitize_text_field($_POST['telefono']);
    $email = sanitize_email($_POST['email']);
    $cristal1 = sanitize_text_field($_POST['cristal1']);
    $cristal2 = sanitize_text_field($_POST['cristal2']);
    $cantidad = intval($_POST['cantidad']);
    $alto = intval($_POST['alto']);
    $ancho = intval($_POST['ancho']);
    $precioTotal = sanitize_text_field($_POST['precioTotal']);

    // Preparar el mensaje del correo
    $mensaje = "Nombre: $nombre $apellido\n";
    $mensaje .= "Teléfono: $telefono\n";
    $mensaje .= "Correo Electrónico: $email\n";
    $mensaje .= "Cristal 1: $cristal1\n";
    $mensaje .= "Cristal 2: $cristal2\n";
    $mensaje .= "Cantidad: $cantidad\n";
    $mensaje .= "Dimensiones: $alto x $ancho mm\n";
    $mensaje .= "Precio Total: $precioTotal\n";

    // Enviar el correo al administrador del sitio
    $admin_email = get_option('admin_email');  // Obtén el correo del administrador
    // Enviar el correo al administrador y al usuario
    $enviado = wp_mail(array($admin_email, $email), 'Cotización de Vidrio', $mensaje);


    if ($enviado) {
        wp_send_json_success('Correo enviado correctamente.');
    } else {
        wp_send_json_error('Hubo un error al enviar el correo.');
    }

    wp_die();
}
