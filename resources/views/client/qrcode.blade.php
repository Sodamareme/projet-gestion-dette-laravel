<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carte de Fidélité</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background: #f0f4f8; /* Light grayish-blue background */
            font-family: Arial, sans-serif;
        }

        .card {
            text-align: center;
            background: #ffffff;
            padding: 20px;
            border-radius: 15px;
            width: 320px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border: 1px solid #dce4e6; /* Light border color */
            position: relative; /* For pseudo-elements */
            overflow: hidden; /* To ensure decorations don't overflow */
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to bottom right, #fffae6, #d0f0c0); /* Gradient background */
            opacity: 0.2; /* Light opacity for background effect */
            z-index: 0; /* Behind content */
        }

        .card h3 {
            margin: 10px 0;
            font-size: 22px;
            color: #333; /* Dark text color */
            z-index: 1; /* Above pseudo-elements */
        }

        .card img {
            border-radius: 50%;
            width: 120px;
            height: 120px;
            object-fit: cover;
            border: 3px solid #00796b; /* Accent border color */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Shadow for image */
            z-index: 1; /* Above pseudo-elements */
        }

        .card img.qr-code {
            width: 160px;
            margin-top: 20px;
            border: 3px solid #00796b; /* Border color for QR code */
            padding: 5px;
            border-radius: 10px; /* Rounded corners for QR code */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Shadow for QR code */
            z-index: 1; /* Above pseudo-elements */
        }

        .card::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: -10px;
            width: 20px;
            height: 20px;
            background: #00796b;
            border-radius: 50%;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            z-index: 1; /* Above pseudo-elements */
        }
    </style>
</head>
<body>
    <div class="card">
        <h3>Carte de Fidélité</h3>
    <p>Name: {{ $client->nom }} {{ $client->prenom }}</p>
    <p>Phone: {{ $client->telephone }}</p>
        <img src="{{ $client->photo }}" alt="Client Photo">
        <h3>{{ $client->surnom }}</h3>
        <img src="{{ $qr_code_url }}" alt="QR Code" class="qr-code">
    </div>
</body>
</html>
