<?php

namespace App\Controller;

use App\Entity\Film;
use App\Form\FilmType;
use App\Repository\FilmRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\OmdbApiService;

class FilmController extends AbstractController
{
    #[Route('/film', name: 'app_film_index', methods: ['GET'])]
    public function index(FilmRepository $filmRepository): Response
    {
        return $this->render('film/index.html.twig', [
            'films' => $filmRepository->findAll(),
        ]);
    }
    #[Route('/', name: 'app_film_inde', methods: ['GET'])]
    public function inde(FilmRepository $filmRepository): Response
    {
        return $this->render('film/index.html.twig', [
            'films' => $filmRepository->findAll(),
        ]);
    }

    #[Route('/film/new', name: 'app_film_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $film = new Film();
        $form = $this->createForm(FilmType::class, $film);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($film);
            $entityManager->flush();

            return $this->redirectToRoute('app_film_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('film/new.html.twig', [
            'film' => $film,
            'form' => $form,
        ]);
    }

    #[Route('/film/{id}', name: 'app_film_show', methods: ['GET'])]
    public function show(int $id, FilmRepository $filmRepository): Response
    {
        $film = $filmRepository->find($id);
    
        
        if (!$film) {
            throw $this->createNotFoundException('Le film demandé n\'existe pas.');
        }
    
        return $this->render('film/show.html.twig', [
            'film' => $film,
        ]);
    }
    

    #[Route('/film/{id}/edit', name: 'app_film_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Film $film, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FilmType::class, $film);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('delete')->isClicked()) {
                $entityManager->remove($film);
                $entityManager->flush();
    
                    return $this->redirectToRoute('app_film_index');
            }
            $entityManager->persist($film);
            $entityManager->flush();

            return $this->redirectToRoute('app_film_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('film/edit.html.twig', [
            'film' => $film,
            'form' => $form,
        ]);
    }

    #[Route('/film/{id}', name: 'app_film_delete', methods: ['POST'])]
    public function delete(Request $request, Film $film, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$film->getId(), $request->request->get('_token'))) {
            $entityManager->remove($film);
            $entityManager->flush();
        }
        return $this->redirectToRoute('app_film_index', [], Response::HTTP_SEE_OTHER);
    }
    
    private $omdbApiService;

    public function __construct(OmdbApiService $omdbApiService)
    {
        $this->omdbApiService = $omdbApiService;
    }

    #[Route('/film/{title}', name: 'film_show_by_title')]
    public function showByTitle(string $title): Response
    {
        $movieData = $this->omdbApiService->fetchMovieData($title);

        return $this->render('film/show.html.twig', [
            'movie' => $movieData,
        ]);
    }
    
    #[Route('/search', name: 'app_film_search', methods: ['GET', 'POST'])]
    public function search(Request $request, EntityManagerInterface $entityManager): Response
    {
       try {
         $title = $request->query->get('title');
        $movieData = null;
    
        if ($title) {
            $movieData = $this->omdbApiService->fetchMovieData($title);
        }
    
        // Créer le formulaire FilmType pour l'ajout de films
        $film = new Film();
        $form = $this->createForm(FilmType::class, $film);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($film);
            $entityManager->flush();
    
            $this->addFlash('success', 'Film ajouté avec succès');
            return $this->redirectToRoute('app_film_index');
        }
    
        // Renvoyer les données de la recherche et le formulaire
        return $this->render('film/search.html.twig', [
            'movie' => $movieData,
            'title' => $title,
            'form' => $form->createView(), // Passer le formulaire à la vue
        ]);
       } catch (\Throwable $th) {
        $movieData= null;
        $title = null; 
        // en cas d'erreur redirection ver la page error
      return $this->render('film/error.html.twig', [
            'movie' => $movieData,
            'title' => $title,
            'form' => $form->createView(), // Passer le formulaire à la vue
        ]);
       }
    }

  

    


}
    