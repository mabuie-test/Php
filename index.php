<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Minha Página PHP</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e6f2ff;
            color: #003366;
            padding: 40px;
            text-align: center;
        }

        .caixa {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px #cccccc;
            display: inline-block;
        }

        h1 {
            color: #004080;
        }

        p {
            font-size: 18px;
        }

        .hora {
            margin-top: 20px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="caixa">
        <h1>Bem-vindo à minha página PHP!</h1>
        <p>Esta é uma página simples hospedada num servidor com suporte a PHP.</p>
        <div class="hora">
            <?php
                date_default_timezone_set('Africa/Maputo');
                echo "Data e hora actuais: " . date("d/m/Y H:i:s");
            ?>
        </div>
    </div>
</body>
</html>
