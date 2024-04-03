<?php

namespace App\Controller;

use App\Data\SearchBarData;
use App\Entity\LovedTrack;
use App\Entity\Scrobble;
use App\Entity\Track;
use App\Entity\User;
use App\Form\SearchBarType;
use Knp\Component\Pager\PaginatorInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MyScrobblesController extends CustomAbsrtactController
{
  const LIMIT_PER_PAGE = 20;

  /**
   * My Scrobbles page rendering
   * @param Request $request
   * @param PaginatorInterface $paginator
   * @return Response
   */
  #[Route('/myPage/myScrobbles', name: 'app_my_scrobbles')]
  public function index(Request $request, PaginatorInterface $paginator): Response
  {
    //TODO : use a GET request

    $response = new Response();
    $view = 'my_scrobbles/index.html.twig';

    $scrobbleRepository = $this->entityManager->getRepository(Scrobble::class);

    $searchBarData = new SearchBarData();
    $searchForm = $this->createForm(SearchBarType::class, $searchBarData);
    $searchForm->handleRequest($request);

    $query = $scrobbleRepository->paginationFilteredQuery($searchBarData);
    $scrobblePagination = $paginator->paginate(
      $query,
      $request->query->getInt('page', 1),
      self::LIMIT_PER_PAGE
    );

    $scrobbleTotal = $scrobblePagination->getTotalItemCount();
    $tableHeaderCaption[] = ['wording' => 'Total scrobbles :', 'data' => number_format($scrobbleTotal, 0, ',', ' ')];

    $paramView = [
      'scrobbles' => $scrobblePagination,
      'form' => $searchForm,
      'pagination' => 1,
      'searchBar' => 'full',
      'activeNavbarItem' => $request->get('_route'),
      'myScrobblesTbodyUrl' => 'my_scrobbles/tbody.html.twig',
      'myScrobblesThead' => ['' , 'Title', 'Artist', 'Album', 'Date'],
      'tableHeaderCaption' => $tableHeaderCaption
    ];

    if ($searchForm->isSubmitted() && $searchForm->isValid()) {
      $response->setStatusCode(Response::HTTP_SEE_OTHER);
    } else {
      $response = null;
    }

    return $this->render($view, $paramView, $response);
  }


  /**
   * Love or unlove a track (API incoming call)
   * @param Request $request
   * @param LoggerInterface $logger
   * @param $id
   * @return Response
   */
  #[Route('/myPage/myScrobbles/love/{id}', name: 'app_my_scrobbles_love')]
  public function loveTrack(Request $request, LoggerInterface $logger, $id): Response
  {
    $response = new Response();
    $loved = false;

    try {

      $lovedTrackRepository = $this->entityManager->getRepository(LovedTrack::class);
      $loved = $lovedTrackRepository->isLoved($id, $this->getUser()->getId());

      $trackRepository = $this->entityManager->getRepository(Track::class);
      $track = $trackRepository->find($request->get('id'));

      if (!$track) {
        throw new \Exception('Track not found');
      }

      if ($loved){
        $lovedTrack = $lovedTrackRepository->findOneBy(['user' => $this->getUser(), 'track' => $track]);
        $this->entityManager->remove($lovedTrack);
      } else {
        $lovedTrack = new LovedTrack();
        $lovedTrack->setUser($this->getUser());
        $lovedTrack->setTrack($track);
        $this->entityManager->persist($lovedTrack);
      }

      $this->entityManager->flush();

      $response->setStatusCode(Response::HTTP_OK);

    } catch (\Exception $e) {
      $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
      $logger->error('Internal error when trying to love/unlove a track (id track : ' . $id . ', user_id : ' . $this->getUser()->getId() . ') : ' . $e->getMessage());
    }

    return $response;
  }

}