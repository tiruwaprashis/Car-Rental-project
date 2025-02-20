<?php
session_start();
include('includes/config.php');
// Function to generate transaction ID
function generateTransactionID() {
    return strtoupper(uniqid('TRX-', true)); // Generates a unique transaction ID starting with "TRX-"
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone_code = $_POST['phone_code'];
    $phone = $_POST['phone'];
    $gender = $_POST['gender'];
    $district = $_POST['district'];
    $occupation = $_POST['occupation'];
    $nationality = $_POST['nationality'];
    $city = $_POST['city'];
    $remarks = $_POST['remarks'];

    // Generate transaction ID
    $transaction_id = generateTransactionID();

    // Store the transaction ID in the session
    $_SESSION['transaction_id'] = $transaction_id;

    // Create PDO connection
    try {
        // Prepare SQL statement
        $stmt = $dbh->prepare("INSERT INTO user_info (transaction_id, full_name, email, phone_code, phone, gender, district, occupation, nationality, city, remarks) 
        VALUES (:transaction_id, :full_name, :email, :phone_code, :phone, :gender, :district, :occupation, :nationality, :city, :remarks)");

        // Bind parameters
        $stmt->bindParam(':transaction_id', $transaction_id);
        $stmt->bindParam(':full_name', $full_name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone_code', $phone_code);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':gender', $gender);
        $stmt->bindParam(':district', $district);
        $stmt->bindParam(':occupation', $occupation);
        $stmt->bindParam(':nationality', $nationality);
        $stmt->bindParam(':city', $city);
        $stmt->bindParam(':remarks', $remarks);

         // Execute the query
         if ($stmt->execute()) {
            echo '<script>alert("Data inserted successfully! Transaction ID: ' . $transaction_id . ' Now Click To Next Button");</script>';
        } else {
            echo '<script>alert("Failed to insert data.");</script>';
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>



<!-- janaki -->


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Information</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
  /* General Styles */
  
    <style>
        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            background: linear-gradient(135deg,rgb(10, 241, 87),rgb(162, 231, 33));
            padding: 10px;
            flex-direction: column;
        }

        .container {
            width: 100%;
            max-width: 900px;
            background: #fff;
            padding: 30px;
 
            font-weight: bold;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        h2 {                                   
            color: #1976d2;
            margin-bottom: 20px;
            text-align: center;
        }

        /* Marquee Styles */
        .marquee-container {
            width: 100%;
            background-color:blue;
            color: white;
            padding: 20px;
            font-size: 20px;
            text-align: center;
            margin-bottom: 10px;
            font-weight: bold;
            font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
        }
        .marquee-container marquee {
    font-weight: bold;
    color: #ffffff;
}

        /* Form Styles */
        .form-group {
            margin-bottom: 15px;
            display: flex;
            flex-direction: column;
        }

        .input-field {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            transition: border-color 0.3s;
        }

        .input-field:focus {
            border-color: #007bff;
            outline: none;
        }

        /* Phone Input Group */
        .phone-group {
            display: flex;
            gap: 10px;
        }

        .phone-code {
            width: 30%;
        }

        .phone-number {
            flex-grow: 1;
        }

        /* Gender Group */
        .gender-group {
            display: flex;
            justify-content: space-between;
        }

        .gender-group label {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        /* Buttons */
        .button {
    padding: 14px 20px;
    background-color: #4CAF50;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 18px;
    transition: background-color 0.3s;
    margin-top: 20px;
} 

.button-container {
    display: flex;
    justify-content: center;
    gap: 20px; /* Space between buttons */
    margin-top: 20px; /* Optional, adds space from elements above */
}

/* .button {
    padding: 10px 20px;
    font-size: 16px;
    cursor: pointer;
    border: none;
    border-radius: 5px;
    transition: background-color 0.3s ease, transform 0.2s ease;
} */

.submit-btn {
    background-color: #4CAF50; /* Green for Submit */
    color: white;
}

.submit-btn:hover {
    background-color: #45a049; /* Darker green on hover */
    transform: scale(1.05); /* Slightly increase size on hover */
}

.next-btn {
    background-color: #008CBA; /* Blue for Next */
    color: white;
}

.next-btn:hover {
    background-color: #007bb5; /* Darker blue on hover */
    transform: scale(1.05); /* Slightly increase size on hover */
}


.button:hover {
    background-color: #45a049;
}

.error {
    color: #e53935;
    margin-top: 10px;
    text-align: center;
}

/* Responsive Design */
@media (max-width: 768px) {
    .form-group {
        flex-direction: column;
    }

    .form-group .input-field {
        width: 100%;
    }

    .phone-group {
        flex-direction: column;
    }

    .phone-code,
    .phone-number {
        width: 100%;
    }

    .gender-group {
        flex-direction: column;
        align-items: flex-start;
    }

    .gender-group label {
        width: 100%;
    }
}

    </style>                                                 
</style>
</head>
<body>

    <!-- Marquee Section -->
    <div class="marquee-container">
        <marquee>Welcome! Please enter your details to proceed with the payment. All fields are required.</marquee>
    </div>

    <!-- User Information Form -->
    <div class="container">
        <h2>Enter Your Information</h2>
        <!-- <form action="payment.php" method="POST" id="userInfoForm"> -->
        <form action="user_info.php" method="POST" id="userInfoForm">
        <div class="form-group">
    <input type="text" name="full_name" id="full_name" placeholder="Full Name" class="input-field" required>
</div>

<div class="form-group">
 
    <input type="email" name="email" id="email" placeholder="Email" class="input-field" required>
</div>
                                                         

<div class="form-group phone-group">
                <select name="phone_code" id="phone_code" class="input-field phone-code" required>
                    <option value="+977">+977 (Nepal)</option>
                </select>
                <input type="tel" name="phone" id="phone" placeholder="Phone Number" class="input-field phone-number" required>
            </div>
          

            <div class="gender-group">
                <label><input type="radio" name="gender" value="male" required> Male</label>
                <label><input type="radio" name="gender" value="female"> Female</label>
                <label><input type="radio" name="gender" value="other"> Other</label>
            </div>

          
            <div class="form-group">
    <select name="district" id="district" class="input-field" required onchange="updatePlaces()">
        <option value="">Select District</option>
        <option value="banke">Banke</option>
        <option value="badiya">Badiya</option>
        <option value="dang">Dang</option>
        <option value="surkhet">Surkhet</option>
    </select>
</div>

<div class="form-group" id="places-container" style="display:none;">
    <select name="place" id="place" class="input-field" required>
        <option value="">Select Place</option>
    </select>
</div>     
           

            <div class="form-group">
                <select name="occupation" id="occupation" class="input-field" required>
                    <option value="">Select Occupation</option>
                    <option value="Engineer">Engineer</option>
                    <option value="Doctor">Doctor</option>
                    <option value="Student">Student</option>
                    <option value="Teacher">Teacher</option>
                    <option value="Businessperson">Businessperson</option>
                    <option value="Farmer">Farmer</option>
                    <option value="Artist">Artist</option>
                    <option value="Scientist">Scientist</option>
                    <option value="Lawyer">Lawyer</option>
                    <option value="Accountant">Accountant</option>
                    <option value="Nurse">Nurse</option>
                    <option value="Technician">Technician</option>
                    <option value="Self-employed">Self-employed</option>
                    <option value="Government Employee">Government Employee</option>
                    <option value="Unemployed">Unemployed</option>
                    <option value="Retired">Retired</option>
                    <option value="Other">Other</option>
                </select>
                <select name="nationality" id="nationality" class="input-field" required>
                    <option value="">Select Nationality</option>
                    <option value="Nepali">Nepali</option>
                    <option value="Indian">Indian</option>
                </select>
            </div>
            <input type="text" name="city" id="city" placeholder="City" class="input-field" required>
            <input type="text" name="remarks" id="remarks" placeholder="Remarks (optional)" class="input-field">

         
            <div class="button-container">
            <button type="submit" class="button" onclick="showAlert()">Submit</button>

                <button type="button" class="button next-btn" onclick="window.location.href='payment.php'">Next</button>
                            </div>
        </form>

        <div id="error-message" class="error"></div>
    </div>
 
    <script>
    function updatePlaces() {
        var district = document.getElementById('district').value;
        var placesContainer = document.getElementById('places-container');
        var placeDropdown = document.getElementById('place');
        
        // Clear previous options
        placeDropdown.innerHTML = '<option value="">Select Place</option>';

        // Show places based on district selected
        if (district === "banke") {
            placesContainer.style.display = "block";
            placeDropdown.innerHTML += '<option value="Nepalgunj">Nepalgunj</option>';
            placeDropdown.innerHTML += '<option value="Kohalpur">Kohalpur</option>';
            placeDropdown.innerHTML += '<option value="Khajura">Khajura</option>';
            placeDropdown.innerHTML += '<option value="Duduwa">Duduwa</option>';
            placeDropdown.innerHTML += '<option value="Jajhira">Jajhira</option>';
            placeDropdown.innerHTML += '<option value="Bichwa">Bichwa</option>';
            placeDropdown.innerHTML += '<option value="Chisapani">Chisapani</option>';
            placeDropdown.innerHTML += '<option value="Baijnath">Baijnath</option>';
            placeDropdown.innerHTML += '<option value="Gothaha">Gothaha</option>';
            placeDropdown.innerHTML += '<option value="Tulsipur">Tulsipur</option>';
            placeDropdown.innerHTML += '<option value="Rajapur">Rajapur</option>';
        } else if (district === "badiya") {
            placesContainer.style.display = "block";
            placeDropdown.innerHTML += '<option value="Thakurdwara">Thakurdwara</option>';
            placeDropdown.innerHTML += '<option value="Bardiya National Park">Bardiya National Park</option>';
            placeDropdown.innerHTML += '<option value="Geruwa">Geruwa</option>';
            placeDropdown.innerHTML += '<option value="Rajapur">Rajapur</option>';
            placeDropdown.innerHTML += '<option value="Dhanauri">Dhanauri</option>';
            placeDropdown.innerHTML += '<option value="Bharwaliya">Bharwaliya</option>';
            placeDropdown.innerHTML += '<option value="Belauri">Belauri</option>';
            placeDropdown.innerHTML += '<option value="Madhuwan">Madhuwan</option>';
            placeDropdown.innerHTML += '<option value="Bishalpur">Bishalpur</option>';
            placeDropdown.innerHTML += '<option value="Sukhipur">Sukhipur</option>';
            placeDropdown.innerHTML += '<option value="Chisapani">Chisapani</option>';
            placeDropdown.innerHTML += '<option value="Kamalpur">Kamalpur</option>';
            placeDropdown.innerHTML += '<option value="Bashwara">Bashwara</option>';
        } else if (district === "dang") {
            placesContainer.style.display = "block";
            placeDropdown.innerHTML += '<option value="Tulsipur">Tulsipur</option>';
            placeDropdown.innerHTML += '<option value="Lamahi">Lamahi</option>';
            placeDropdown.innerHTML += '<option value="Ghorahi">Ghorahi</option>';
            placeDropdown.innerHTML += '<option value="Rajpur">Rajpur</option>';
            placeDropdown.innerHTML += '<option value="Mahalaxmi">Mahalaxmi</option>';
            placeDropdown.innerHTML += '<option value="Chhatrabari">Chhatrabari</option>';
            placeDropdown.innerHTML += '<option value="Bhalubang">Bhalubang</option>';
            placeDropdown.innerHTML += '<option value="Siddhartha">Siddhartha</option>';
            placeDropdown.innerHTML += '<option value="Haku">Haku</option>';
            placeDropdown.innerHTML += '<option value="Dandajheri">Dandajheri</option>';
        } else if (district === "surkhet") {
            placesContainer.style.display = "block";
            placeDropdown.innerHTML += '<option value="Birendranagar">Birendranagar</option>';
            placeDropdown.innerHTML += '<option value="Karnali">Karnali</option>';
            placeDropdown.innerHTML += '<option value="Burgung">Burgung</option>';
            placeDropdown.innerHTML += '<option value="Chingad">Chingad</option>';
            placeDropdown.innerHTML += '<option value="Chaukune">Chaukune</option>';
            placeDropdown.innerHTML += '<option value="Narayan">Narayan</option>';
            placeDropdown.innerHTML += '<option value="Benkhola">Benkhola</option>';
            placeDropdown.innerHTML += '<option value="Panchapuri">Panchapuri</option>';
            placeDropdown.innerHTML += '<option value="Bhuragaun">Bhuragaun</option>';
            placeDropdown.innerHTML += '<option value="Lamki">Lamki</option>';
        } else {
            placesContainer.style.display = "none";
        }
    }
</script>

    <script>
        // Client-side form validation
// Client-side form validation
document.getElementById("userInfoForm").addEventListener("submit", function(event) {
    let errorMessage = "";
    let emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    let phonePattern = /^[0-9]{10}$/;
   

    let email = document.getElementById("email").value;
    let phone = document.getElementById("phone").value;
  
    if (!emailPattern.test(email)) {
        errorMessage += "Invalid email format.\n";
    }
    if (!phonePattern.test(phone)) {
        errorMessage += "Phone number must be 10 digits.\n";
    }


    if (errorMessage) {
        document.getElementById("error-message").innerText = errorMessage;
        event.preventDefault();  // Prevent form submission
    }
});

<script>
</body>
</html>
