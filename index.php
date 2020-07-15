<?php

//READ!!!!!!!!!!!!!
//https://pepipost.com/tutorials/send-an-email-via-gmail-smtp-server-using-php/
//run locally php -S localhost:8000
// use this to redirect user to ur html page at any point header('Location: http://google.com');
//if getting errors after pushing to heroku: https://accounts.google.com/b/0/DisplayUnlockCaptcha

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, Origin, Cache-Control, Pragma, Authorization, Accept, Accept-Encoding");


// $env = getenv("PASSWORD");

$mail = new PHPMailer(true);

$conn = mysqli_connect("us-cdbr-east-02.cleardb.com", "b6d19c977650fd", "c1ede30b", "heroku_1f0a660f47e45f9");

//check if not connected
if (!$conn) {
    echo 'Connection error: ' . mysqli_connect_error();
} else {
    if ($_SERVER['REQUEST_URI'] === "/emails") {
        $sql = "SELECT * FROM emails";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            // var_dump($result);
            while ($row = $result->fetch_assoc()) {
                echo "id: " . $row["id"] . " - Name: " . $row["email"] . "<br>";
            }
        } else {
            die("couldn't fetch emails");
        }
        exit();
    }

    if (!isset($_POST["email"])) {
        die("no email");
    }
    $receiver = mysqli_real_escape_string($conn, $_POST["email"]);


    $sql = "INSERT INTO emails (email) VALUES ('$receiver')";

    if ($conn->query($sql) === TRUE) {
        // echo "New record created successfully";
        //send email
        $mail->IsSMTP();
        $mail->SMTPDebug = false;
        $mail->SMTPAuth   = true;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->Host       = "smtp.gmail.com";
        $mail->Username   = "ojadianita@gmail.com";
        $mail->Password   = getenv('PASSWORD');

        $mail->IsHTML(true);
        $mail->AddAddress($receiver, "subscriber");
        $mail->SetFrom("ojadianita@gmail.com", "Finderz landing page");
        $mail->AddReplyTo("ojadianita@gmail.com", "Finderz landing page");
        $mail->Subject = "Welcome Onboard!";
        $content = "<b>Thank you for subscribing.</b>";

        $mail->MsgHTML($content);
        if (!$mail->Send()) {
            echo "Error while sending Email.";
        } else {
            echo "success";
        }
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
