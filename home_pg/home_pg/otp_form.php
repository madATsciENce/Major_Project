<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>OTP Verification</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        padding: 20px;
        max-width: 400px;
        margin: auto;
    }

    form {
        display: flex;
        flex-direction: column;
    }

    label {
        margin-top: 10px;
        font-weight: bold;
    }

    input {
        padding: 8px;
        margin-top: 5px;
        font-size: 1em;
    }

    button {
        margin-top: 20px;
        padding: 10px;
        background-color: #3c00a0;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 1em;
    }

    button:hover {
        background-color: #290073;
    }
    </style>
</head>

<body>
    <h1>OTP Verification</h1>
    <form method="post" action="verify_otp.php">
        <label for="otp">Enter OTP:</label>
        <input type="text" id="otp" name="otp" pattern="\d{6}" maxlength="6" required />
        <button type="submit">Verify OTP</button>
    </form>
</body>

</html>