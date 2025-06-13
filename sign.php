<!DOCTYPE html>
<html lang="en">
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign Up</title>
  <link rel="stylesheet" href="style.css">
  <script src="https://kit.fontawesome.com/5e4c3f328d.js" crossorigin="anonymous"></script>
  <style>
    * {
      margin: 0;
      padding: 0;
      font-family: 'Poppins', sans-serif;
      box-sizing: border-box;
    }

    .container {
      width: 100%;
      height: 100vh;
      background-image: linear-gradient(rgba(0, 0, 50, 0.8), rgba(0, 0, 50, 0.8)), url(pic.jpg);
      background-position: center;
      background-size: cover;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .form-box {
      width: 90%;
      max-width: 450px;
      background: #fff;
      padding: 50px 60px 70px;
      text-align: center;
      border-radius: 8px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    }

    .form-box h1 {
      font-size: 30px;
      margin-bottom: 20px;
      color: #3c00a0;
      position: relative;
    }

    .form-box h1::after {
      content: '';
      width: 30px;
      height: 4px;
      border-radius: 3px;
      background: #3c00a0;
      position: absolute;
      bottom: -12px;
      left: 50%;
      transform: translateX(-50%);
    }

    .input-field {
      background: #eaeaea;
      margin: 10px 0;
      border-radius: 3px;
      display: flex;
      align-items: center;
      overflow: hidden;
      padding: 10px;
    }

    input, select {
      width: 100%;
      background: transparent;
      border: 0;
      outline: 0;
      padding: 10px;
    }

    .input-field i {
      margin-left: 10px;
      color: #999;
    }

    .btn-field {
      width: 100%;
      display: flex;
      justify-content: space-between;
      margin-top: 10px;
    }

    .btn-field button {
      flex-basis: 48%;
      background: #3c00a0;
      color: #fff;
      height: 40px;
      border-radius: 20px;
      border: 0;
      outline: 0;
      cursor: pointer;
      transition: background 0.3s;
    }

    .btn-field button.disable {
      background: #eaeaea;
      color: #555;
    }

    .forgot-password {
      text-align: center;
      margin-top: 10px;
    }

    .forgot-password a {
      text-decoration: none;
      color: #3c00a0;
      font-size: 14px;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="form-box">
      <h1>Sign Up</h1>
      <form id="f" action="registration.php" method="post">
        <div class="input-field">
          <i class="fa-solid fa-user"></i>
          <input type="text" id="Name" name="Name" placeholder="Name">
        </div>
        <div class="input-field">
          <i class="fa-solid fa-envelope"></i>
          <input type="email" id="Email" name="Email" placeholder="Email">
        </div>
        <div class="input-field">
          <i class="fa-solid fa-phone"></i>
          <input type="text" id="Phone_Number" name="Phone_Number" placeholder="Phone Number">
        </div>
        <div class="input-field">
          <i class="fa-solid fa-cake-candles"></i>
          <input type="number" id="Age" name="Age" placeholder="Age">
        </div>
        <div class="input-field">
          <i class="fa-solid fa-venus-mars"></i>
          <select name="Select_Gender">
            <option disabled selected>Select Gender</option>
            <option>Male</option>
            <option>Female</option>
            <option>Other</option>
          </select>
        </div>
        <div class="input-field">
          <i class="fa-solid fa-lock"></i>
          <input type="password" id="Password" name="Password" placeholder="Password">
        </div>
        <div class="input-field">
          <i class="fa-solid fa-lock"></i>
          <input type="password" id="Confirm_Password" name="Confirm_Password" placeholder="Confirm Password">
        </div>
		<span id="passwordError" ></span>
        <div class="forgot-password">
          <a href="forget.php">Forgot Password?</a>
        </div>
        <div class="btn-field">
          <button type="submit" id="submitBtn">Sign Up</button>
          <button type="button" id="signinBtn">Sign In</button>
        </div>
      </form>
    </div>
  </div>

<script>
        document.getElementById('f').addEventListener('input', function () {
            validateForm();
        });

        function validateForm() {
            const name = document.getElementById('Name').value;
            const email = document.getElementById('Email').value;
            const phone_number = document.getElementById('Phone_Number').value;
            const age = document.getElementById('Age');
            const password = document.getElementById('Password').value;
            const confirmPassword = document.getElementById('Confirm_Password').value;
			const errorElement = document.getElementById('passwordError');

            let isValid = true;

            if (!name || !password || !confirmPassword) {
                isValid = false;
            }

            if (password !== confirmPassword) {
                errorElement.textContent = 'Passwords do not match';
                errorElement.classList.remove('success');
                errorElement.classList.add('error');
                isValid = false;
            } else {
                errorElement.textContent = 'Passwords match';
                errorElement.classList.remove('error');
                errorElement.classList.add('success');
            }

            if (isValid) {
                submitBtn.classList.add('enabled');
                submitBtn.disabled = false;
            } else {
                submitBtn.classList.remove('enabled');
                submitBtn.disabled = true;
            }
        }
    </script>

  <script>
    document.getElementById("signinBtn").onclick = function () {
      window.location.href = "signin.php";
    };
  </script>
</body>
</html>