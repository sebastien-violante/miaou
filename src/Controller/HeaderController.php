<?php

namespace App\Controller;

use App\Entity\Message;
use App\Form\MessageType;
use Symfony\Component\Mime\Email;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HeaderController extends AbstractController
{
    /**
     * @Route("/cgu", name="cgu")
     */
    public function cgu(): Response
    {
        return $this->render('cgu.html.twig');
    }

    /**
     * @Route("/contactus", name="contactus")
     */
    public function contactus(
        Request $request,
        EntityManagerInterface $em,
        MailerInterface $mailer
    ): Response {
        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);
        $contactmail = $form->getData();
        $sender=$contactmail->getEmail();
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($message);
            $em->flush();
            $mail = (new Email())
            ->from($sender)
            ->to('miaou@gmail.com')
            ->subject('Nouveau message')
            ->html('<p>Bonjour, vous venez de recevoir un message de la part d\'un utilisateur de l\'application MIA...OU ? !</p>');
            $mailer->send($mail);
            return $this->redirectToRoute('home');
        }
        return $this->render('contactus/index.html.twig', [
            'form' => $form->createView(),
        ]);
      
    }
}
