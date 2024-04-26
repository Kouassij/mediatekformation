<?php
namespace App\Controller\admin;

use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class AdminCategoriesController extends AbstractController{
   /**
     * @var FormationRepository
     */
    private $repository;
    
    /**
     * 
     * @param FormationRepository $repository
     */
    public function __construct(FormationRepository $repository) {
        $this->repository = $repository;
    }  
      /**
     * @Route("/admin", name="admin.categories")
     * @return Response
     */
    public function index(): Response{
        $formations = $this->repository->findAllLasted(2);
        return $this->render("pages/categories.html.twig", [
            'formations' => $formations,
            
             
            
        ]); 
    }

    /**
 * @Route("/categories/delete/{id}", name="categories.delete", methods={"POST"})
 */
public function delete($id): Response {
    $categories = $this->categoriesRepository->find($id);
    if ($categories) {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($categories);
        $entityManager->flush();
    }
    
    return $this->redirectToRoute('categories');
}
    
    
    
    /**
 * @Route("/categories/edit/{id}", name="categories.edit", defaults={"id"=null})
 * @param Request $request
 * @param int|null $id
 * @return Response
 */
public function edit(Request $request, $id = null): Response {
    $entityManager = $this->getDoctrine()->getManager();
    $categorieRepository = $entityManager->getRepository(Categorie::class);
    $categories = $categorieRepository->findAll();

    // Création du formulaire d'ajout de catégorie
    $categorie = new Categorie();
    $form = $this->createForm(CategorieType::class, $categorie);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Vérifier si la catégorie existe déjà
        $existingCategorie = $categorieRepository->findOneBy(['nom' => $categorie->getNom()]);
        if (!$existingCategorie) {
            $entityManager->persist($categorie);
            $entityManager->flush();
            $this->addFlash('success', 'Catégorie ajoutée avec succès!');
            return $this->redirectToRoute('categories.edit');
        } else {
            $this->addFlash('error', 'Cette catégorie existe déjà!');
        }
    }

    // Récupérer les catégories pour vérifier les associations avec des formations
    $canDelete = [];
    foreach ($categories as $cat) {
        $formations = $this->repository->findBy(['categorie' => $cat->getId()]);
        $canDelete[$cat->getId()] = count($formations) === 0; // Si aucune formation n'est associée, on peut supprimer
    }

    return $this->render('pages/categories_edit.html.twig', [
        'form' => $form->createView(),
        'categories' => $categories,
        'canDelete' => $canDelete
    ]);
}

 /**
    * @Route("/admin/add", name="admin.categories.add")
    * @param Request $request
    * @return Response
 */
public function add(Request $request): Response {
    if ($request->isMethod('POST')) {
        $name = $request->request->get('name');
        $description = $request->request->get('description', '');
        
        if (empty($name)) {
            $this->addFlash('error', 'Name is required.');
            return $this->redirectToRoute('playlists.add');
        }

        $playlist = new Playlist();
        $playlist->setName($name);
        $playlist->setDescription($description);
        $this->playlistRepository->add($playlist, true);
        
        $this->addFlash('success', 'Playlist added successfully!');
        return $this->redirectToRoute('playlists');
    }
    
    return $this->render("pages/playlist_add.html.twig");
}

}