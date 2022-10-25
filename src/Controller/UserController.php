<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\EditProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/user', name: 'user')]
    public function index(): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $orders = $user->getOrders();

        return $this->render('user/index.html.twig', [
            'orders' => $orders,
        ]);
    }

    #[Route('/user/edit', name: 'user_edit')]
    public function EditUser(Request $request, EntityManagerInterface $entityManager): Response
    {
        //Je recupere l'utilisateur connecter
        $user = $this->getUser();
        //Je crée le formulaire afin pouvoir modifier
        $form = $this->createForm(EditProfileType::class, $this->getUser());
        $form->handleRequest($request);
        //Nous verifions si le formulaire est bien remplis et répond aux critères de validation
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success',"Votre profil a bien été modifié");
            return $this->redirectToRoute('user');
        }

        return $this->render('user/edit.html.twig', [
            'editUserForm' => $form->createView(),
        ]);
    }


    #[Route('/user/edit/password', name: 'user_edit_pass')]
    public function EditPassword(Request $request,ManagerRegistry $doctrine ,UserPasswordHasherInterface $passwordHasher): Response
    {
        if($request->isMethod('POST')){
            $em = $doctrine->getManager();
            $user = $this->getUser();

            if ($passwordHasher->isPasswordValid($user, $request->request->get('currentpass'))){

                //On vérifie si les deux mdp sont identiques
                if ($request->request->get('pass') == $request->request->get('pass2')) {
                    $user->setPassword($passwordHasher->hashPassword($user, $request->request->get('pass')));
                    $em->flush();

                    return $this->redirectToRoute('user');
                }else{
                    $this->addFlash('danger', 'Les mots de passes ne sont pas identiques');
                }
            }else{
                $this->addFlash('danger', 'Votre mot de passe actuel est incorrect');
            }

        }
        return $this->render('user/passwordedit.html.twig');

    }
}
