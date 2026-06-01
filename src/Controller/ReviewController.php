<?php

namespace App\Controller;

use App\Entity\Review;
use App\Form\ReviewType;
use App\Repository\ReviewRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ReviewController extends AbstractController
{
    // Miért Dependency Injection? A modern Symfonyban a konstruktorban kérjük el a szükséges service-eket.
    public function __construct(
        private ReviewRepository $reviewRepository,
        private EntityManagerInterface $entityManager,
    ) {
    }

    #[Route('/', name: 'app_home', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        $searchQuery = $request->query->get('q');

        // Új vélemény kezelése (2.1)
        $review = new Review();
        $form = $this->createForm(ReviewType::class, $review);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($review);
            $this->entityManager->flush();

            $this->addFlash('success', 'Köszönjük a véleményed!');

            // Miért PRG (Post-Redirect-Get) minta? Megakadályozza az űrlap duplikált beküldését F5-re.
            return $this->redirectToRoute('app_home');
        }

        return $this->render('review/index.html.twig', [
            'reviews' => $this->reviewRepository->findLatestReviews($searchQuery),
            'form' => $form->createView(),
            'searchQuery' => $searchQuery,
        ]);
    }

    #[Route('/review/{id}', name: 'app_review_show', methods: ['GET'])]
    public function show(Review $review): Response
    {
        // Miért ParamConverter? A Symfony automatikusan kikeresi a Review-t az ID alapján,
        // ha nem találja, 404-et dob. Clean és elegáns.
        return $this->render('review/show.html.twig', [
            'review' => $review,
        ]);
    }

    #[Route('/companies', name: 'app_companies', methods: ['GET'])]
    public function companies(Request $request): Response
    {
        $searchQuery = $request->query->get('q');

        return $this->render('review/companies.html.twig', [
            'companies' => $this->reviewRepository->getCompanyStatistics($searchQuery),
            'searchQuery' => $searchQuery,
        ]);
    }
}
