<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AccountType;
use App\Entity\PasswordUpdate;
use App\Form\RegistrationType;
use App\Form\PasswordUpdateType;
use Symfony\Component\Form\FormError;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountController extends AbstractController
{
    /**
     * @Route("/login", name="account_login")
     
     */
    public function login(AuthenticationUtils $utils): Response
    {
        $error=$utils->getLastAuthenticationError();
        $username=$utils->getLastUsername();
    
        return $this->render('account/login.html.twig',[
            'hasError'=>$error!==null,
            'username'=>$username
        ]);
    }


    /**
     * @Route("/logout",name="account_logout")
     * @return void
     */

    public function logout(){
        // rien
    }

    /**
    * @Route("/register",name="account_register")
    */

     public function register(Request $request,ObjectManager $manager,UserPasswordEncoderInterface $encoder)
     {
         $user=new User();
         $form=$this->createForm(RegistrationType::class,$user);
         $form->handleRequest($request);
         if($form->isSubmitted() && $form->isValid())
         {
             $hash=$encoder->encodePassword($user,$user->getHash());
             $user->setHash($hash);
             $manager->persist($user);
             $manager->flush();

             $this->addFlash(
                 'success',
                 'Votre compte a été bien créées'
             );
             return $this->redirectToRoute('account_login');
         }

         return $this->render('account/registration.html.twig',
         ['form'=>$form->createView()]);

     }
     

     /**
      * modification de profile
      * @Route("/account/profile",name="account_profile")
      * 
      */
//@IsGranted("ROLE_USER")
      public function profile(Request $request,ObjectManager $manager)
      {
          $user=$this->getUser();
          $form=$this->createForm(AccountType::class,$user);
          $form->handleRequest($request);
          if($form->isSubmitted() && $form->isValid())
          {
              $manager->persist($user);
              $manager->flush();
              $this->addFlash(
                  'success',
                  "les données su profil ont été enregistrées"
              );
          }
          return $this->render('account/profile.html.twig',[
              'form'=>$form->createView()
          ]);
      }

      /**
       * @Route("/update-pwd",name="account_pwd")
       * @return response
       */
//@IsGranted("ROLE_USER")
       public function updatePwd(Request $request,UserPasswordEncoderInterface $encoder,ObjectManager $manager)
       {
           $pwdUp=new PasswordUpdate();// l'entité créée
           $user=$this->getUser();
           $form=$this->createForm(PasswordUpdateType::class,$pwdUp);
           $form->handleRequest($request);
           if($form->isSubmitted() && $form->isValid())
           {
               if(!password_verify($pwdUp->getOldPassword(),$user->getHash())){
                   $form->get("oldPassword")->addError(new FormError("Mot de passe actuel incorrect"));

               } else 
               {
                   $newPassword=$pwdUp->getNewPassword();
                   $hash=$encoder->encodePassword($user,$newPassword);
                   $user->setHash($hash);
                   $manager->persist($user);
                   $manager->flush();
                   $this->addFlash(
                       'success',
                       "votre mot de pas a été bien modiifé"
                   );
                   return $this->redirectToRoute("");
               }


           }
           return $this->render('account/password.html.twig',[
               'form'=>$form->createView()
           ]);
       }

       /**
        * Permet d'afficher le profild de l'utilisateur connecté
        * @Route("/account",name="account_index")
        * paramConverter("User")
        * @return response
        */
        public function myAccount(){
            //$em=$this->getDoctrine()->getManager();
            //$user=$urepo->findOneBy(['firstName']);
            return $this->render('user/user_index.html.twig',[
                'user'=>$this->getUser()
            ]);
        }

        /**
         * Permet d'afficher la liste des réservations faite par un utilisateur
         * 
         * @Route("/account/bookings",name="account_bookings")
         * @return response
         */

         public function bookings(){
             return $this->render('account/book_list.html.twig');
         }
}
