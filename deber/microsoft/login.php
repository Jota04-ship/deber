<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plataforma</title>
    <link rel="icon" href="https://sistemaseducaciononline.uta.edu.ec/pluginfile.php/1/theme_adaptable/favicon/1715289690/sistemas.png">
    <link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background: url('fondo.jpeg') no-repeat;
            background-size: cover;
            margin: 0;
            font-family: Arial, sans-serif;
        }

        .titulo {
            text-align: center;
            margin-bottom: 20px;
            background-color: white;
            padding: 1px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }

        .logo {
            width: 120px;
            height: 120px;
            background: url('logo1.jpg') no-repeat;
            background-size: contain;
            float: left;
            margin-right: 10px;
        }

        .contenedor-formulario {
            margin-top: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }

        .grupo-formulario input[type="text"],
        .grupo-formulario input[type="password"],
        .grupo-formulario input[type="submit"] {
            max-width: 250px;
            width: 100%;
            padding: 10px;
            margin: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        .grupo-formulario label {
            display: block;
            text-align: left;
        }

        .grupo-formulario-check {
            text-align: left;
            display: flex;
            align-items: center;
        }

        .grupo-formulario-check label {
            margin-bottom: 0;
            margin-left: 5px;
        }

        .grupo-formulario-mt-3 {
            margin-top: 20px;
            text-align: center;
        }

        .grupo-formulario-mt-3 button {
            display: inline-block;
            margin-top: 10px;
            padding: 10px 20px;
            background-color: #680909;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .grupo-formulario-mt-3 button:hover {
            background-color: #4f1212;
        }

        .btn-microsoft {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            color: #fff;
            background-color: #680909;
            padding: 10px 20px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .btn-microsoft img {
            width: 20px;
            margin-right: 10px;
            vertical-align: middle;
        }

        .btn-microsoft:hover {
            background-color: #4f1212;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-7">
                <div class="titulo">
                    <div class="logo"></div>
                    <h1 class="mt-4 mb-0">PLATAFORMA EDUCATIVA</h1>
                    <h1 class="mb-0">INSTITUCIONAL</h1>
                </div>
                <div class="contenedor-formulario">
                    <form action="procesar.php" method="post">
                        <div class="grupo-formulario">
                            <input type="text" class="Control" name="Usuario" placeholder="Usuario/Correo electrónico" required>
                        </div>
                        <div class="grupo-formulario">
                            <input type="password" class="Control" name="contraseña" placeholder="Contraseña" required>
                        </div>
                        <div class="grupo-formulario-check mt-3">
                            <?php
                            $captcha = generateRandomString(4);
                            echo "<label>$captcha</label><br>";
                            echo "<input type='text' name='captcha' required><br>";
                            function generateRandomString($length)
                            {
                                $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
                                $charactersLength = strlen($characters);
                                $randomString = '';
                                for ($i = 0; $i < $length; $i++) {
                                    $randomString .= $characters[rand(0, $charactersLength - 1)];
                                }
                                return $randomString;
                            }
                            ?>
                        </div>
                        <div class="grupo-formulario-mt-3">
                            <button type="submit" id="btnAcceder" name="btnAcceder">Acceder</button>
                        </div>
                    </form>
                    <div>
                        <label for="identifiquese">Identifíquese usando su cuenta en:</label>
                    </div>
                    <a href="https://login.microsoftonline.com/a988ccd4-00ed-4bf3-a4d1-b5661f44abdf/oauth2/v2.0/authorize?client_id=5c9ac2b8-b927-4ff0-8085-b930c9f0c331&response_type=code&redirect_uri=http://localhost/deber/microsoft/auth_redirect.php&scope=openid+profile+User.Read" class="btn-microsoft">
                        <img src="http://localhost/deber/microsoft/Microsoft.jpg" alt="Microsoft Office 365">
                        <span>Microsoft Office 365</span>
                    </a>

                </div>
            </div>
        </div>
    </div>
</body>

</html>