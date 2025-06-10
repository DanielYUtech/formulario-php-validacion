document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('contactForm');
    const errorsDiv = document.getElementById('errors');

    form.addEventListener('submit', function(event) {
        // Detener el envío automático del formulario para validarlo primero
        event.preventDefault();
        
        // Limpiar errores previos
        errorsDiv.innerHTML = '';
        errorsDiv.style.display = 'none';

        // --- VALIDACIÓN EN EL CLIENTE (JAVASCRIPT) ---
        const name = form.elements['name'].value.trim();
        const email = form.elements['email'].value.trim();
        const phone = form.elements['phone'].value.trim();
        const captcha = form.elements['captcha'].value.trim();
        
        let clientErrors = [];

        if (name === '') {
            clientErrors.push('El campo Nombre es obligatorio.');
        }
        
        if (email === '') {
            clientErrors.push('El campo Email es obligatorio.');
        }

        if (phone === '') {
            clientErrors.push('El campo Teléfono es obligatorio.');
        } else {
            // Expresión regular para validar el formato 123-456-7890
            const phoneRegex = /^\d{3}-\d{3}-\d{4}$/;
            if (!phoneRegex.test(phone)) {
                clientErrors.push('Teléfono: El formato debe ser 123-456-7890.');
            }
        }
        
        if (captcha === '') {
            clientErrors.push('El campo CAPTCHA es obligatorio.');
        }

        // Si hay errores en el cliente, los mostramos y no enviamos el formulario
        if (clientErrors.length > 0) {
            errorsDiv.style.display = 'block';
            let errorHtml = '<b>Por favor, corrige los siguientes errores:</b><ul>';
            clientErrors.forEach(error => {
                errorHtml += `<li>${error}</li>`;
            });
            errorHtml += '</ul>';
            errorsDiv.innerHTML = errorHtml;
            return; // Detiene la ejecución aquí
        }

        // Si la validación del cliente pasa, enviamos los datos al servidor con fetch
        const formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            // Mostramos la respuesta del servidor (errores o éxito) en el div
            errorsDiv.style.display = 'block';
            errorsDiv.innerHTML = data;
            
            // Si el formulario se envió con éxito, podemos resetearlo
            if (data.includes("éxito")) {
                form.reset();
                // Opcional: Recargar la página para obtener un nuevo CAPTCHA
                setTimeout(() => location.reload(), 2000);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            errorsDiv.style.display = 'block';
            errorsDiv.innerHTML = 'Ocurrió un error al contactar el servidor.';
        });
    });
});