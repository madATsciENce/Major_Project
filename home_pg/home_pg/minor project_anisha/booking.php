<?php
// booking.php

// Define hotel data (could be moved to a database in real app)
$hotels = [
    "Leh Palace Hotel" => ["location" => "Leh", "rent" => 4500],
    "Snowland Hotel" => ["location" => "Leh", "rent" => 4000],
    "Lamayuru Hotel" => ["location" => "Lamayuru", "rent" => 3200],
    "Stok Palace Heritage Hotel" => ["location" => "Stok", "rent" => 7000],
    "Zanskar Valley Hotel" => ["location" => "Zanskar", "rent" => 4400],
];

// Get hotel name from query parameter
$hotelName = isset($_GET['hotel']) ? $_GET['hotel'] : null;

if (!$hotelName || !array_key_exists($hotelName, $hotels)) {
    echo "Invalid hotel selected.";
    exit;
}

$hotel = $hotels[$hotelName];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Booking - <?php echo htmlspecialchars($hotelName); ?></title>
    <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #eef2f3;
        padding: 20px;
    }

    .booking-container {
        max-width: 600px;
        margin: auto;
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    h2 {
        text-align: center;
    }

    label {
        display: block;
        margin-top: 15px;
    }

    input,
    select {
        width: 100%;
        padding: 8px;
        margin-top: 5px;
    }

    button {
        margin-top: 20px;
        background-color: #3498db;
        color: white;
        border: none;
        padding: 12px;
        width: 100%;
        cursor: pointer;
        font-size: 16px;
        border-radius: 4px;
    }

    button:hover {
        background-color: #5dade2;
    }
    </style>
</head>

<body>
    <div class="booking-container">
        <h2>Book Your Stay at <?php echo htmlspecialchars($hotelName); ?></h2>
        <p><strong>Location:</strong> <?php echo htmlspecialchars($hotel['location']); ?></p>
        <p><strong>Rent per night:</strong> ₹<?php echo number_format($hotel['rent']); ?></p>

        <form method="POST" action="process_booking.php">
            <input type="hidden" name="hotel" value="<?php echo htmlspecialchars($hotelName); ?>" />
            <label for="name">Your Name:</label>
            <input type="text" id="name" name="name" required />

            <label for="email">Email Address:</label>
            <input type="email" id="email" name="email" required />

            <label for="checkin">Check-in Date:</label>
            <input type="date" id="checkin" name="checkin" required />

            <label for="checkout">Check-out Date:</label>
            <input type="date" id="checkout" name="checkout" required />

            <label for="guests">Number of Guests:</label>
            <select id="guests" name="guests" required>
                <option value="1">1</option>
                <option value="2" selected>2</option>
                <option value="3">3</option>
                <option value="4">4</option>
            </select>

            <button type="submit">Proceed to Payment</button>
        </form>
    </div>
</body>

</html>