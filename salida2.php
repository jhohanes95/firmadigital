<!DOCTYPE html>
<html>
<head>
    <title>Firmar Archivo PDF</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #cbcbcb;
        }
        .container {
            max-width: 750px;
            margin: auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.2);
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
        h1 {
            margin-bottom: 20px;
            text-align: center;
            font-family: 'Arial Black', sans-serif;
            color: #c51f33;
        }
        form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        label {
            margin-bottom: 10px;
        }
        input[type="file"] {
            margin-bottom: 20px;
        }
        input[type="submit"] {
            background-color: #007bff;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
        img {
            max-width: 100%;
            height: auto;
        }
        #progressContainer {
            display: none;
            width: 100%;
            height: 20px;
            background-color: #f0f0f0;
            border-radius: 5px;
        }
        #progressBar {
            width: 0;
            height: 100%;
            background-color: #007bff;
            border-radius: 5px;
            text-align: center;
            color: white;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Firmar Archivo PDF</h1>
    <form id="uploadForm" action="" method="post" enctype="multipart/form-data">
        <label for="pdfFile">Selecciona un archivo PDF:</label>
        <input type="file" name="pdfFile" id="pdfFile" accept=".pdf" required>
        <br>
        <input type="submit" name="submit" value="Procesar">
    </form>
    
    <div id="progressContainer">
        <div id="progressBar">0%</div>
    </div>
<?php
function generateProgressBarScript() {
        return "
        <script>
            const form = document.getElementById('uploadForm');
            const progressBar = document.getElementById('progressBar');
            const progressContainer = document.getElementById('progressContainer');

            form.addEventListener('submit', function(event) {
                event.preventDefault();
                progressContainer.style.display = 'block';
                form.querySelector('input[type=\"submit\"]').disabled = true;

                let progress = 0;
                const interval = setInterval(function() {
                    progress += 10;
                    progressBar.style.width = progress + '%';
                    progressBar.textContent = progress + '%';

                    if (progress >= 100) {
                        clearInterval(interval);
                        progressBar.textContent = 'Procesamiento completado';
                        // Agregar la lógica para redirigir o mostrar mensaje de éxito aquí
                    }
                }, 1000);
            });
        </script>";
    }

    echo generateProgressBarScript();
?>
<?php
    

    if (isset($_POST['submit'])) {
        $uploadedFile = $_FILES['pdfFile'];

        if ($uploadedFile['error'] === UPLOAD_ERR_OK) {
            $fileExtension = pathinfo($uploadedFile['name'], PATHINFO_EXTENSION);

            if (strtolower($fileExtension) === 'pdf') {
                $nombre_salida = date('Y-m-d_H-i-s') . '.pdf';
                $comando = 'java -jar "AndesSCDFirmador.jar" --metodofirma estampa --formatofirma pdf --entrada "' . $uploadedFile['tmp_name'] . '" --salida ' . $nombre_salida . ' --formatoentrada archivo --formatosalida archivo --aplicatsa true --tsausuario ducas --tsapass Gh8cR73d --tsaurl https://tsa.andesscd.com.co/ --test true';
                exec($comando, $salida, $retorno);

                echo "<h2>Resultado del Proceso</h2>";
                echo "<pre>";
    
                if (isset($json_output['estado'])) {
                    if ($json_output['estado'] === '0') {
                        echo "<h1>DESCARGA AUTOMÁTICA EXITOSA</h1>";
                        echo "<p style='color:#c51f33';><b>Revise su carpeta de descargas</b><p>";
                        
                        session_start();
                        $miVariable = $nombre_salida;
                        $_SESSION['variable'] = $miVariable;
                        echo "<script>window.location.href = 'descargar.php';</script>";
                        exit;
                    } else {
                        echo "Error al ejecutar el comando: $comando<br>";
                        print_r($salida);
                    }
                } else {
                    echo "No se encontró el valor de 'estado' en la salida JSON.<br>";
                }

                echo "</pre>";

            } else {
                echo "El archivo debe ser en formato PDF.";
            }
                
        } else {
            echo "Error al cargar el archivo. Código de error: " . $uploadedFile['error'];
        }	 
    }
?>
</div>
</body>
</html>
