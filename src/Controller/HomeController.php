<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/')]
class HomeController extends AbstractController
{
    public function __invoke()
    {
        return $this->render('index.html.twig');
    }
}
