<?php

namespace App\Controller;

use App\Entity\Boutique;
use App\Form\BoutiqueType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\BoutiqueRepository;
use Doctrine\ORM\EntityManagerInterface;


class BoutiqueController extends AbstractController
{
    #[Route('/boutique', name: 'app_boutique')]
    public function boutiqueList(ManagerRegistry $doctrine): Response
    {
        $boutiqueRepository = $doctrine->getRepository(Boutique::class);
        $boutiques = $boutiqueRepository->findAll();
        return $this->render('boutique/index.html.twig', [
            'boutiques' => $boutiques,
        ]);
    }

    #[Route('/newboutique', name: 'addboutique')]
    public function newBoutique(Request $request, ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $boutique = new Boutique();
        $form = $this->createForm(BoutiqueType::class, $boutique);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($boutique);
            $em->flush();
            return $this->redirectToRoute('app_boutique');
        }
        
        return $this->render('boutique/boutique.html.twig', [
            'title' => 'Add boutique',
            'form' => $form->createView(),
        ]);
    }

    #[Route('/editb/{id}', name: 'boutique_edit')]
    public function editBoutique($id, Request $request, ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $boutiqueRepository = $doctrine->getRepository(Boutique::class);
        $boutique = $boutiqueRepository->find($id);
        $form = $this->createForm(BoutiqueType::class, $boutique);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('app_boutique');
        }
        
        return $this->render('boutique/boutique.html.twig', [
            'title' => 'Update boutique',
            'form' => $form->createView(), 
        ]);
    }
    #[Route('/boutique/details/{id}', name: 'app_boutique_details')]
public function boutiqueDetails($id, BoutiqueRepository $boutiqueRepository)
{
    $boutique = $boutiqueRepository->find($id);
    return $this->render('boutique/details.html.twig', [
        'boutique' => $boutique
    ]);
}
#[Route('/boutique/delete/{id}', name: 'app_boutique_delete')]
public function deleteBoutique($id, BoutiqueRepository $boutiqueRepository, EntityManagerInterface $em)
{
    $boutique = $boutiqueRepository->find($id);
    if ($boutique) {
        $em->remove($boutique);
        $em->flush();
    }
    return $this->redirectToRoute('app_boutique');
}



}
