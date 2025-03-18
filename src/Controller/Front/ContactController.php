<?php

namespace App\Controller\Front;

use App\Form\ContactFormType;
use App\Service\SendMailService;
use Symfony\Component\Mime\Email;
use App\Service\HeaderDataProvider;
use App\Repository\HomePageRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContactController extends AbstractController
{
    public function __construct(
        private HomePageRepository $homePage, 
        private HeaderDataProvider $headerDataProvider,
        private MailerInterface $mailer,

    )
    {

    }

    #[Route('/feane/contact', name: 'app_contact')]
    public function index(Request $request): Response
    {
        $homePages = $this->homePage->findAll();
        $headerData = $this->headerDataProvider->getHeaderData();

        $form = $this->createForm(ContactFormType::class);
        $form->handleRequest($request);

        $messageSuccess = 'Votre e-mail a bien été envoyé, merci de nous avoir contacté';

        if ($form->isSubmitted() && $form->isValid()) {
            //$from = $form->get('email')->getData();
            $from = $this->getUser();
            $name = $form->get('name')->getData();
            $phone = $form->get('phone')->getData();
            $subject = $form->get('subject')->getData();
            $message = $form->get('message')->getData();
            $messageText = strip_tags($message);

            // Créer le contenu du message
            $messageContent = "Nom : $name\n";
            $messageContent .= "Téléphone : $phone\n";
            $messageContent .= "Message :\n$messageText";
        
            $email = (new Email())
                ->from('lnomenjanahary68@gmail.com')
                ->to('anjaralynah1@gmail.com')
                ->subject($subject)
                ->text($messageContent)
                ->replyTo($from); 
        
            // Envoyer le mail
            $this->mailer->send($email);
        
            $this->addFlash(
                'success',
                $messageSuccess
            );

          return  $this->redirectToRoute('app_contact');
        }

        $data = array_merge($headerData, [
            'homePages' => $homePages,
            'form' => $form->createView()
        ]);

        return $this->render('front/contact/index.html.twig', $data);
    }
}
