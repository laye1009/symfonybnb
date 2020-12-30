<?php

namespace App\Controller;

use App\Repository\AdRepository;
use App\Service\StatsService;
use App\Repository\UserRepository;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminDashboardController extends AbstractController
{
    /**
     * @Route("/admin/dash", name="admin_dashboard")
     */
    //public function index(ObjectManager $manager, UserRepository $repo,StatsService $stats): Response
    public function index(ObjectManager $manager,UserRepository $repo,AdRepository $arepo): Response
    {
        
        $tot=count($repo->findAll());
        echo $tot;

        

        
        return $this->render('admin/dashboard/dash_index.html.twig'

        //,['stats'=>compact('users','bookings','comments')
        //bestAds=$bestAds
        );
    }
}
