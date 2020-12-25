<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Booking;
use App\Form\BookingType;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BookingController extends AbstractController
{
    /**
     * @Route("/ads/{slug}/book", name="booking_create")
     * //@Route("{slug}/book", name="booking_create")
     * //@IsGranted("ROLE_USER")
     */
    public function book(Ad $ad,Request $request,ObjectManager $manager,Booking $booking): Response
    {
        //$booking=new Booking();
        $form=$this->createForm(BookingType::class,$booking);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $user=$this->getUser();
            $booking->setBooker($user)
                    ->setAd($ad);
            // Si les dates ne sont pas dispo, message d'erreur
            if(!$booking->isBookableDates()){
                $this->addFlash(
                    'warning',
                    'Vos dates sont réservées'
                );
            } else{
                $manager->persist($booking);
                $manager->flush();
    
                return $this->redirectToRoute('booking_show',['id'=>$booking->getId(),
                'withAlert'=>true]);
            }
            /*
            $manager->persist($booking);
            $manager->flush();

            return $this->redirectToRoute('booking_show',['id'=>$booking->getId(),
            'withAlert'=>true]); */
        }
        
        return $this->render('booking/index_book.html.twig', [
            'ad'=>$ad,
            'form'=>$form->createView()
        ]);
    }

    /**
     * Permet d'afficher la page d'une réservation
     * @Route("/booking/{id}",name="booking_show")
     * 
     * @param Booking $booking
     * @return Response
     */
    
    public function book_show(Booking $booking){
        return $this->render('booking/book_show.html.twig',[
            'booking'=>$booking
        ]);
    }
}
