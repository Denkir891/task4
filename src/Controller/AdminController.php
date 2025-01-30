<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class AdminController extends AbstractController
{
    #[Route(path: '/admin', name: 'admin_home')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $users = $entityManager->getRepository(User::class)->findBy([], ['LastLogin' => 'DESC']);
        return $this->render('admin/index.html.twig', [
            'users' => $users,
        ]);
    }

    // Bloquear usuarios
    #[Route('/admin/block', name: 'admin_block', methods: ['POST'])]
    public function blockUsers(Request $request, EntityManagerInterface $entityManager, Security $security, TokenStorageInterface $tokenStorage): Response
    {
        $data = json_decode($request->getContent(), true);
        $ids = $data['userIds'] ?? [];

        if (!empty($ids)) {
            $currentUser = $security->getUser(); // Obtener el usuario actualmente logueado

            if ($currentUser instanceof User) {
                $users = $entityManager->getRepository(User::class)->findBy(['id' => $ids]);

                // Verificar si el usuario está intentando bloquearse a sí mismo
                foreach ($users as $user) {
                    if ($user->getId() === $currentUser->getId()) {
                        // Si el usuario se está bloqueando a sí mismo
                        // Cambiar su estado a bloqueado
                        $user->setStatus('blocked');

                        // Desloguear al usuario y redirigir al login
                        $entityManager->flush(); // Guardamos los cambios de bloqueo

                        // Desloguear al usuario
                        $tokenStorage->setToken(null); // Borrar el token de sesión

                        // Redirigir al login
                        return $this->redirectToRoute('app_login');
                    }

                    // Si no es el mismo, simplemente bloqueamos al usuario
                    $user->setStatus('blocked');
                }

                // Guardar los cambios en la base de datos para los demás usuarios
                $entityManager->flush();
                $this->addFlash('success', 'Selected users have been blocked successfully.');
            }
        }

        return new Response('', Response::HTTP_OK);
    }

    // Desbloquear usuarios
    #[Route('/admin/unblock', name: 'admin_unblock', methods: ['POST'])]
    public function unblockUsers(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);
        $ids = $data['userIds'] ?? [];

        if (!empty($ids)) {
            $users = $entityManager->getRepository(User::class)->findBy(['id' => $ids]);
            foreach ($users as $user) {
                $user->setStatus('active');
            }
            $entityManager->flush();
            $this->addFlash('success', 'Selected users have been unblocked successfully.');
        }

        return new Response('', Response::HTTP_OK);
    }

    // Eliminar usuarios
    #[Route('/admin/delete', name: 'admin_delete', methods: ['POST'])]
    public function deleteUsers(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);
        $ids = $data['userIds'] ?? [];

        if (!empty($ids)) {
            $users = $entityManager->getRepository(User::class)->findBy(['id' => $ids]);
            foreach ($users as $user) {
                $entityManager->remove($user);
            }
            $entityManager->flush();
            $this->addFlash('success', 'Selected users have been deleted successfully.');
        }

        return new Response('', Response::HTTP_OK);
    }
}







