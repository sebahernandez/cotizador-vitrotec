let precioFormateado;

function calcularPrecio() {
    const cristal1 = document.getElementById('cristal1').selectedOptions[0].text;
    const cristal2 = document.getElementById('cristal2').selectedOptions[0].text;
    const cantidad = parseInt(document.getElementById('cantidad').value);
    const alto = parseInt(document.getElementById('alto').value);
    const ancho = parseInt(document.getElementById('ancho').value);
    const precioCristal1 = parseFloat(document.getElementById('cristal1').value);
    const precioCristal2 = parseFloat(document.getElementById('cristal2').value);

    const area = (alto * ancho) / 1000000;
    const perimetro = (alto + ancho) / 1000;
    const factorFijo = 3200;
    const precioTotal = (area * precioCristal1) + (area * precioCristal2) + (perimetro * 2 * factorFijo);

    const formatter = new Intl.NumberFormat('es-CL', { style: 'currency', currency: 'CLP', minimumFractionDigits: 0 });
    precioFormateado = formatter.format(precioTotal);

    document.getElementById('precioTotal').innerText = precioFormateado;
    document.getElementById('enviarBtn').disabled = false; // Habilitar el botón de "Enviar Cotización"
}

function enviarCotizacion() {
    const nombre = document.getElementById('nombre').value;
    const apellido = document.getElementById('apellido').value;
    const telefono = document.getElementById('telefono').value;
    const email = document.getElementById('email').value;
    const cristal1 = document.getElementById('cristal1').selectedOptions[0].text;
    const cristal2 = document.getElementById('cristal2').selectedOptions[0].text;
    const cantidad = parseInt(document.getElementById('cantidad').value);
    const alto = parseInt(document.getElementById('alto').value);
    const ancho = parseInt(document.getElementById('ancho').value);

    jQuery.ajax({
        url: cotizadorAjax.ajax_url,
        method: 'POST',
        data: {
            action: 'enviar_cotizacion',
            nombre: nombre,
            apellido: apellido,
            telefono: telefono,
            email: email,
            cristal1: cristal1,
            cristal2: cristal2,
            cantidad: cantidad,
            alto: alto,
            ancho: ancho,
            precioTotal: precioFormateado,
        },
        success: function (response) {
            alert('Correo enviado correctamente.');
            document.getElementById('enviarBtn').disabled = true; // Deshabilitar el botón de "Enviar Cotización" después de enviar
        },
        error: function (response) {
            alert('Hubo un error al enviar el correo.');
        },
    });
}
