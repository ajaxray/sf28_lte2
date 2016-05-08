<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;


class UtilityController extends Controller
{
    /**
     * @Route("/util/dump", name="util_dump")
     */
    public function dumpAction()
    {
        return new Response($this->getUser()->getRole());
    }

    /**
     * @Route("/util/test-email", name="util_test_email")
     */
    public function testEmailAction()
    {
        $message = \Swift_Message::newInstance()
                                 ->setSubject('Test email')
                                 ->setFrom([$this->container->getParameter('mailer_sender_email') => $this->container->getParameter('mailer_sender')])
                                 ->setTo($this->getUser()->getEmail())
                                 ->setBody(
                                     $this->renderView(
                                         'AppBundle:Email:test.html.twig'
                                     ),
                                     'text/html'
                                 );
        $this->get('mailer')->send($message);
        return new Response('Mail sent!');
    }
}