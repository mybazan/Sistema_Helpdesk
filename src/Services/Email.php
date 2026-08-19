<?php

namespace App\Services;

use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordToken;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RequestStack;


class Email
{
    private $mailer;
    private $requestStack;

    public function __construct(MailerInterface $mailer, RequestStack $requestStack)
    {
        $this->mailer = $mailer;
        $this->requestStack = $requestStack;
    }

    public function processSendingEmailPassword(User $user, ResetPasswordToken $resetToken, string $tokenLifetime)
    {
        $email = (new TemplatedEmail())
            ->from(new Address($_ENV["ACCOUNT_EMAIL"], 'Sistema de Soporte'))
            ->to($user->getEmail())
            ->subject('Recuperar contraseña - Sistema de Soporte')
            ->htmlTemplate('reset_password/email.html.twig')
            ->context([
                'resetToken' => $resetToken,
                'tokenLifetime' => $tokenLifetime,
                'nombre' => $user->getNombre(),
                'usuario' => $user->getUsername(),
            ]);

        $this->mailer->send($email);
    }

    public function processSendingChangePassword(User $user)
    {
        $request = $this->requestStack->getCurrentRequest();
        $userAgent = $request->headers->get('User-Agent');

        if (stripos($userAgent, 'Windows') !== false) {
            $userAgent = 'Windows';
        } elseif (stripos($userAgent, 'Macintosh') !== false || stripos($userAgent, 'Mac OS') !== false) {
            $userAgent = 'Mac OS';
        } elseif (stripos($userAgent, 'Linux') !== false) {
            $userAgent = 'Linux';
        } else {
            $userAgent = 'Desconocido';
        }
        
        $email = (new TemplatedEmail())
            ->from(new Address($_ENV["ACCOUNT_EMAIL"], 'Sistema de Soporte'))
            ->to($user->getEmail())
            ->subject('Cambio de contraseña - Sistema de Soporte')
            ->htmlTemplate('reset_password/email_password.html.twig')
            ->context([
                'nombre' => $user->getNombre(),
                'ip' => $request->getClientIp(),
                'so' => $userAgent,
            ]);

        $this->mailer->send($email);
    }
}