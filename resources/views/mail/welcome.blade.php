<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bem-vindo ao Fasebuks</title>
    <style>
        /* Estilos de exemplo, personalize conforme necessário */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #333;
        }

        p {
            color: #666;
            margin-bottom: 20px;
        }

        .btn {
            display: inline-block;
            background-color: #007bff;
            color: #fff;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Bem-vindo ao DisparaZap, {{$user->name}}!</h1>
        <p>Olá,</p>
        <p>Ficamos muito felizes em tê-lo como parte da nossa comunidade. O DisparaZap é uma plataforma onde você pode
            enviar mensagens utilizando multiplas conexões do WhatsApp e muito mais!</p>
        <p>Clique no botão abaixo para começar a explorar:</p>
        <a href="https://disparazap.joserafael.dev.br/" class="btn">Explorar o DisparaZap</a>
        <p>Se você tiver alguma dúvida ou precisar de ajuda, não hesite em nos contatar.</p>
        <p>Obrigado,<br>Equipe DisparaZap</p>
    </div>
</body>

</html>