<?php
echo "testing";
die()
​
//READ!!!!!!!!!!!!!
//https://pepipost.com/tutorials/send-an-email-via-gmail-smtp-server-using-php/
//run locally php -S localhost:8000
// use this to redirect user to ur html page at any point header('Location: http://google.com');
//if getting errors after pushing to heroku: https://accounts.google.com/b/0/DisplayUnlockCaptcha
​
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
​
require 'vendor/autoload.php';
​
// $env = getenv("PASSWORD");
​
$mail = new PHPMailer(true);
​
$conn = mysqli_connect("us-cdbr-east-02.cleardb.com", "b6d19c977650fd", "c1ede30b", "heroku_1f0a660f47e45f9");
​
//check if not connected
if (!$conn) {
    echo 'Connection error: ' . mysqli_connect_error();
} else {
    // $sql = "CREATE TABLE emails (
    //     id INT AUTO_INCREMENT PRIMARY KEY,
    //     email VARCHAR(100) NOT NULL)";
​
    // if ($conn->query($sql) === TRUE) {
    //     echo "Table MyGuests created successfully";
    // } else {
    //     echo "Error creating table: " . $conn->error;
    // }
​
​
    if (!isset($_POST["email"])) {
        die("no email");
    }
    $receiver = mysqli_real_escape_string($conn, $_POST["email"]);
​
​
    $sql = "INSERT INTO emails (email) VALUES ('$receiver')";
​
    if ($conn->query($sql) === TRUE) {
        // echo "New record created successfully";
        //send email
        $mail->IsSMTP();
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;
        $mail->SMTPAuth   = true;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->Host       = "smtp.gmail.com";
        $mail->Username   = "ojadianita@gmail.com";
        $mail->Password   = getenv('PASSWORD');
​
        $mail->IsHTML(true);
        $mail->AddAddress($receiver, "subscriber");
        $mail->SetFrom("ojadianita@gmail.com", "Finderz landing page");
        $mail->AddReplyTo("ojadianita@gmail.com", "Finderz landing page");
        $mail->Subject = "Welcome Onboard!";
        $content = "<b>Thank you for subscribing</b>";
​
        $mail->MsgHTML($content);
        if (!$mail->Send()) {
            echo "Error while sending Email.";
            // var_dump($mail);
        } else {
            echo "Email was sent successfully";
        }
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
​
    $conn->close();
}
