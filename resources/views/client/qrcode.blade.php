<!-- resources/views/client/qrcode.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carte de fidélité</title>
    <style>
        .card {
            text-align: center;
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            padding: 20px;
            border-radius: 10px;
            width: 300px;
            margin: auto;
        }
        .card img {
            border-radius: 50%;
            width: 100px;
            height: 100px;
            object-fit: cover;
        }
        .card h3 {
            margin: 10px 0;
            font-size: 18px;
        }
        .card img.qr-code {
            width: 150px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="card">
        <h3>Carte de fidélité</h3>
        <img src="{{ $client->photo }}" alt="Client Photo">
        <h3>{{ $client->surnom }}</h3>
        <img src="{{ $qr_code_path }}" alt="QR Code" class="qr-code">
    </div>
</body>
</html>
