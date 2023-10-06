<?php

namespace App\Controller\Backend;

use COM;
use App\Entity\Categorie;
use App\Form\CategorieType;
use PhpParser\Node\Stmt\Catch_;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/admin/categories', name: 'admin.categories')]
class CategorieController extends AbstractController
{
    public function __construct(
        private CategorieRepository $repo,
        private EntityManagerInterface $em, // pour base de données
    )
    {
        
    }

    #[Route('', name: '.index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('Backend/Categorie/index.html.twig', [
            'categories' => $this->repo->findAll(),
        ]);
    }

    #[Route('/create', name:'.create',methods: ['GET','POST'])]
    public function create(Request $request): Response|RedirectResponse
    {
        $categorie = new Categorie();

        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $this->em->persist($categorie); // push bdd
            $this->em->flush(); // commit bdd
            
            $this->addFlash('success','Catégorie crée avec succès');

            return $this->redirectToRoute('admin.categories.index');
        }

        return $this->render('Backend/Categorie/create.html.twig',[
            'form' => $form,
        ]);
    }

    #[Route('/{id}/update', name:'.update',methods:['GET','POST'])]
    public function update(?Categorie $categorie, Request $request): Response|RedirectResponse
    {
        if(!$categorie instanceof Categorie){
            $this->addFlash('error','Catégorie non trouvé');

            return $this->redirectToRoute('admin.categories.index');
        }

        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $this->em->persist($categorie);
            $this->em->flush();
            $this->addFlash('success','Catégorie modifié avec succès');

            return $this->redirectToRoute('admin.categories.index');
        }

        return $this->render("Backend/Categorie/update.html.twig", [
            'form' => $form,
            'categorie' => $categorie,
        ]);
    }

    #[Route('/{id}/delete', name:'.delete', methods: ['POST'])]
    public function delete(?Categorie $categorie, Request $request): RedirectResponse
    {
        if(!$categorie instanceof Categorie){
            $this->addFlash('error','Catégorie non trouvé');

            return $this->redirectToRoute('admin.categories.index');
        }
        
        if($this->isCsrfTokenValid('delete'. $categorie->getId(), $request->request->get('token')))
        {
            $this->em->remove($categorie);
            $this->em->flush();
            $this->addFlash('success','Catégorie supprimé avec succès');

            return $this->redirectToRoute('admin.categories.index');
        }

        $this->addFlash('error','Categorie supprimé avec succès');
        return $this->redirectToRoute('admin.categories.index');
    }

    #[Route('/{id}/visibility', name: '.switch', methods: ['GET'])]
    public function switch(?Categorie $categorie): JsonResponse
    {
        if(!$categorie instanceof Categorie){
            return new JsonResponse([
                'status' => 'Error',
                'message' => 'Catégorie non trouvée'
            ],404);
        }

        $categorie->setEnabled(!$categorie->isEnabled());

        $this->em->persist($categorie);
        $this->em->flush();

        return new JsonResponse([
            'status' => 'Success',
            'message' => 'Catégorie mise à jour avec succès',
            'visibility' => $categorie->isEnabled(),
        ]);
    }
}
