<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Form\ReverifyType;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    /**
     * Summary of __construct
     * @param \App\Security\EmailVerifier $emailVerifier
     */
    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    
    /**
     * Summary of register
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface $userPasswordHasher
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     * @return \Symfony\Component\HttpFoundation\Response
     */
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $user->setRoles(["ROLE_NORMAL_USER"]);

            $entityManager->persist($user);
            $entityManager->flush();

            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('matheusviaira160@gmail.com', 'Matheu Vieira'))
                    ->to($user->getEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );
            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    
    /**
     * Summary of verifyUserEmail
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Symfony\Contracts\Translation\TranslatorInterface $translator
     * @param \App\Repository\UserRepository $userRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    #[Route('/verify/email/', name: 'app_verify_email')]
    #[Route('/verify/email/admin', name: 'app_verify_email_admin')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator, UserRepository $userRepository): Response
    {
        $rote_name = $request->attributes->get("_route");
        if ($rote_name == "app_verify_email_admin") {
            $role = "admin";
        } else {
            $role = null;
        }
        $id = $request->query->get('id');

        if (null === $id) {
            return $this->redirectToRoute('app_register');
        }

        $user = $userRepository->find($id);

        if (null === $user) {
            return $this->redirectToRoute('app_register');
        }

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user, $role);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('app_register');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('app_login');
    }

    
    /**
     * Summary of reverify
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \App\Repository\UserRepository $userRepository
     * @return Response|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    #[Route("/send-reverify", "app_reverify")]
    public function reverify(Request $request, UserRepository $userRepository) {
        if ($this->getUser()) {
            return $this->redirectToRoute("app_home_index");
        }

        $form = $this->createForm(ReverifyType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $userRepository->findOneBy(["email" => $form->get("email")->getData()]);
            if ($user) {
                $this->emailVerifier->sendEmailConfirmation(
                    "app_verify_email",
                    $user,
                    (new TemplatedEmail())
                    ->from(new Address("matheusviaira160@gmail.com"))
                    ->to($user->getEmail())
                    ->subject("Matheus Vieira")
                    ->htmlTemplate('registration/confirmation_email.html.twig')
                );
                return $this->redirectToRoute("app_login");
            } else {
                $this->addFlash("error", "Incorrect Email");
            }
        }
        return $this->render("registration/reverify.html.twig", [
            "reverifyForm" => $form
        ]);
    }


    /**
     * Summary of ChargeToAdmin
     * @param \Symfony\Component\Security\Core\User\UserInterface $user
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     * @return void
     */
    #[Route(path: "/charge-to-admin", name: "app_charge_to_admin")]
    public function ChargeToAdmin(UserInterface $user, EntityManagerInterface $entityManager): Response
    {
        
        if (!$user instanceof User) {
            dd("barriu boy, + deu error!!");
            return new Response("success");
        }

        $this->emailVerifier->sendEmailConfirmation(
            "app_verify_email_admin",
            $user,
            (new TemplatedEmail())
            ->from(new Address("matheusviaira160@gmail.com"))
            ->to("matheusviaira160@gmail.com")
            ->subject("Matheus Vieira")
            ->htmlTemplate('registration/confirmation_email.html.twig')
        );
        return new Response("success");
    }
}
