<?php

namespace Core;

/**
 * SMTP Mailer
 *
 * Sends email via SMTP without any third-party libraries.
 * Supports STARTTLS (port 587) and SSL (port 465) with AUTH LOGIN.
 *
 * Configuration (define in config/config.php):
 *   MAIL_HOST         — SMTP server hostname
 *   MAIL_PORT         — 587 for STARTTLS, 465 for SSL
 *   MAIL_USERNAME     — SMTP login (full email address for Gmail/Outlook)
 *   MAIL_PASSWORD     — App password (Gmail) or account password (Outlook)
 *   MAIL_FROM_ADDRESS — Envelope + header From address
 *   MAIL_FROM_NAME    — Display name in From header
 *   MAIL_ENCRYPTION   — 'tls' (STARTTLS, default) or 'ssl'
 */
class Mailer
{
    private string $host;
    private int    $port;
    private string $username;
    private string $password;
    private string $fromAddress;
    private string $fromName;
    private string $encryption;

    public function __construct()
    {
        $this->host        = defined('MAIL_HOST')         ? MAIL_HOST         : '';
        $this->port        = defined('MAIL_PORT')         ? (int) MAIL_PORT   : 587;
        $this->username    = defined('MAIL_USERNAME')     ? MAIL_USERNAME     : '';
        $this->password    = defined('MAIL_PASSWORD')     ? MAIL_PASSWORD     : '';
        $this->fromAddress = defined('MAIL_FROM_ADDRESS') ? MAIL_FROM_ADDRESS : '';
        $this->fromName    = defined('MAIL_FROM_NAME')    ? MAIL_FROM_NAME    : (defined('APP_NAME') ? APP_NAME : '');
        $this->encryption  = defined('MAIL_ENCRYPTION')   ? MAIL_ENCRYPTION   : 'tls';
    }

    /**
     * Send an email.
     *
     * @param string $to      Recipient email address
     * @param string $subject Email subject (UTF-8)
     * @param string $body    Plain-text body (UTF-8)
     * @param array  $options Optional overrides:
     *                          'reply_to'  => reply-to address
     *                          'from'      => override from address
     *                          'from_name' => override from display name
     */
    public function send(string $to, string $subject, string $body, array $options = []): bool
    {
        if (empty($this->host) || empty($this->username) || empty($this->password)) {
            error_log('Mailer: SMTP not configured (MAIL_HOST / MAIL_USERNAME / MAIL_PASSWORD missing)');
            return false;
        }

        $fromAddress = $options['from']      ?? $this->fromAddress;
        $fromName    = $options['from_name'] ?? $this->fromName;
        $replyTo     = $options['reply_to']  ?? null;

        try {
            $fp = $this->connect();

            $this->read($fp); // server greeting

            $ehlo = 'EHLO ' . $this->getHostname();
            $this->command($fp, $ehlo);

            if ($this->encryption === 'tls') {
                $this->command($fp, 'STARTTLS');
                if (!stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                    throw new \RuntimeException('STARTTLS negotiation failed');
                }
                $this->command($fp, $ehlo); // re-send EHLO after TLS upgrade
            }

            // AUTH LOGIN
            $this->command($fp, 'AUTH LOGIN');
            $this->command($fp, base64_encode($this->username));
            $response = $this->command($fp, base64_encode($this->password));

            if (!str_starts_with(trim($response), '235')) {
                throw new \RuntimeException('SMTP authentication failed: ' . trim($response));
            }

            // Envelope
            $this->command($fp, "MAIL FROM:<{$fromAddress}>");
            $this->command($fp, "RCPT TO:<{$to}>");
            $this->command($fp, 'DATA');

            // Build headers
            $encodedSubject = '=?UTF-8?B?' . base64_encode($subject) . '?=';
            $fromHeader     = $fromName
                ? '"' . $this->encodeHeader($fromName) . '" <' . $fromAddress . '>'
                : $fromAddress;

            $headers  = "From: {$fromHeader}\r\n";
            $headers .= "To: {$to}\r\n";
            $headers .= "Subject: {$encodedSubject}\r\n";
            $headers .= "Date: " . date('r') . "\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
            $headers .= "Content-Transfer-Encoding: base64\r\n";
            if ($replyTo) {
                $headers .= "Reply-To: {$replyTo}\r\n";
            }
            $headers .= "X-Mailer: OVGC-Mailer/1.0\r\n";

            // Body as base64, 76-char lines (RFC 2045)
            $encodedBody = chunk_split(base64_encode($body));

            fwrite($fp, $headers . "\r\n" . $encodedBody . "\r\n.\r\n");
            $dataResponse = $this->read($fp);

            if (!str_starts_with(trim($dataResponse), '250')) {
                throw new \RuntimeException('SMTP DATA rejected: ' . trim($dataResponse));
            }

            fwrite($fp, "QUIT\r\n");
            fclose($fp);

            return true;

        } catch (\Throwable $e) {
            error_log('Mailer error: ' . $e->getMessage());
            return false;
        }
    }

    // -------------------------------------------------------------------------

    /** Open the socket connection to the SMTP server. */
    private function connect(): mixed
    {
        $scheme  = $this->encryption === 'ssl' ? 'ssl' : 'tcp';
        $timeout = 15;
        $errno   = 0;
        $errstr  = '';

        $fp = stream_socket_client(
            "{$scheme}://{$this->host}:{$this->port}",
            $errno,
            $errstr,
            $timeout
        );

        if ($fp === false) {
            throw new \RuntimeException("SMTP connect failed ({$errno}): {$errstr}");
        }

        stream_set_timeout($fp, $timeout);
        return $fp;
    }

    /** Write a command and return the server's response. */
    private function command(mixed $fp, string $cmd): string
    {
        fwrite($fp, "{$cmd}\r\n");
        return $this->read($fp);
    }

    /**
     * Read a (possibly multi-line) SMTP response.
     * Multi-line responses have '-' after the status code; last line has ' '.
     */
    private function read(mixed $fp): string
    {
        $response = '';
        while (($line = fgets($fp, 512)) !== false) {
            $response .= $line;
            // A space at position 3 means this is the last response line
            if (isset($line[3]) && $line[3] === ' ') {
                break;
            }
        }
        return $response;
    }

    /** Encode a header value (display name) for UTF-8 safety. */
    private function encodeHeader(string $value): string
    {
        if (mb_detect_encoding($value, 'ASCII', true) === false) {
            return '=?UTF-8?B?' . base64_encode($value) . '?=';
        }
        return $value;
    }

    /** Hostname to use in EHLO — use server name or fallback to localhost. */
    private function getHostname(): string
    {
        $host = gethostname();
        return ($host !== false && $host !== '') ? $host : 'localhost';
    }
}
