<?php
session_start();
include "connect.php";
$client_id = clientID;
$client_secret = client_secret;
$redirect_uri = redirectUri;
$auth_url = "https://accounts.google.com/o/oauth2/v2/auth";
$token_url = "https://oauth2.googleapis.com/token";
$user_info_url = "https://www.googleapis.com/oauth2/v2/userinfo";
if (isset($_GET['code'])) {
    $code = $_GET['code'];

    $data = [
        'code' => $code,
        'client_id' => $client_id,
        'client_secret' => $client_secret,
        'redirect_uri' => $redirect_uri,
        'grant_type' => 'authorization_code'
    ];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $token_url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    $response_data = json_decode($response, true);
    if (isset($response_data['access_token'])) {
        $_SESSION['access_token'] = $response_data['access_token'];
        $access_token = $response_data['access_token'];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $user_info_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $access_token
        ]);
        $user_info = curl_exec($ch);
        curl_close($ch);
        $user_info_data = json_decode($user_info, true);

        $_SESSION['picture'] = $user_info_data['picture'];
        $_SESSION['name'] = $user_info_data['name'];
        $query = $conn->prepare("SELECT roles , email FROM users WHERE google_id = ?");
        $query->execute([$user_info_data['id']]);
        $user = $query->fetch();
        $_SESSION['roles'] = $user['roles'];
        
        if (!$user) {
            $query = $conn->prepare("INSERT INTO users (email, google_id, name, profile_picture) VALUES (?, ?, ?, ?)");
            $query->execute([
                $user_info_data['email'], 
                $user_info_data['id'], 
                $user_info_data['name'], 
                $user_info_data['picture']
            ]);
        }
        $_SESSION['email'] = $user_info_data['email'];
        header("Location: dashboard.php");
        exit();
    } else {
        echo "Error: Unable to fetch access token.";
    }
} else {
    $auth_url = $auth_url . "?response_type=code&client_id=" . urlencode($client_id) . "&redirect_uri=" . urlencode($redirect_uri) . "&scope=" . urlencode("email profile");
    header("Location: " . $auth_url);
    exit();
}
?>
