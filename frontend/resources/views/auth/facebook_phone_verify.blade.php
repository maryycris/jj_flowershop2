<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facebook Phone Verification - JJ Flowershop</title>
</head>
<body>
    <h2>Phone Number Verification Required</h2>
    <p>To complete your Facebook login, please verify your phone number.</p>
    <input type="text" id="phone" placeholder="+639123456789"><br>
    <div id="recaptcha-container"></div>
    <button onclick="sendOTP()">Send OTP</button><br><br>

    <input type="text" id="otp" placeholder="Enter OTP">
    <button onclick="verifyOTP()">Verify</button>

    <!-- Firebase JS SDKs (must be before your script) -->
    <script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-auth-compat.js"></script>
    <script>
      const firebaseConfig = {
        apiKey: "AIzaSyDbTD2wdaw4LvIWDo8JxzMJiY5eh-OwAw8",
        authDomain: "jj-flowershop-capstone-be022.firebaseapp.com",
        projectId: "jj-flowershop-capstone-be022",
        storageBucket: "jj-flowershop-capstone-be022.appspot.com",
        messagingSenderId: "437812731967",
        appId: "1:437812731967:web:2aaaf3a69e5cbdb4f7d88a",
        measurementId: "G-CMK4N92HSQ"
      };
      firebase.initializeApp(firebaseConfig);

      let confirmationResult = null;

      function sendOTP() {
        const phone = document.getElementById("phone").value;
        window.recaptchaVerifier = new firebase.auth.RecaptchaVerifier('recaptcha-container', {
          'size': 'normal',
          'callback': () => console.log("Recaptcha Verified")
        });

        firebase.auth().signInWithPhoneNumber(phone, window.recaptchaVerifier)
          .then((result) => {
            confirmationResult = result;
            alert("OTP sent!");
          })
          .catch((error) => {
            alert(error.message);
          });
      }

      function verifyOTP() {
        const code = document.getElementById("otp").value;
        confirmationResult.confirm(code).then((result) => {
          const user = result.user;
          // Send phone number to Laravel backend
          fetch("/facebook/verify-phone", {
            method: "POST",
            headers: {
              "Content-Type": "application/json",
              "Accept": "application/json",
              'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ phone: user.phoneNumber })
          })
          .then(res => res.json())
          .then(data => {
            alert("Phone verified and Facebook login complete!");
            window.location.href = "/dashboard";
          })
          .catch(err => alert("Laravel error: " + err));
        }).catch((error) => {
          alert("Incorrect OTP.");
        });
      }
    </script>
</body>
</html> 