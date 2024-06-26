<?php
namespace App\Controller\admin;

use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use App\Repository\PlaylistRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Description of PlaylistsController
 *
 * @author Test1
 */
class AdminPlaylistsController extends AbstractController {
    
    /**
     * 
     * @var PlaylistRepository
     */
    private $playlistRepository;
    
    /**
     * 
     * @var FormationRepository
     */
    private $formationRepository;
    
    /**
     * 
     * @var CategorieRepository
     */
    private $categorieRepository;    
    
    function __construct(PlaylistRepository $playlistRepository, 
            CategorieRepository $categorieRepository,
            FormationRepository $formationRespository) {
        $this->playlistRepository = $playlistRepository;
        $this->categorieRepository = $categorieRepository;
        $this->formationRepository = $formationRespository;
    }
    
    /**
     * @Route("/admin", name="admin.playlists")
     
     * @return Response
     */
  public function index(): Response {
    $playlists = $this->playlistRepository->findAllWithFormationCount('ASC');
    $categories = $this->categorieRepository->findAll();
    return $this->render("pages/playlists.html.twig", [
        'playlists' => $playlists,
        'categories' => $categories
    ]);
}


    /**
     * @Route("/playlists/tri/{champ}/{ordre}", name="playlists.sort")
     * @param type $champ
     * @param type $ordre
     * @return Response
     */
    public function sort($champ, $ordre): Response {
    switch ($champ) {
        case "name":
            $playlists = $this->playlistRepository->findAllOrderByName($ordre);
            break;
        case "formationCount":
            $playlists = $this->playlistRepository->findAllWithFormationCount($ordre);
            break;
    }
    $categories = $this->categorieRepository->findAll();
    return $this->render("pages/playlists.html.twig", [
        'playlists' => $playlists,
        'categories' => $categories
    ]);
}

        
/**
    * @Route("/admin/add", name="admin.playlists.add")
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

/**
 * @Route("/playlists/delete/{id}", name="playlists.delete")
 */
public function delete($id): Response {
    $playlist = $this->playlistRepository->find($id);
    if (!$playlist) {
        $this->addFlash('error', 'Playlist not found.');
        return $this->redirectToRoute('playlists');
    }

    if (!$playlist->getFormations()->isEmpty()) {
        $this->addFlash('error', 'Playlist cannot be deleted as it has associated formations.');
        return $this->redirectToRoute('playlists');
    }

    $this->playlistRepository->remove($playlist, true);
    $this->addFlash('success', 'Playlist deleted successfully.');
    return $this->redirectToRoute('playlists');
}

/**
 * @Route("/playlists/edit/{id}", name="playlists.edit")
 */
public function edit($id, Request $request): Response {
    $playlist = $this->playlistRepository->find($id);
    if (!$playlist) {
        $this->addFlash('error', 'Playlist not found.');
        return $this->redirectToRoute('playlists');
    }

    if ($request->isMethod('POST')) {
        $playlist->setName($request->request->get('name', $playlist->getName()));
        $playlist->setDescription($request->request->get('description', $playlist->getDescription()));
        $this->playlistRepository->add($playlist, true);
        
        $this->addFlash('success', 'Playlist updated successfully.');
        return $this->redirectToRoute('playlists');
    }

    return $this->render("pages/playlist_edit.html.twig", [
        'playlist' => $playlist
    ]);
}

}
