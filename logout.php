<?php
session_start();

function revokeToken($token) {
    $url = 'https://oauth2.googleapis.com/revoke';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['token' => $token]));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return $httpCode === 200;
}
if (isset($_SESSION['access_token'])) {
    $token = $_SESSION['access_token'];
    if (revokeToken($token)) {
        echo "Token revoked successfully.";
    } else {
        echo "Failed to revoke token.";
    }
}

session_unset();
session_destroy();
header("Location: index.php");
exit;
?>
