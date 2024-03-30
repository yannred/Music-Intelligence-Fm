<?php

namespace App\Controller;

use App\Entity\Album;
use App\Entity\Artist;
use App\Entity\Scrobble;
use App\Entity\Track;
use App\Service\ApiRequestService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MyPageController extends CustomAbsrtactController
{

  /**
   * Display the user's page
   * @param ApiRequestService $apiRequestService
   * @param Request $request
   * @param PaginatorInterface $paginator
   * @return Response
   * @throws \Exception
   */
  #[Route('/myPage', name: 'app_my_page')]
  public function index(ApiRequestService $apiRequestService, Request $request, PaginatorInterface $paginator): Response
  {
    $apiRequestService->setUser($this->getUser());

    //User infos
    $lastFmUserInfo = $apiRequestService->getLastFmUserInfo();
    //TODO : handle error response from API
    $lastFmUserInfo = json_decode($lastFmUserInfo, true);
    if ($lastFmUserInfo === false) {
      throw new \Exception("Error in ScrobblerController::updateScrobble() : Can't decode api first response in json");
    }

    $lastFmUser = array();
    $lastFmUser['userName'] = $lastFmUserInfo['user']['name'];
    $lastFmUser['userRealName'] = $lastFmUserInfo['user']['realname'];
    $lastFmUser['scrobbleCount'] = $lastFmUserInfo['user']['playcount'];
    foreach ($lastFmUserInfo['user']['image'] as $image) {
      if ($image['size'] == 'large') {
        $lastFmUser['image'] = $image['#text'];
        break;
      }
    }
    $lastFmUser['trackCount'] = $lastFmUserInfo['user']['track_count'];
    $lastFmUser['albumCount'] = $lastFmUserInfo['user']['album_count'];
    $lastFmUser['artistCount'] = $lastFmUserInfo['user']['artist_count'];


    //Last scrobbles
    $scrobbleRepository = $this->entityManager->getRepository(Scrobble::class);
    $query = $scrobbleRepository->paginationQuery();
    $scrobblePagination = $paginator->paginate(
      $query,
      $request->query->getInt('page', 1),
      Scrobble::LIMIT_RECENT_TRACKS
    );

    //top tracks
    $trackRepository = $this->entityManager->getRepository(Track::class);
    $tracks = $trackRepository->getTopTracks();

    //top artists
    $artistRepository = $this->entityManager->getRepository(Artist::class);
    $artists = $artistRepository->getTopArtists();

    //top albums
    $albumRepository = $this->entityManager->getRepository(Album::class);
    $albums = $albumRepository->getTopAlbums();


    return $this->render('my_page/index.html.twig', [
      'lastFmUser' => $lastFmUser,
      'scrobbles' => $scrobblePagination,
      'artists' => $artists,
      'albums' => $albums,
      'tracks' => $tracks,
      'pagination' => 0,
      'userPlaycount' => 1,
      'activeNavbarItem' => $request->get('_route'),
      'myScrobblesTbodyUrl' => 'my_scrobbles/tbody.html.twig',
      'myScrobblesThead' => ['' , 'Title', 'Artist', 'Album', 'Date'],
      'myTracksTbodyUrl' => 'my_tracks/tbody.html.twig',
      'myTracksThead' => ['' , 'Title', 'Artist', 'Album', 'Scrobble']
    ]);
  }
}