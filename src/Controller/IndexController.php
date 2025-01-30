<?php
// src/Controller/IndexController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;

class IndexController extends AbstractController
{
    #[Route(path: '/', name: 'app_home')]
    public function index(): RedirectResponse
    {
        // Redirige automáticamente al login cuando accedes a la raíz
        return $this->redirectToRoute('app_login');
    }
}
