<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Mail;

Mail::raw('SMTP test', function ($message) {
    $message->to('yoloverse2023@gmail.com')->subject('SMTP test');
});

echo 'mail_send_command_ran';
