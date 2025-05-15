<?php

namespace App\Controller;

use App\Entity\Employe;
use App\Form\EmployeTypeForm;
use Doctrine\ORM\EntityManager;
use App\Repository\EmployeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EmployeController extends AbstractController
{
    #[Route('/employe', name: 'app_employe')]
    public function index(EmployeRepository $employeRepository): Response
    {
        // SELECT * FROM employe ORDER BY nom
        $employes = $employeRepository->findBy([], ['nom' => 'ASC']);
        return $this->render('employe/index.html.twig', [
            'employes' => $employes
        ]);
    }

    #[Route('/employe/new', name: 'new_employe')]
    #[Route('/employe/{id}/edit', name: 'edit_employe')]
    public function new(?Employe $employe, Request $request, EntityManagerInterface $entityManager): Response
    {

        // si l'entreprise n'existe pas, on crée un nouvel objet
        if(!$employe) {
            $employe = new Employe();
        }
        
        // on crée le formulaire 
        // on lui passe l'entité employe
        $form = $this->createForm(EmployeTypeForm::class, $employe);
        
        // on lui dit de traiter la requête
        $form->handleRequest($request);

        // on vérifie si le formulaire a été soumis et est valide
        // si oui, on récupère les données du formulaire
        // et on les hydrate dans l'entité employé
        if ($form->isSubmitted() && $form->isValid()) {
            $employe = $form->getData();

            // equivalent à un prepare en PDO
            $entityManager->persist($employe);
            // equivalent à un execute en PDO
            $entityManager->flush();

        // et on redirige vers la liste des entreprises
            return $this->redirectToRoute('app_employe');
        }
        
        // on lui dit d'afficher le forumulaire avec le template employe/new.html.twig
        return $this->render('employe/new.html.twig', [
            'formAddEmploye' => $form
        ]);
    }
    
    #[Route('/employe/{id}/delete', name: 'delete_employe')]
    public function delete(Employe $employe, EntityManagerInterface $entityManager)
    {
        // on supprime l'employé
        $entityManager->remove($employe);
        $entityManager->flush();

        // et on redirige vers la liste des employés
        return $this->redirectToRoute('app_employe');
    }

    #[Route('/employe/{id}', name: 'show_employe')]
    public function show(Employe $employe): Response
    {
        return $this->render('employe/show.html.twig', [
            'employe' => $employe
        ]);
    }

}