<?php

namespace App\Controller\Backend;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Csrf\CsrfToken;

#[Route('/admin/articles', name: 'admin.articles')]
class ArticleController extends AbstractController
{
    public function __construct(
        private ArticleRepository $repo,
        private EntityManagerInterface $em, // pour base de données
    ){
    }

    #[Route('', name: '.index', methods:['GET'])]
    public function index(): Response
    {
        return $this->render('Backend/Article/index.html.twig', [
            'articles' => $this->repo->findAll(),
        ]);
    }

    #[Route('/create', name:'.create',methods: ['GET','POST'])]
    public function create(Request $request): Response|RedirectResponse
    {
        $article = new Article();

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $article->setAuthor($this->getUser());
            
            $this->em->persist($article); // push bdd
            $this->em->flush(); // commit bdd
            
            $this->addFlash('success','Article crée avec succès');

            return $this->redirectToRoute('admin.articles.index');
        }

        return $this->render('Backend/Article/create.html.twig',[
            'form' => $form,
        ]);
    }

    #[Route('/{id}/update', name:'.update',methods:['GET','POST'])]
    public function update(?Article $article, Request $request): Response|RedirectResponse
    {
        if(!$article instanceof Article){
            $this->addFlash('error','Article non trouvé');

            return $this->redirectToRoute('admin.articles.index');
        }

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $this->em->persist($article);
            $this->em->flush();
            $this->addFlash('success','Article modifié avec succès');

            return $this->redirectToRoute('admin.articles.index');
        }

        return $this->render("Backend/Article/update.html.twig", [
            'form' => $form,
            'article' => $article,
        ]);
    }

    #[Route('/{id}/delete', name:'.delete', methods: ['POST'])]
    public function delete(?Article $article, Request $request): RedirectResponse
    {
        if(!$article instanceof Article){
            $this->addFlash('error','Article non trouvé');

            return $this->redirectToRoute('admin.articles.index');
        }
        
        if($this->isCsrfTokenValid('delete'. $article->getId(), $request->request->get('token')))
        {
            $this->em->remove($article);
            $this->em->flush();
            $this->addFlash('succes','Article supprimé avec succès');

            return $this->redirectToRoute('admin.articles.index');
        }

        $this->addFlash('error','Article supprimé avec succès');
        return $this->redirectToRoute('admin.articles.index');
    }

}
