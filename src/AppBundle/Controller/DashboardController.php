<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;


class DashboardController extends Controller
{
    /**
     * @Route("/", name="dashboard")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function indexAction()
    {
        $authorization = $this->get('security.authorization_checker');
        if ($authorization->isGranted('ROLE_ADMIN')) {
            return $this->render("AppBundle:Dashboard:index.html.twig");
        } else {
            return $this->createAccessDeniedException('So far, only ROLE_ADMIN has a dashboard');
        }
    }
}