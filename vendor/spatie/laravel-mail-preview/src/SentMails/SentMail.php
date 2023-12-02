<?php

namespace Spatie\MailPreview\SentMails;

use Illuminate\Support\Str;
use PHPUnit\Framework\Assert;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mime\Address;

class SentMail
{
    public function __construct(
        public SentMessage $message,
        public string $htmlPath,
        public string $emlPath,
    ) {
    }

    public function body(): string
    {
        return $this->message->getOriginalMessage()->getBody()->bodyToString();
    }

    public function bodyContains(string $expectedSubstring): bool
    {
        return Str::contains($this->body(), $expectedSubstring);
    }

    public function subject(): ?string
    {
        return $this->message->getOriginalMessage()->getSubject();
    }

    public function assertSubjectContains(string $expectedSubstring)
    {
        $actualSubject = $this->subject();

        Assert::assertStringContainsString($expectedSubstring, $actualSubject, "The expected substring `$expectedSubstring` was not found in the actual subject `$actualSubject`");
    }

    public function from(): array
    {
        return array_map(function (Address $address) {
            return $address->getAddress();
        }, $this->message->getOriginalMessage()->getFrom());
    }

    public function hasFrom(string $expectedAddress): bool
    {
        return in_array($expectedAddress, $this->from());
    }

    public function assertFrom(string $expectedAddress): self
    {
        Assert::assertContains(
            $expectedAddress,
            $this->from(),
            "Did not find `{$expectedAddress}` in the `to` recipients: " . implode(', ', $this->from())
        );

        return $this;
    }

    public function to(): array
    {
        return array_map(function (Address $address) {
            return $address->getAddress();
        }, $this->message->getOriginalMessage()->getTo());
    }

    public function hasTo(string $expectedAddress): bool
    {
        return in_array($expectedAddress, $this->to());
    }

    public function assertTo(string $expectedAddress): self
    {
        Assert::assertContains(
            $expectedAddress,
            $this->to(),
            "Did not find `{$expectedAddress}` in the `to` recipients: " . implode(', ', $this->to())
        );

        return $this;
    }

    public function cc(): array
    {
        return array_map(function (Address $address) {
            return $address->getAddress();
        }, $this->message->getOriginalMessage()->getCc());
    }

    public function hasCc(string $expectedAddress): bool
    {
        return in_array($expectedAddress, $this->cc());
    }

    public function assertCc(string $expectedAddress): self
    {
        Assert::assertContains(
            $expectedAddress,
            $this->cc(),
            "Did not find `{$expectedAddress}` in the `cc` recipients: " . implode(', ', $this->cc())
        );

        return $this;
    }

    public function bcc(): array
    {
        return array_map(function (Address $address) {
            return $address->getAddress();
        }, $this->message->getOriginalMessage()->getBcc());
    }

    public function hasBcc(string $expectedAddress): bool
    {
        return in_array($expectedAddress, $this->bcc());
    }

    public function assertBcc(string $expectedAddress): self
    {
        Assert::assertContains(
            $expectedAddress,
            $this->bcc(),
            "Did not find `{$expectedAddress}` in the `bcc` recipients: " . implode(', ', $this->bcc())
        );

        return $this;
    }

    public function assertContains(string $substring): self
    {
        Assert::assertStringContainsString(
            $substring,
            $this->body(),
            "Did not find `{$substring}` in the body of the sent mail",
        );

        return $this;
    }
}
