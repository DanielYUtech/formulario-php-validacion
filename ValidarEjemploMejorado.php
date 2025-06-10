<?php
// Iniciamos la sesión para poder usar el CAPTCHA
session_start();

// Generar el código CAPTCHA si no existe
if (!isset($_SESSION['captcha'])) {
    $n1 = rand(1, 10);
    $n2 = rand(1, 10);
    $_SESSION['captcha'] = $n1 + $n2;
    $_SESSION['captcha_question'] = "Cuanto es {$n1} + {$n2}?";
}


// Bloque para procesar el formulario cuando se envía (método POST)
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    // Array para almacenar los errores
    $errors = [];

    // 1. Sanear y validar el nombre
    $name = trim(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING));
    if (empty($name)) {
        $errors[] = "El campo Nombre es obligatorio.";
    } elseif (!preg_match("/^[a-zA-Z ]*$/", $name)) {
        $errors[] = "Nombre: Solo se permiten letras y espacios.";
    }

    // 2. Sanear y validar el email
    $email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
    if (empty($email)) {
        $errors[] = "El campo Email es obligatorio.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "El formato del email es inválido.";
    }
    
    // 3. Sanear y validar el teléfono
    $phone = trim(filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING));
    // Expresión regular para el formato 123-456-7890
    $phone_regex = "/^\d{3}-\d{3}-\d{4}$/";
    if (empty($phone)) {
        $errors[] = "El campo Teléfono es obligatorio.";
    } elseif (!preg_match($phone_regex, $phone)) {
        $errors[] = "Teléfono: El formato debe ser 123-456-7890.";
    }

    // 4. Validar el CAPTCHA
    $captcha_input = filter_input(INPUT_POST, 'captcha', FILTER_SANITIZE_NUMBER_INT);
    if (empty($captcha_input)) {
        $errors[] = "El campo CAPTCHA es obligatorio.";
    } elseif ($captcha_input != $_SESSION['captcha']) {
        $errors[] = "El resultado del CAPTCHA es incorrecto.";
    }


    // Comprobar si hay errores
    if (!empty($errors)) {
        // Si hay errores, mostrarlos en una lista
        echo "<b>Por favor, corrige los siguientes errores:</b><ul>";
        foreach ($errors as $error) {
            echo "<li>" . htmlspecialchars($error) . "</li>";
        }
        echo "</ul>";
    } else {
        // Si todo está correcto, procesar los datos
        echo "<b style='color: green;'>¡Formulario enviado con éxito!</b>";
        
        // Limpiar el CAPTCHA de la sesión para que se genere uno nuevo
        unset($_SESSION['captcha']);
        unset($_SESSION['captcha_question']);
    }
    
    // Detenemos el script para que no se muestre el HTML de abajo en la respuesta fetch
    exit();
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Validaciones Mejoradas con Fetch</title>
    <style>
        body { font-family: sans-serif; }
        #errors { border: 1px solid #ff0000; background-color: #ffecec; padding: 10px; margin-top: 15px; border-radius: 5px; color: #a94442; }
        #errors b { color: #a94442; }
        #errors ul { margin: 5px 0 0 15px; padding: 0; }
        b.success { color: green; }
    </style>
</head>
<body>

    <h2>Ejemplo de Validaciones Mejorado</h2>
    
    <form id="contactForm" action="ValidarEjemploMejorado.php" method="post" novalidate>
        Nombre: <input type="text" name="name" required><br><br>
        
        Email: <input type="email" name="email" required><br><br>
        
        Teléfono: <input type="text" name="phone" placeholder="123-456-7890" required><br><br>
        
        <b><?php echo $_SESSION['captcha_question']; ?></b>
        <input type="text" name="captcha" required><br><br>
        
        <input type="submit" name="submit" value="Enviar">
    </form>
    
    <div id="errors"></div>

    <script src="form-handler-mejorado.js" defer></script>
</body>
</html>