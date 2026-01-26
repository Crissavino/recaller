<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestEmail extends Command
{
    protected $signature = 'email:test {email : The email address to send to} {--debug : Show detailed debug info}';

    protected $description = 'Send a test email to verify email configuration';

    public function handle(): int
    {
        $email = $this->argument('email');
        $debug = $this->option('debug');

        $this->info("Email Configuration:");
        $this->line("  MAIL_MAILER: " . config('mail.default'));
        $this->line("  MAIL_FROM: " . config('mail.from.address'));
        $this->line("  RESEND_KEY: " . (config('services.resend.key') ? 'Set (' . substr(config('services.resend.key'), 0, 10) . '...)' : 'NOT SET'));
        $this->newLine();

        $this->info("Sending test email to {$email}...");

        try {
            Mail::raw(
                "This is a test email from Recaller.\n\nIf you received this, your email configuration is working!\n\nSent at: " . now()->toDateTimeString(),
                function ($message) use ($email) {
                    $message->to($email)
                        ->subject('Recaller - Test Email (' . now()->format('H:i:s') . ')');
                }
            );

            $this->newLine();
            $this->info("Email sent! (no exceptions thrown)");
            $this->line("Check your inbox AND spam folder at {$email}");

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->newLine();
            $this->error("Failed to send email!");
            $this->error("Error: " . $e->getMessage());

            if ($debug) {
                $this->newLine();
                $this->line("Stack trace:");
                $this->line($e->getTraceAsString());
            }

            return self::FAILURE;
        }
    }
}
