<?php

namespace JairoJeffersont\Helpers;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Class EmailService
 *
 * A helper service to send emails using PHPMailer with SMTP configuration loaded from environment variables.
 *
 * This class initializes PHPMailer with settings loaded from environment variables (.env file),
 * and provides a method to send HTML emails.
 *
 * @package JairoJeffersont\Helpers
 */
class EmailService {
    /**
     * The PHPMailer instance used to send emails.
     *
     * @var PHPMailer
     */
    protected $mailer;

    /**
     * EmailService constructor.
     *
     * Loads environment variables from the .env file (if not already loaded),
     * and configures the PHPMailer instance using SMTP settings.
     *
     * @throws \PHPMailer\PHPMailer\Exception if PHPMailer initialization fails
     */
    public function __construct() {

        $this->mailer = new PHPMailer(true);

        $this->mailer->isSMTP();
        $this->mailer->Host       = $_ENV['MAIL_HOST'];
        $this->mailer->SMTPAuth   = true;
        $this->mailer->Username   = $_ENV['MAIL_USERNAME'];
        $this->mailer->Password   = $_ENV['MAIL_PASSWORD'];
        $this->mailer->SMTPSecure = $_ENV['MAIL_ENCRYPTION'];
        $this->mailer->Port       = $_ENV['MAIL_PORT'];

        $this->mailer->setFrom($_ENV['MAIL_FROM_ADDRESS'], $_ENV['MAIL_FROM_NAME']);
        $this->mailer->isHTML(true);
        $this->mailer->CharSet = 'UTF-8';
    }

    /**
     * Sends an HTML email to a single recipient using the configured SMTP transport.
     *
     * This method:
     *  - Clears any previously added recipients
     *  - Sets the recipient email address
     *  - Defines the email subject and HTML body
     *  - Attempts to send the email using PHPMailer
     *
     * The email is sent using the SMTP configuration provided via environment variables.
     * Character encoding is UTF-8 and the email content is treated as HTML.
     *
     * @param string $to
     *        The recipient's email address.
     *
     * @param string $subject
     *        The subject line of the email.
     *
     * @param string $body
     *        The HTML content of the email body.
     *
     * @return array{
     *     status: string
     * }
     * Returns an associative array indicating the result of the operation.
     * On success, the array contains:
     *  - status: "success"
     *
     * @throws \RuntimeException
     *         Thrown when the email fails to send due to SMTP errors,
     *         invalid configuration, or internal PHPMailer exceptions.
     */

    public function sendMail(string $to, string $subject, string $body): array {
        try {
            $this->mailer->clearAllRecipients();
            $this->mailer->addAddress($to);
            $this->mailer->Subject = $subject;
            $this->mailer->Body    = $body;

            $this->mailer->send();

            return ['status' => 'success'];
        } catch (Exception $e) {
            throw new \RuntimeException("Failed to send email: {$e->getMessage()}", 0, $e);
        }
    }
}
