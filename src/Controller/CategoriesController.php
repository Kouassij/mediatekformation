<?php
// Définition de l'espace de noms pour le contrôleur des catégories
namespace App\Controller;

// Importation des classes nécessaires depuis le framework Symfony et des repositories
use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Description de CategoriesController
 *
 * @author Test1
 */
class CategoriesController extends AbstractController{
   /**
     * @var FormationRepository
     */
    private $formationRepository; // Variable pour le repository des formations
    
    /**
     * @var CategorieRepository
     */
    private $categorieRepository; // Variable pour le repository des catégories
    
    /**
     * Constructeur pour initialiser les repositories des formations et des catégories
     * 
     * @param FormationRepository $formationRepository
     * @param CategorieRepository $categorieRepository
     */
    function __construct(FormationRepository $formationRepository, CategorieRepository $categorieRepository) {
        $this->formationRepository = $formationRepository;
        $this->categorieRepository = $categorieRepository;
    }
      
      /**
       * @Route("/", name="categories")
       * @return Response
       // Méthode pour afficher la page principale des catégories avec les formations récentes
       */
    public function index(): Response{
        $formations = $this->formationRepository->findAllLasted(2); // Récupère les deux dernières formations
        $categories = $this->categorieRepository->findAll(); // Récupère toutes les catégories
        return $this->render("pages/categories.html.twig", [
            'formations' => $formations,
            'categories' => $categories
        ]); 
    }

    /**
     * @Route("/formations/recherche/{champ}/{table}", name="formations.findallcontain")
     * @param type $champ
     * @param Request $request
     * @param type $table
     * @return Response
     // Méthode pour rechercher des formations basées sur un champ spécifique et une valeur donnée
     */
    public function findAllContain($champ, Request $request, $table=""): Response{
        $valeur = $request->get("recherche"); // Récupère la valeur de recherche de la requête
        $formations = $this->formationRepository->findByContainValue($champ, $valeur, $table); // Cherche des formations selon le critère
        $categories = $this->categorieRepository->findAll(); // Récupère toutes les catégories
        return $this->render("pages/categories.html.twig", [
            'formations' => $formations,
            'categories' => $categories,
            'valeur' => $valeur,
            'table' => $table
        ]);
    }

    /**
     * Méthode pour afficher une catégorie spécifique par son ID
     * @param int $id
     * @return Response
     */
    public function showOne($id): Response{
        $categories = $this->categorieRepository->find($id); // Trouve la catégorie par son ID
        return $this->render("pages/categories.html.twig", [
            'categories' => $categories // Envoie la catégorie trouvée à la vue
        ]);        
    }
 
}