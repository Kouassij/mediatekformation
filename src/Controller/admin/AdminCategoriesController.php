<?php
// Espace de nom pour le contrôleur d'administration
namespace App\Controller\admin;

// Importations nécessaires pour la gestion des repositories et des réponses HTTP
use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// Définition de la classe du contrôleur pour les catégories d'administration
class AdminCategoriesController extends AbstractController{
   /**
     * @var FormationRepository
     */
    private $repository; // Déclaration de la variable pour le repository des formations
    
    /**
     * @param FormationRepository $repository
     */
    public function __construct(FormationRepository $repository) {
        $this->repository = $repository; // Initialisation du repository des formations
    }  
      /**
     * @Route("/admin", name="admin.categories")
     * @return Response
     */
    public function index(): Response{
        $formations = $this->repository->findAllLasted(2); // Récupère les 2 dernières formations
        return $this->render("pages/categories.html.twig", [
            'formations' => $formations, // Passe les formations à la vue
        ]); 
    }

    /**
     * @Route("/categories/delete/{id}", name="categories.delete", methods={"POST"})
     */
    public function delete($id): Response {
        $categories = $this->categoriesRepository->find($id); // Trouve la catégorie par son ID
        if ($categories) {
            $entityManager = $this->getDoctrine()->getManager(); // Obtient le gestionnaire d'entité
            $entityManager->remove($categories); // Supprime l'entité de la catégorie
            $entityManager->flush(); // Persiste les changements dans la base de données
        }
        
        return $this->redirectToRoute('categories'); // Redirige vers la liste des catégories
    }
    
    /**
     * @Route("/categories/edit/{id}", name="categories.edit", defaults={"id"=null})
     * @param Request $request
     * @param int|null $id
     * @return Response
     */
    public function edit(Request $request, $id = null): Response {
        $entityManager = $this->getDoctrine()->getManager(); // Obtient le gestionnaire d'entité
        $categorieRepository = $entityManager->getRepository(Categorie::class); // Obtient le repository des catégories
        $categories = $categorieRepository->findAll(); // Récupère toutes les catégories

        // Création du formulaire d'ajout de catégorie
        $categorie = new Categorie();
        $form = $this->createForm(CategorieType::class, $categorie); // Crée un formulaire pour les catégories
        $form->handleRequest($request); // Gère la requête du formulaire

        if ($form->isSubmitted() && $form->isValid()) { // Vérifie si le formulaire est soumis et valide
            // Vérifier si la catégorie existe déjà
            $existingCategorie = $categorieRepository->findOneBy(['nom' => $categorie->getNom()]); // Recherche une catégorie existante par nom
            if (!$existingCategorie) {
                $entityManager->persist($categorie); // Persiste la nouvelle catégorie
                $entityManager->flush(); // Sauvegarde les changements
                $this->addFlash('success', 'Catégorie ajoutée avec succès!'); // Ajoute un message de succès
                return $this->redirectToRoute('categories.edit'); // Redirige vers l'édition des catégories
            } else {
                $this->addFlash('error', 'Cette catégorie existe déjà!'); // Ajoute un message d'erreur si la catégorie existe déjà
            }
        }

        // Récupérer les catégories pour vérifier les associations avec des formations
        $canDelete = [];
        foreach ($categories as $cat) {
            $formations = $this->repository->findBy(['categorie' => $cat->getId()]); // Trouve les formations associées à chaque catégorie
            $canDelete[$cat->getId()] = count($formations) === 0; // Détermine si la catégorie peut être supprimée (aucune formation associée)
        }

        return $this->render('pages/categories_edit.html.twig', [
            'form' => $form->createView(), // Affiche le formulaire
            'categories' => $categories, // Affiche les catégories
            'canDelete' => $canDelete // Affiche les possibilités de suppression
        ]);
    }

    /**
     * @Route("/admin/add", name="admin.categories.add")
     * @param Request $request
     * @return Response
     */
    public function add(Request $request): Response {
        if ($request->isMethod('POST')) { // Vérifie si la requête est une méthode POST
            $name = $request->request->get('name'); // Récupère le nom de la catégorie depuis le formulaire
            $description = $request->request->get('description', ''); // Récupère la description, si fournie
            
            if (empty($name)) {
                $this->addFlash('error', 'Name is required.'); // Ajoute un message d'erreur si le nom est vide
                return $this->redirectToRoute('playlists.add'); // Redirige vers l'ajout de playlist
            }

            $playlist = new Playlist();
            $playlist->setName($name); // Définit le nom de la playlist
            $playlist->setDescription($description); // Définit la description de la playlist
            $this->playlistRepository->add($playlist, true); // Ajoute la playlist à la base de données
            
            $this->addFlash('success', 'Playlist added successfully!'); // Ajoute un message de succès
            return $this->redirectToRoute('playlists'); // Redirige vers la liste des playlists
        }
        
        return $this->render("pages/playlist_add.html.twig"); // Affiche la page d'ajout de playlist
    }
}