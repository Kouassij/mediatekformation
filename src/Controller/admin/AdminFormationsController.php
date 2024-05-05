<?php
// Définit l'espace de noms pour ce contrôleur, situé sous le dossier `admin`
namespace App\Controller\admin;

// Importe les classes nécessaires du framework Symfony et les repositories utilisés
use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Contrôleur pour gérer les formations depuis le panneau d'administration
 *
 * @author Test1
 */
class AdminFormationsController extends AbstractController {

    /**
     * @var FormationRepository
     //Repository pour les opérations CRUD sur les entités Formation
     */
    private $formationRepository;
    
    /**
     * @var CategorieRepository
     //Repository pour les opérations CRUD sur les entités Catégorie
     */
    private $categorieRepository;
    
    // Constructeur pour injecter les dépendances nécessaires aux repositories
    function __construct(FormationRepository $formationRepository, CategorieRepository $categorieRepository) {
        $this->formationRepository = $formationRepository;
        $this->categorieRepository = $categorieRepository;
    }
    
    /**
     * @Route("/admin", name="admin.formations")
     * @return Response
     // Méthode pour afficher toutes les formations et catégories sur la page d'administration
     */
    public function index(): Response{
        $formations = $this->formationRepository->findAll(); // Récupère toutes les formations
        $categories = $this->categorieRepository->findAll(); // Récupère toutes les catégories
        return $this->render("admin/admin.formations.html.twig", [
            'formations' => $formations,
            'categories' => $categories
        ]);
    }
  
    /**
     * @Route("/formations/delete/{id}", name="formations.delete", methods={"POST"})
     // Méthode pour supprimer une formation spécifique par ID
     */
    public function delete($id): Response {
        $formation = $this->formationRepository->find($id); // Trouve la formation par son ID
        if ($formation) {
            $entityManager = $this->getDoctrine()->getManager(); // Obtient le gestionnaire d'entité
            $entityManager->remove($formation); // Supprime la formation de la base de données
            $entityManager->flush(); // Applique les changements à la base de données
        }
        
        return $this->redirectToRoute('admin.formations'); // Redirige vers la page des formations après la suppression
    }
  
    /**
     * @Route("/formations/edit/{id?}", name="formations.edit")
     // Méthode pour éditer une formation, nouvelle ou existante
     */
    public function edit(Request $request, $id = null): Response {
        $formation = $id ? $this->formationRepository->find($id) : new Formation(); // Charge une formation existante ou crée une nouvelle instance
        $form = $this->createForm(FormationType::class, $formation); // Crée un formulaire pour la formation
        $form->handleRequest($request); // Gère la soumission du formulaire

        if ($form->isSubmitted() && $form->isValid()) { // Vérifie si le formulaire est soumis et valide
            $entityManager = $this->getDoctrine()->getManager(); // Obtient le gestionnaire d'entité
            $entityManager->persist($formation); // Sauvegarde ou met à jour la formation
            $entityManager->flush(); // Applique les changements à la base de données
            
            return $this->redirectToRoute('admin.formations'); // Redirige vers la liste des formations après l'édition
        }

        return $this->render('admin/admin.formations.edit.html.twig', [
            'form' => $form->createView() // Rendu du formulaire sur la page
        ]);
    }

    /**
     * @Route("/admin/add", name="admin.formations.add")
     * @param Request $request
     * @return Response
     // Contrôleur pour ajouter une nouvelle formation
     */
    public function add(Request $request): Response{
        $formation = new Formation(); // Crée une nouvelle instance de Formation
        $formFormation = $this->createForm(FormationType::class, $formation); // Crée un formulaire pour la nouvelle formation

        $formFormation->handleRequest($request); // Gère la soumission du formulaire
        if($formFormation->isSubmitted() && $formFormation->isValid()){ // Vérifie si le formulaire est soumis et valide
            $this->formationRepository->add($formation, true); // Ajoute la nouvelle formation à la base de données
            return $this->redirectToRoute('admin.formations'); // Redirige vers la liste des formations
        }     

        return $this->render("admin/admin.formations.add.html.twig", [
            'formation' => $formation, // Passe la formation à la vue
            'formformation' => $formFormation->createView() // Rendu du formulaire
        ]);        
    }    
}