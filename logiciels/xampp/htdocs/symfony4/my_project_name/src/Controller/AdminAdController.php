<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AdType;
use App\Repository\AdRepository;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminAdController extends AbstractController
{
    /**
     * @Route("/admin/ads", name="admin_ads_index")
     */
    public function index(AdRepository $repo): Response
    {
        return $this->render('admin/ad/index_min_ad.html.twig', [
            'ads'=>$repo->findAll()
        ]);
    }

    /**
     * @Route("/admin/ads/{id}/edit",name="admin_ads_edit")
     * @param Ad $ad
     * @return Response
     * 

     */

     public function edit(Ad $ad,Request $request,ObjectManager $manager){
         $form=$this->createForm(AdType::class,$ad);
         $form->handleRequest($request);
         if($form->isSubmitted() && $form->isValid()){
             $manager->persist($ad);
             $manager->flush();

             $this->addFlash(
                 'success',
                 "l'annonce  <strong>{$ad->getTitle()} </strong> a été bien enregistrée"
             );
         }
         return $this->render('admin/ad/admin_edit.html.twig',[
             'ad'=>$ad,
             'form'=>$form->createView()
         ]);
     }

     /**
      * Permet de supprimer une annonce
      * @Route("/admin/ads/{id}/delete",name="admin_ads_delete")
      * @param Ad $ad
      * @param ObjectManager $manager
      * @return Response
      */
      public function delete(Ad $ad,ObjectManager $manager){
          if(count($ad->getBookings())>0){
              $this->addFlash(
                  'warning',
                  "Cette annonce est déjà réservée"
              );
          } else {
            $manager->remove($ad);
            $manager->flush();
            $this->addFlash(
                'success',
                "L'annonce {$ad->getTitle()} a été bien supprimée "
            );
          }
          return $this->redirectToRoute('admin_ads_index');

      }
}
