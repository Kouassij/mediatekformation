<?php
// Définition de l'espace de noms pour le contrôleur de connexion
namespace App\Controller;

// Importation des classes nécessaires depuis le framework Symfony
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Contrôleur gérant l'authentification des utilisateurs
 */
class LoginController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     * // Méthode pour afficher la page de connexion
     */
    public function index(): Response
    {
        // Affiche la vue pour la page de connexion et passe le nom du contrôleur à la vue
        return $this->render('login/index.html.twig', [
            'controller_name' => 'LoginController',
        ]);
    }
   
}