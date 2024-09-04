<?php

namespace App\Controller;

use App\Entity\Film;
use App\Form\FilmType;
use App\Repository\FilmRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
    public function show(Film $film): Response
    {
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
    public function search(Request $request): Response
    {
      try {
        $title = $request->query->get('title');
        $movieData = null;

        if ($title) {
            $movieData = $this->omdbApiService->fetchMovieData($title);
        }else{
            
            $movieData = null;
            return $this->render('film/search.html.twig', [
                'movie' => $movieData,
                'title' => $title,
            ]);
        }

        return $this->render('film/search.html.twig', [
            'movie' => $movieData,
            'title' => $title,
        ]);
      } catch (\Throwable $th) {
        $movieData = null;
        return $this->render('film/search.html.twig', [
            'movie' => $movieData,
            'title' => $title,
        ]);
      }
    }

}
    