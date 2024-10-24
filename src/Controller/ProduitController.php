<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

class ProduitController extends AbstractController
{
    #[Route('/produit', name: 'app_produit')]
    public function produitList(ManagerRegistry $doctrine): Response
    {
        $produitRepository = $doctrine->getRepository(Produit::class);
        $produits = $produitRepository->findAll();
        return $this->render('produit/index.html.twig', [
            'produits' => $produits,
        ]);
    }
    #[Route('/newproduit', name: 'addproduit')]
    public function newproduit(Request $request, ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($produit);
            $em->flush();
            return $this->redirectToRoute('app_produit');
        }
        
        return $this->render('produit/produit.html.twig', [
            'title' => 'Add produit',
            'form' => $form->createView(),
        ]);
    }
    #[Route('/editp/{id}', name: 'produit_edit')]
    public function editproduit($id, Request $request, ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $produitRepository = $doctrine->getRepository(Produit::class);
        $produit = $produitRepository->find($id);
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('app_produit');
        }
        
        return $this->render('produit/produit.html.twig', [
            'title' => 'Update produit',
            'form' => $form->createView(), 
        ]);
    }
    #[Route('/produit/details/{id}', name: 'app_produit_details')]
    public function boutiqueDetails($id, ProduitRepository $produitRepository)
    {
        $produit = $produitRepository->find($id);
        return $this->render('produit/details.html.twig', [
            'produit' => $produit
        ]);
    }
    #[Route('/produit/delete/{id}', name: 'app_produit_delete')]
public function deleteBoutique($id, ProduitRepository $produitRepository, EntityManagerInterface $em)
{
    $produit = $produitRepository->find($id);
    if ($produit) {
        $em->remove($produit);
        $em->flush();
    }
    return $this->redirectToRoute('app_produit');
}
}