<?php

namespace Symfony\Component\Mailer\Transport;

use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\SentMessage;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Monolog\Logger as MonologLogger;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;


final class LogTransport extends AbstractTransport
{
    private MonologLogger $transport;

    public function __construct(string $path, string $environment = 'DEV', EventDispatcherInterface $dispatcher = null, LoggerInterface $logger = null)
    {
        parent::__construct($dispatcher, $logger);

        $monolog = new MonologLogger($environment);

        $monolog->pushHandler($handler = new RotatingFileHandler($path, 0, MonologLogger::DEBUG));

        $handler->setFormatter(new LineFormatter(null, null, true, true));

        $this->transport = $monolog;
    }

    protected function doSend(SentMessage $message): void
    {
        $this->transport->debug($message->toString());
    }

    public function __toString(): string
    {
        return 'log://monolog';
    }
}
