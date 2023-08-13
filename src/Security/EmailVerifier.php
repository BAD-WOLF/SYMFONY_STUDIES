<?php

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class EmailVerifier
{
    /**
     * Summary of __construct
     * @param \SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface $verifyEmailHelper
     * @param \Symfony\Component\Mailer\MailerInterface $mailer
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     */
    public function __construct(
        private VerifyEmailHelperInterface $verifyEmailHelper,
        private MailerInterface $mailer,
        private EntityManagerInterface $entityManager
    ) {
    }

    /**
     * Summary of sendEmailConfirmation
     * @param string $verifyEmailRouteName
     * @param \Symfony\Component\Security\Core\User\UserInterface $user
     * @param \Symfony\Bridge\Twig\Mime\TemplatedEmail $email
     * @return void
     */
    public function sendEmailConfirmation(string $verifyEmailRouteName, UserInterface $user, TemplatedEmail $email): void
    {
        if (!$user instanceof User) {
            return;
        }

        $signatureComponents = $this->verifyEmailHelper->generateSignature(
            $verifyEmailRouteName,
            $user->getId(),
            $user->getEmail(),
            ['id' => $user->getId()]
        );

        $context = $email->getContext();
        $context['signedUrl'] = $signatureComponents->getSignedUrl();
        $context['expiresAtMessageKey'] = $signatureComponents->getExpirationMessageKey();
        $context['expiresAtMessageData'] = $signatureComponents->getExpirationMessageData();

        $email->context($context);

        $this->mailer->send($email);
    }

    /**
     * Summary of handleEmailConfirmation
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Symfony\Component\Security\Core\User\UserInterface $user
     * @throws VerifyEmailExceptionInterface
     * @param string|null $role
     * @return void
     */
    public function handleEmailConfirmation(Request $request, UserInterface $user, string|null $role = null): void
    {
        if (!$user instanceof User) {
            return;
        }

        $this->verifyEmailHelper->validateEmailConfirmation($request->getUri(), $user->getId(), $user->getEmail());


        if ($role === null) {
            $user->setIsVerified(true);
        }else if($role === "admin"){
            $user->setRoles(["ROLE_ADMIN"]);
        } 

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
