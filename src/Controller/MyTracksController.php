<?php

namespace App\Controller;

use App\Data\SearchBarData;
use App\Entity\Track;
use App\Form\SearchBarType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MyTracksController extends CustomAbsrtactController
{
  const LIMIT_PER_PAGE = 20;

  /**
   * Render the page with the list of tracks
   * @param Request $request
   * @param PaginatorInterface $paginator
   * @return Response
   */
  #[Route('/myPage/myTracks', name: 'app_my_tracks')]
  public function index(Request $request, PaginatorInterface $paginator): Response
  {
    //TODO : use a GET request

    $trackRepository = $this->entityManager->getRepository(Track::class);

    $searchBarData = new SearchBarData();
    $searchForm = $this->createForm(SearchBarType::class, $searchBarData);
    $searchForm->handleRequest($request);

    $query = $trackRepository->paginationFilteredQuery($searchBarData);

    $tracksPagination = $paginator->paginate(
      $query,
      $request->query->getInt('page', 1),
      self::LIMIT_PER_PAGE
    );

    $trackTotal = $tracksPagination->getTotalItemCount();
    $tableHeaderCaption[] = ['wording' => 'Total tracks :', 'data' => number_format($trackTotal, 0, ',', ' ')];

    $response = new Response();
    if ($searchForm->isSubmitted() && $searchForm->isValid()) {
      $response->setStatusCode(Response::HTTP_SEE_OTHER);
    }

    return $this->render(
      'my_tracks/index.html.twig',
      [
        'tracks' => $tracksPagination,
        'pagination' => "1",
        'userPlaycount' => "1",
        'searchBar' => 'date',
        'form' => $searchForm,
        'activeNavbarItem' => $request->get('_route'),
        'myTracksTbodyUrl' => 'my_tracks/tbody.html.twig',
        'myTracksThead' => ['' , 'Title', 'Artist', 'Album', 'Scrobble'],
        'tableHeaderCaption' => $tableHeaderCaption
      ],
      $response
    );
  }
}
