<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>OTP Verification</title>
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.css"
      integrity="sha384-OFO98I1/jj4hVZaH0rUbfCbRHLkkTyv4mxzZjV9p/F9qBuJpb+42mxNU2VGsdQJ"
      crossorigin="anonymous"
    />
    <style>

      body {
        background-color: #DEF5E5;
        font-family: Arial, sans-serif;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        flex-direction: column;
        margin: 0;
      }
      h2 {
        color: #333;
      }
      form {
        background-color: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        max-width: 350px;
        margin-bottom: 10px;
      }
      input {
        width: 100%;
        padding: 10px;
        margin: 10px 0;
        border-radius: 4px;
        border: 1px solid #ddd;
      }
      button {
        width: 100%;
        padding: 10px;
        background-color: #4caf50;
        color: #fff;
        border: none;
        border-radius: 4px;
        cursor: pointer;
      }
      button:hover {
        background-color: #45a049;
      }
      #timer {
        margin-top: 10px;
        color: red;
        font-weight: bold;
      }
      .container{
        text-align: center;
        width: 90%;
      }
      .requeotp{
        margin: 0 auto;
        width: 350px;
      }
    </style>
  </head>
  <body>
  <div class="container">
  <div class="requeotp">
   <h2>Request OTP</h2>
    <form id="email-form">
      <input
        type="email"
        id="email-input"
        placeholder="Enter your email"
        required
      />
      <button type="button" onclick="requestOTP()">Send OTP</button>
    </form>
   </div>

    <div id="otp-section" style="display: none">
      <h2>Enter OTP</h2>
      <form id="otp-form">
        <input type="text" id="otp-input" placeholder="Enter OTP" required />
        <button type="button" onclick="verifyOTP()">Verify OTP</button>
      </form>
      <p id="timer"></p>
    </div>
  </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script>
      let countdown; 

      function requestOTP() {
        const email = document.getElementById("email-input").value;
        swal({
          title: "Please wait...",
          text: "Generating OTP, please wait a moment.",
          icon: "info",
          buttons: false,
          closeOnClickOutside: false,
          closeOnEsc: false,
        });

        var xhr = new XMLHttpRequest();
        xhr.open("POST", "smtp.php", true);
        xhr.setRequestHeader("Content-Type", "application/json");
        xhr.onreadystatechange = function() {
          if (xhr.readyState === 4 && xhr.status === 200) {
            var data = JSON.parse(xhr.responseText);
            swal.close(); 
            if (data.success) {
              swal(
                "OTP Sent!",
                data.message || "OTP has been sent to your email.",
                "success"
              );
              document.getElementById("otp-section").style.display = "block";
              startCountdown();
            } else {
              swal("Error", data.message || "Failed to send OTP.", "error");
            }
          }
        };
        xhr.send(JSON.stringify({ email: email }));
      }

      function startCountdown() {
        let timeLeft = 60;
        let timerElement = document.getElementById("timer");
        timerElement.textContent = `Time left: ${timeLeft} seconds`;
        countdown = setInterval(() => {
          timeLeft--;
          timerElement.textContent = `Time left: ${timeLeft} seconds`;

          if (timeLeft <= 0) {
            clearInterval(countdown);
            timerElement.textContent = "OTP has expired.";
            swal(
              "Expired",
              "Your OTP has expired. Please request a new one.",
              "warning"
            );
          }
        }, 1000);
      }

      function verifyOTP() {
        const otp = document.getElementById("otp-input").value;

        var xhr = new XMLHttpRequest();
        xhr.open("POST", "verify.php", true);
        xhr.setRequestHeader("Content-Type", "application/json");
        xhr.onreadystatechange = function() {
          if (xhr.readyState === 4 && xhr.status === 200) {
            var data = JSON.parse(xhr.responseText);
            if (data.success) {
              document.getElementById("otp-section").style.display = "none";
              clearInterval(countdown); 
              swal("Success", "OTP is valid!", "success").then((res) =>{
                location.href = "Forgot.php";
              })
            } else {
              swal("Error", data.message || "Invalid OTP.", "error");
            }
          }
        };
        xhr.send(JSON.stringify({ otp: otp }));
      }
    </script>
  </body>
</html>
