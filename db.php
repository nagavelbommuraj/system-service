<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

$servername = "localhost";
$username = "root"; // Change to your database username
$password = ""; // Change to your database password
$dbname = "appointment"; // Change to your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Validate POST data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = isset($_POST['name']) ? $_POST['name'] : null;
    $email = isset($_POST['email']) ? $_POST['email'] : null;
    $number = isset($_POST['number']) ? $_POST['number'] : null;
    $appointment_for = isset($_POST['appointment_for']) ? $_POST['appointment_for'] : null;
    $appointment_description = isset($_POST['appointment_description']) ? $_POST['appointment_description'] : null;
    $date = isset($_POST['date']) ? $_POST['date'] : null;
    $time = isset($_POST['time']) ? $_POST['time'] : null;

    // Check if any field is missing
    if (empty($name) || empty($email) || empty($number) || empty($appointment_for) || empty($appointment_description) || empty($date) || empty($time)) {
        echo "All fields are required.";
        exit();
    }

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO users (name, email, number, appointment_for, appointment_description, date, time) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $name, $email, $number, $appointment_for, $appointment_description, $date, $time);

    if ($stmt->execute()) {
        // Send email notification to the user
        sendEmail($email, 'Welcome to Our Service', getWelcomeMessage($name));

        // Send notification email to the admin
        $adminEmail = 'nagaveljackson77@gmail.com'; // Change to your admin email address
        sendEmail($adminEmail, 'New User Registration', getAdminNotificationMessage($name, $email, $number));

        // Redirect to home page after successful form submission
        header("Location: index.html");
        exit(); // Make sure to exit after the header redirection
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
$conn->close();

function sendEmail($to, $subject, $body) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->SMTPDebug = 0;
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Corrected the SMTP server to gmail.com
        $mail->SMTPAuth = true;
        $mail->Username = 'nagaveljackson77@gmail.com'; // SMTP username
        $mail->Password = 'awalglnrjxbxdeli'; // SMTP password (consider using environment variables for security)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;

        // Recipients
        $mail->setFrom('nagaveljackson77@gmail.com', 'Violavizn Education');
        $mail->addAddress($to);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->AltBody = strip_tags($body);

        $mail->send();
        echo 'Message has been sent';
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

function getWelcomeMessage($name) {
    return "<h1>Welcome, $name!</h1>
            <p>Thank you for registering with us. We are thrilled to have you on board.</p>
            <p>If you have any questions or need any assistance, please feel free to reach out to us.</p>
            <p>Best regards,<br>Your Service Team</p>";
}

function getAdminNotificationMessage($name, $email, $number) {
    return "<h1>New User Registration</h1>
            <p>A new user has registered with the following details:</p>
            <p>Name: $name</p>
            <p>Email: $email</p>
            <p>Phone Number: $number</p>";
}
