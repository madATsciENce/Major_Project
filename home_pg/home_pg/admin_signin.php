<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Sign In</title>
    <link rel="stylesheet" href="style.css">
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
        max-width: 400px;
        background: #fff;
        padding: 50px 40px 60px;
        text-align: center;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    }

    .form-box h1 {
        font-size: 30px;
        margin-bottom: 30px;
        color: #3c00a0;
        position: relative;
    }

    .input-field {
        background: #eaeaea;
        margin: 15px 0;
        border-radius: 3px;
        display: flex;
        align-items: center;
        overflow: hidden;
        padding: 10px;
    }

    input {
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
        margin-top: 20px;
    }

    .btn-field button {
        width: 100%;
        background: #3c00a0;
        color: #fff;
        height: 40px;
        border-radius: 20px;
        border: 0;
        outline: 0;
        cursor: pointer;
        transition: background 0.3s;
    }

    .btn-field button:hover {
        background: #290073;
    }
    </style>
</head>

<body>
    <div class="container">
        <div class="form-box">
            <h1>Admin Sign In</h1>
            <form action="admin_north.php" method="post">
                <div class="input-field">
                    <i class="fa-solid fa-user-shield"></i>
                    <input type="text" name="Admin_Username" placeholder="Admin Username" required>
                </div>
                <div class="input-field">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" name="Admin_Password" placeholder="Password" required>
                </div>
                <div class="btn-field">
                    <button type="submit" name="admin_signin">Sign In</button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>