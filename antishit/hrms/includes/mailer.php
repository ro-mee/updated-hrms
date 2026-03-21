<?php
/**
 * Mailer Helper (PHPMailer via Gmail SMTP)
 * HRMS - Human Resource Management System
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require_once __DIR__ . '/PHPMailer/Exception.php';
require_once __DIR__ . '/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/SMTP.php';

function getMailer(): PHPMailer {
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        // The user's provided credentials
        $mail->Username   = 'crypticalrome@gmail.com'; 
        $mail->Password   = 'uefn gasa zdbb vpuq';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        
        $mail->setFrom('crypticalrome@gmail.com', APP_NAME);
        $mail->isHTML(true);
    } catch (Exception $e) {
        error_log("Mailer configuration failed: {$mail->ErrorInfo}");
    }
    return $mail;
}

function sendMail(string $to, string $subject, string $htmlBody, string $fromName = APP_NAME): bool {
    try {
        $mail = getMailer();
        $mail->addAddress($to);
        $mail->Subject = $subject;
        $mail->Body    = $htmlBody;
        $mail->AltBody = strip_tags($htmlBody);
        
        return $mail->send();
    } catch (Exception $e) {
        error_log("Email sending failed to {$to}: {$e->getMessage()}");
        return false;
    }
}

function send2FACode(string $to, string $name, string $code): bool {
    $subject = "Your 2-Step Verification Code";
    $body = "
    <div style='font-family: Arial, sans-serif; max-width: 500px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px;'>
        <h2 style='color: #0d6efd; text-align: center;'>2-Step Verification</h2>
        <p>Hi {$name},</p>
        <p>To keep your account safe, we need to verify your identity. Use the 6-digit code below to complete your login:</p>
        <div style='background-color: #f8f9fa; padding: 15px; text-align: center; margin: 20px 0; border-radius: 5px;'>
            <h1 style='letter-spacing: 5px; margin: 0; color: #333;'>{$code}</h1>
        </div>
        <p>This code will expire in 10 minutes.</p>
        <p>If you didn't request this code, please secure your account immediately.</p>
        <hr style='border: none; border-top: 1px solid #eee; margin-top: 30px;'/>
        <p style='font-size: 12px; color: #888; text-align: center;'>&copy; " . date('Y') . " " . APP_NAME . " Security Team</p>
    </div>
    ";
    return sendMail($to, $subject, $body);
}

// Keeping the older functions intact so other modules don't break
function mailLeaveStatusUpdate(array $employee, array $leave): void {
    $status  = ucfirst($leave['status']);
    $subject = "[HRMS] Leave Request $status";
    $body    = "<html><body style='font-family:sans-serif;'><p>Dear {$employee['full_name']},</p><p>Your leave request from <strong>{$leave['start_date']}</strong> to <strong>{$leave['end_date']}</strong> has been <strong>$status</strong>.</p>" . ($leave['remarks'] ? "<p>Remarks: {$leave['remarks']}</p>" : "") . "<p>Regards,<br>" . APP_NAME . " Team</p></body></html>";
    sendMail($employee['email'], $subject, $body);
}

function mailNewEmployee(array $employee, string $password): void {
    $subject = "Welcome to " . APP_NAME . " - Your Account Details";
    $body    = "<html><body style='font-family:sans-serif;'><p>Dear {$employee['full_name']},</p><p>Your HRMS account has been created. Here are your login details:</p><ul><li><strong>Email:</strong> {$employee['email']}</li><li><strong>Password:</strong> $password</li><li><strong>URL:</strong> <a href='" . APP_URL . "'>" . APP_URL . "</a></li></ul><p>Please change your password after first login.</p><p>Regards,<br>" . APP_NAME . " Team</p></body></html>";
    sendMail($employee['email'], $subject, $body);
}

function mailPayslipReady(array $employee, string $period): void {
    $subject = "[HRMS] Your Payslip for $period is Ready";
    $body    = "<html><body style='font-family:sans-serif;'><p>Dear {$employee['full_name']},</p><p>Your payslip for <strong>$period</strong> is available in the HRMS portal.</p><p><a href='" . APP_URL . "'>Login to view your payslip</a></p><p>Regards,<br>" . APP_NAME . " Team</p></body></html>";
    sendMail($employee['email'], $subject, $body);
}

function sendPasswordResetEmail(string $to, string $name, string $link): bool {
    $subject = "Password Reset Request";
    $body = "
    <div style='font-family: Arial, sans-serif; max-width: 500px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px;'>
        <h2 style='color: #4f46e5; text-align: center;'>Password Reset</h2>
        <p>Hi {$name},</p>
        <p>We received a request to reset your password. Click the button below to choose a new one:</p>
        <div style='text-align: center; margin: 30px 0;'>
            <a href='{$link}' style='background-color: #4f46e5; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold;'>Reset Password</a>
        </div>
        <p>If the button doesn't work, copy and paste this link into your browser:</p>
        <p style='font-size: 12px; color: #666; word-break: break-all;'>{$link}</p>
        <p>This link will expire in 1 hour.</p>
        <p>If you didn't request this, you can safely ignore this email.</p>
        <hr style='border: none; border-top: 1px solid #eee; margin-top: 30px;'/>
        <p style='font-size: 12px; color: #888; text-align: center;'>&copy; " . date('Y') . " " . APP_NAME . " Team</p>
    </div>
    ";
    return sendMail($to, $subject, $body);
}
