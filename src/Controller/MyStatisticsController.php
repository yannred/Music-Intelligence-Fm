<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MyStatisticsController extends CustomAbsrtactController
{

  /**
   * Display the user's statistics page (dashboard for widgets)
   * @param Request $request
   * @return Response
   */
  #[Route('/myPage/myStatistics', name: 'app_my_statistics')]
  public function index(Request $request): Response
  {
    return $this->render('my_statistics/index.html.twig', [
      'activeNavbarItem' => $request->get('_route'),
    ]);
  }

}
