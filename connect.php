<?php 
// config define
    define('host','localhost');
    define('user','chiangrai_santisuk');
    define('password','santisuk_iot');
    define('database','chiangrai_santisuk');
    define('clientID','405700642753-h0t89sp615q86g65jkn42dnrei3ftnno.apps.googleusercontent.com');
    define('tokentelegram','7690100860:AAGFuri8bhTRaWyV9Xr2BtFU13_yCgxWRt4');
    define('redirectUri','http://meo-mil.xyz/redirect.php');
    define('client_secret','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6');

  
  try {
      $conn = new PDO("mysql:host=". host .";dbname=". database . "", user, password);
      // ตั้งค่าการแสดงข้อผิดพลาด
      $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  } catch(PDOException $e) {
      die("เกิดข้อผิดพลาดกับเว็บ");
  }