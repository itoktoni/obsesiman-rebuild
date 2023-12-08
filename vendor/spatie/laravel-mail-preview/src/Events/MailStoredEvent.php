<?php

namespace Spatie\MailPreview\Events;

use Symfony\Component\Mailer\SentMessage;

class MailStoredEvent
{
    public function __construct(
        public SentMessage $message,
        public string $pathToHtmlVersion,
        public string $pathToEmlVersion,
    ) {
    }
}
