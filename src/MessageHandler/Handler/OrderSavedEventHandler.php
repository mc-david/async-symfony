<?php

namespace App\MessageHandler\Handler;

use App\Message\Event\OrderSavedEvent;
use Mpdf\Mpdf;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Mime\Email;

#[AsMessageHandler]
class OrderSavedEventHandler
{

    public function __construct(private MailerInterface $mailer)
    {
    }

    public function __invoke(OrderSavedEvent $event)
    {
        $mpdf = new Mpdf();
        $content = "<h1>Contract Note for Order {$event->getOrderId()}</h1>";
        $content .= '<p>Total: <b>$1898.75</b></p>';
        $mpdf->writeHtml($content);
        $contractNotePdf = $mpdf->output('', 'S' );

        $email = (new Email())
            ->from('sales@stocksapp.com')
            ->to('email@example.tech')
            ->subject('Contract note for order ' . $event->getOrderId())
            ->text('Here is your contract note!')
            ->attach($contractNotePdf, 'contract-note.pdf');

        $this->mailer->send($email);
    }
}