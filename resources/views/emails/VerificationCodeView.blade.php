<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>One-time verification code</title>
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Open Sans', sans-serif;
            background-color: #333;
            color: #fff;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 200vh;
        }
        .container {
            text-align: center;
            background: linear-gradient(rgba(1, 30, 76, 1), #000000);
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0px 0px 20px rgba(0,0.5,0.5,0.2);
        }
        h1 {
            color: #fff;
            font-size: 48px;
            margin-bottom: 20px;
        }
        h2 {
            color: #fff;
            margin-bottom: 10px;
            font-size: 48px;
        }
        p {
            color: #fff;
            margin-bottom: 5px;
        }
        a {
            display: inline-block;
            color: #000000;
            background-color: rgba(0, 0, 0, 0.5);
            padding: 15px 30px;
            margin: 20px 0;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
        }
        .code {
            letter-spacing: 8px;

        }
    </style>
</head>
<body>
<div class="container">
    <p>Por favor ingrese este código en la aplicación.</p>
    <p>Este código es válido por 5 minutos y es de uso único.</p>
    <a><h1 class="code" >{{$code}}</h1></a>
</div>
</body>
</html>
