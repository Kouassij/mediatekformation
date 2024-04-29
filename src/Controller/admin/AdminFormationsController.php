<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace App\Controller\admin;

use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controleur des formations
 *
 * @author Test1
 */
class AdminFormationsController extends AbstractController {

    /**
     * 
     * @var formationRepository
     //Repository utilisé pour les opérations sur les entités Formation.
     */
    private $formationRepository;
    
   
    function __construct(FormationRepository $formationRepository, CategorieRepository $categorieRepository) {
        $this->formationRepository = $formationRepository;
        $this->categorieRepository= $categorieRepository;
    }
    
    /**
     * @Route("/admin", name="admin.formations")
     * @return Response
     */
    public function index(): Response{
        $formations = $this->formationRepository->findAll();
        $categories = $this->categorieRepository->findAll();
        return $this->render("admin/admin.formations.html.twig", [
            'formations' => $formations,
            'categories' => $categories
        ]);
    }
  
 /**
 * @Route("/formations/delete/{id}", name="formations.delete", methods={"POST"})
 */
public function delete($id): Response {
    $formation = $this->formationRepository->find($id);
    if ($formation) {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($formation);
        $entityManager->flush();
    }
    
    return $this->redirectToRoute('admin.formations');
}
  /**
 * @Route("/formations/edit/{id?}", name="formations.edit")
 */
public function edit(Request $request, $id = null): Response {
    $formation = $id ? $this->formationRepository->find($id) : new Formation();
    $form = $this->createForm(FormationType::class, $formation);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($formation);
        $entityManager->flush();
        
        return $this->redirectToRoute('admin.formations');
    }

    return $this->render('admin/admin.formations.edit.html.twig', [
        'form' => $form->createView()
    ]);
}
 /**
    * @Route("/admin/add", name="admin.formations.add")
    * @param Request $request
    * @return Response
  // Ce contrôleur permet de lister, ajouter, modifier et supprimer des formations.
 */
   

public function add(Request $request): Response{
        $formation = new Formation();
        $formFormation = $this->createForm(FormationType::class, $formation);

        $formFormation->handleRequest($request);
        if($formFormation->isSubmitted() && $formFormation->isValid()){
            $this->repository->add($formation, true);
            return $this->redirectToRoute('admin.formation');
        }     

        return $this->render("admin/admin.formations.add.html.twig", [
            'formation' => $formation,
            'formformation' => $formFormation->createView()
        ]);        
    }    
}

