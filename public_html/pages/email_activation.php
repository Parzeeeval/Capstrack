<?php 
    $mail->isSMTP();
    $mail->Host = 'smtp.hostinger.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'capstrack.bulsu.cict@capstrack.tech';
    $mail->Password = '@Capstrack4206911';
    $mail->SMTPSecure = 'ssl';
    $mail->Port = 465;
    
    $mail->setFrom('capstrack.bulsu.cict@capstrack.tech', 'Capstrack BulSU CICT');
    $mail->addAddress($userInfo[$r][0], $userInfo[$r][1] . " " . $userInfo[$r][3]);
    $mail->addReplyTo('capstrack.bulsu.cict@capstrack.tech', 'Capstrack BulSU CICT');;
    
    $mail->isHTML(true);
    $mail->Subject = 'Capstrack Account Activation';
    
    $logoUrl = 'https://drive.google.com/uc?id=1fUvSX-J3tc8G-fPvo5T9K1-xrwGb8Cvr';
    $activationLink = 'https://capstrack.tech/activate?id=' . $generated_id . '&token=' . $generated_token;
    
    $mail->Body = '
        <div style="background-color: #f4f4f4; padding: 20px; font-family: Arial, sans-serif; color: #333;">
            <div style="max-width: 600px; margin: auto; background: #EF7215; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);">
                <!-- Logo -->
                <div style="text-align: center; margin-bottom: 20px;">
                    <img src="' . $logoUrl . '" alt="Capstrack Logo" style="width: 80px; height: 80px;">
                </div>
                
                <!-- Greeting and Message -->
                <h2 style="text-align: center; color: white;">Hello, ' . $userInfo[$r][1] . ' ' . $userInfo[$r][3] . '!</h2>
                <p style="font-size: 16px; color: white; text-align: center;">
                    We are excited to welcome you to <b>Capstrack</b>! To activate your account, please click the button below and use the provided credentials.
                </p>
                
                <!-- Activation Button -->
                <div style="text-align: center; margin: 20px 0;">
                    <a href="' . $activationLink . '" style="display: inline-block; padding: 12px 24px; background-color: #0056b3; color: #ffffff; border-radius: 5px; text-decoration: none; font-weight: bold;">Activate Account</a>
                </div>
                
                <!-- User Credentials -->
                <div style="background-color: #f8f8f8; padding: 15px; border-radius: 8px; margin-top: 20px;">
                    <p style="margin: 0;"><b style="color: #0056b3;">User ID:</b> ' . $generated_id . '</p>
                    <p style="margin: 0;"><b style="color: #0056b3;">Generated Password:</b> ' . $generated_password . '</p>
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
    
    $mail->AltBody = 'Hello, ' . $userInfo[$r][1] . ' ' . $userInfo[$r][3] . '
    To activate your Capstrack account, please use the following link:
    
    ' . $activationLink . '
    
    User ID: ' . $generated_id . '
    Generated Password: ' . $generated_password . '
    
    From Capstrack, Bulacan State University, City of Malolos, Bulacan, Philippines
    https://www.Capstrack.tech';
    
    if ($mail->send()) {

        $last_sequence = getLastSequence();
        $query = "UPDATE sequence_tracker SET last_sequence = ?";
        $stmt = $conn->prepare($query);
        $result = $stmt->execute([$last_sequence]);
    
        if ($result) {
            
            if ($conn->commit()) {
                $messages[] = "<li><strong>Email:</strong> <span style=\"color: green; font-weight: bold;\">" . htmlspecialchars($userInfo[$r][0]) . "</span> <em>Sent Successfully!</em></li>";
            } 
            
            else {
                throw new Exception("Database commit failed for user: " . $userInfo[$r][0]);
            }
        }
        
        else {
            throw new Exception("Could not update last_sequence.");
        }
    } else {
        throw new Exception('Message could not be sent to ' . $userInfo[$r][0] . ' Mailer Error: ' . $mail->ErrorInfo);
        echo '<script>console.log("Error: "'. addslashes($mail->ErrorInfo).');</script>';
    }
    
    $mail->clearAddresses();
    $mail->clearAttachments();
?>



