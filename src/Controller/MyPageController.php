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


  /**
   * Return the Html for the compare scrobbles per period widget
   * @return Response
   */
  #[Route('/myPage/compareScrobblesPerPeriod', name: 'app_compare_scrobbles_per_period')]
  public function getCompareScrobblesPerPeriodWidget(): Response
  {

    //Week Stats
    $scrobbleRepository = $this->entityManager->getRepository(Scrobble::class);
    $weekResult = $scrobbleRepository->getTotalScrobblesThisWeek($this->getUser());

    $week = array();
    $week['lastWeek']['value'] = $weekResult['lastWeek'];
    $week['thisWeek']['value'] = $weekResult['thisWeek'];

    if ($weekResult['lastWeek'] == 0 && $weekResult['thisWeek'] == 0) {
      $week['lastWeek']['percent'] = 0;
      $week['thisWeek']['percent'] = 0;
    } elseif($weekResult['lastWeek'] == $weekResult['thisWeek']) {
      $week['lastWeek']['percent'] = 100;
      $week['thisWeek']['percent'] = 100;
    } elseif($weekResult['lastWeek'] > $weekResult['thisWeek']){
      $week['thisWeek']['percent'] = round(($weekResult['thisWeek'] * 100) / $weekResult['lastWeek']);
      $week['lastWeek']['percent'] = 100;
    } else {
      $week['thisWeek']['percent'] = 100;
      $week['lastWeek']['percent'] = round(($weekResult['lastWeek'] * 100) / $weekResult['thisWeek']);
    }


    //Month Stats
    $monthResult = $scrobbleRepository->getTotalScrobblesThisMonth($this->getUser());

    $month = array();
    $month['lastMonth']['value'] = $monthResult['lastMonth'];
    $month['thisMonth']['value'] = $monthResult['thisMonth'];

    if ($monthResult['lastMonth'] == 0 && $monthResult['thisMonth'] == 0) {
      $month['lastMonth']['percent'] = 0;
      $month['thisMonth']['percent'] = 0;
    } elseif($monthResult['lastMonth'] == $monthResult['thisMonth']) {
      $month['lastMonth']['percent'] = 100;
      $month['thisMonth']['percent'] = 100;
    } elseif($monthResult['lastMonth'] > $monthResult['thisMonth']){
      $month['thisMonth']['percent'] = round(($monthResult['thisMonth'] * 100) / $monthResult['lastMonth']);
      $month['lastMonth']['percent'] = 100;
    } else {
      $month['thisMonth']['percent'] = 100;
      $month['lastMonth']['percent'] = round(($monthResult['lastMonth'] * 100) / $monthResult['thisMonth']);
    }


    //Year Stats
    $yearResult = $scrobbleRepository->getTotalScrobblesThisYear($this->getUser());

    $year = array();
    $year['lastYear']['value'] = $yearResult['lastYear'];
    $year['thisYear']['value'] = $yearResult['thisYear'];

    if ($yearResult['lastYear'] == 0 && $yearResult['thisYear'] == 0) {
      $year['lastYear']['percent'] = 0;
      $year['thisYear']['percent'] = 0;
    } elseif($yearResult['lastYear'] == $yearResult['thisYear']) {
      $year['lastYear']['percent'] = 100;
      $year['thisYear']['percent'] = 100;
    } elseif($yearResult['lastYear'] > $yearResult['thisYear']){
      $year['thisYear']['percent'] = round(($yearResult['thisYear'] * 100) / $yearResult['lastYear']);
      $year['lastYear']['percent'] = 100;
    } else {
      $year['thisYear']['percent'] = 100;
      $year['lastYear']['percent'] = round(($yearResult['lastYear'] * 100) / $yearResult['thisYear']);
    }

    return $this->render('my_statistics/statistic_widget/compare_scrobbles_per_period.html.twig', [
      'week' => $week,
      'month' => $month,
      'year' => $year
    ]);

  }

}