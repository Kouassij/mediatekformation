<?php
// Définition de l'espace de noms pour le contrôleur de l'accueil
namespace App\Controller;

// Importation des classes nécessaires depuis le framework Symfony
use App\Repository\FormationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Contrôleur pour la page d'accueil du site
 *
 * @author Test1
 */
class AccueilController extends AbstractController{
      
    /**
     * @var FormationRepository
     */
    private $repository; // Déclaration de la variable pour le repository des formations
    
    /**
     * Constructeur pour initialiser le repository des formations
     * @param FormationRepository $repository
     */
    public function __construct(FormationRepository $repository) {
        $this->repository = $repository; // Initialisation du repository
    }   
    
       /**
        * @Route("/", name="accueil")
        * @return Response
        // Méthode pour afficher la page d'accueil avec les dernières formations
        */
    public function index(): Response{
        $formations = $this->repository->findAllLasted(2); // Récupère les deux dernières formations
        return $this->render("pages/accueil.html.twig", [
            'formations' => $formations // Envoie les formations à la vue
        ]); 
    }
    
    /**
     * @Route("/cgu", name="cgu")
     * @return Response
     // Méthode pour afficher la page des Conditions Générales d'Utilisation (CGU)
     */
    public function cgu(): Response{
        return $this->render("pages/cgu.html.twig"); // Affiche la page CGU
    }
}