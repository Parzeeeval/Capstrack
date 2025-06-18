<?php 
    $mail->isSMTP();
    $mail->Host = 'smtp.hostinger.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'capstrack.bulsu.cict@capstrack.tech';
    $mail->Password = '@Capstrack4206911';
    $mail->SMTPSecure = 'ssl';
    $mail->Port = 465;
    
    $mail->setFrom('capstrack.bulsu.cict@capstrack.tech', 'Capstrack BulSU CICT');
    $mail->addAddress($email, $firstname . " " . $surname);
    $mail->addReplyTo('capstrack.bulsu.cict@capstrack.tech', 'Capstrack BulSU CICT');;
    
    $mail->isHTML(true);
    $mail->Subject = 'Capstrack Forgot Password';
    
    $logoUrl = 'https://drive.google.com/uc?id=1fUvSX-J3tc8G-fPvo5T9K1-xrwGb8Cvr';
    $activationLink = 'https://capstrack.tech/forgot_pass?id=' . $userID . '&code=' . $generated_code . '&token=' . $generated_token;
    
    $mail->Body = '
        <div style="background-color: #f4f4f4; padding: 20px; font-family: Arial, sans-serif; color: #333;">
            <div style="max-width: 600px; margin: auto; background: #EF7215; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);">
                <!-- Logo -->
                <div style="text-align: center; margin-bottom: 20px;">
                    <img src="' . $logoUrl . '" alt="Capstrack Logo" style="width: 80px; height: 80px;">
                </div>
                
                <!-- Greeting and Message -->
                <h2 style="text-align: center; color: white;">Reset Password</h2>
                <p style="font-size: 16px; color: white; text-align: center;">
                    <b>Reset Password request</b> To reset your password, please click the button below and use the provided confirmation code.
                </p>
                
                <!-- Activation Button -->
                <div style="text-align: center; margin: 20px 0;">
                    <a href="' . $activationLink . '" style="display: inline-block; padding: 12px 24px; background-color: #0056b3; color: #ffffff; border-radius: 5px; text-decoration: none; font-weight: bold;">Reset Password</a>
                </div>
                
                <!-- User Credentials -->
                <div style="background-color: #f8f8f8; padding: 15px; border-radius: 8px; margin-top: 20px;">
                    <p style="margin: 0;"><b style="color: #0056b3;">Confirmation Code:</b><strong>' . $generated_code . '<strong></p>
                </div>
    
                <!-- Additional Info -->
                <p style="font-size: 14px; color: white; text-align: center; margin-top: 20px;">
                    Bulacan State University, City of Malolos, Bulacan, Philippines
                </p>
                
                <!-- Footer -->
                <div style="text-align: center; margin-top: 20px; font-size: 12px; color: #aaa;">
                    <p style="color: #0056b3; text-decoration: none;">&copy; ' . date("Y") . ' Capstrack.tech. All rights reserved.</p>
                    <p><a href="https://www.Capstrack.tech" style="color: #0056b3; text-decoration: none;">Visit our website</a></p>
                </div>
            </div>
        </div>
    ';
    
    $mail->AltBody = 'Reset password' . '
    To reset your Capstrack password, please use the following link:
    
    ' . $activationLink . '
    
    Confirmation Code: ' . $generated_code . '
    
    From Capstrack, Bulacan State University, City of Malolos, Bulacan, Philippines
    https://www.Capstrack.tech';
    
    if ($mail->send()) {

        if ($conn->commit()) {
            echo "<script>
                Swal.fire({
                    title: 'Forgot Password Request',
                    text: 'Reset Email Sent Succesfuly!',
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.isConfirmed) {
                         window.location.href = '/login';
                    }
                    
                    else if (result.isDismissed) {
                         window.location.href = '/login';
                    }
                });
            </script>";
        } 
        
        else {
            throw new Exception("Database commit failed");
        }
    } 
    
    else {
        throw new Exception('Message could not be sent to ' .$email . ' Mailer Error: ' . $mail->ErrorInfo);
        echo '<script>console.log("Error: "'. addslashes($mail->ErrorInfo).');</script>';
    }
    
    $mail->clearAddresses();
    $mail->clearAttachments();
?>



