<?php

namespace App\Controller;

use App\Entity\Entreprise;
use App\Form\EntrepriseTypeForm;
use App\Repository\EntrepriseRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class EntrepriseController extends AbstractController
{
    #[Route('/entreprise', name: 'app_entreprise')]
    public function index(EntrepriseRepository $entrepriseRepository): Response
    {
        // SELECT * FROM entreprise WHERE 'ville' = 'Altkich' ORDER BY raison_sociale
        $entreprises = $entrepriseRepository->findBy([], ['raisonSociale' => 'ASC']);
        return $this->render('entreprise/index.html.twig', [
            'entreprises' => $entreprises
        ]);
    }

    #[Route('/entreprise/new', name: 'new_entreprise')]
    #[Route('/entreprise/{id}/edit', name: 'edit_entreprise')]
    public function new_edit(?Entreprise $entreprise, Request $request, EntityManagerInterface $entityManager): Response
    {
        // si l'entreprise n'existe pas, on crée un nouvel objet
        if(!$entreprise) {
            $entreprise = new Entreprise();
        }

        // on crée le formulaire 
        // on lui passe l'entité entreprise
        $form = $this->createForm(EntrepriseTypeForm::class, $entreprise);
        
        // on lui dit de traiter la requête
        $form->handleRequest($request);

        // on vérifie si le formulaire a été soumis et est valide
        // si oui, on récupère les données du formulaire
        // et on les hydrate dans l'entité entreprise
        if ($form->isSubmitted() && $form->isValid()) {
            $entreprise = $form->getData();

            // equivalent à un prepare en PDO
            $entityManager->persist($entreprise);
            // equivalent à un execute en PDO
            $entityManager->flush();

        // et on redirige vers la liste des entreprises
            return $this->redirectToRoute('app_entreprise');
        }
        
        // on lui dit d'afficher le forumulaire avec le template entreprise/new.html.twig
        return $this->render('entreprise/new.html.twig', [
            'formAddEntreprise' => $form,
            // renvois un booléen pour savoir si on est dans le cas d'une création ou d'une modification
            'edit' => $entreprise->getId()
        ]);
    }

     #[Route('/entreprise/{id}/delete', name: 'delete_entreprise')]
    public function delete(Entreprise $entreprise, EntityManagerInterface $entityManager)
    {
        // on supprime l'employé
        $entityManager->remove($entreprise);
        $entityManager->flush();

        // et on redirige vers la liste des employés
        return $this->redirectToRoute('app_entreprise');
    }

    
    #[Route('/entreprise/{id}', name: 'show_entreprise')]
    public function show(Entreprise $entreprise): Response
    {
        return $this->render('entreprise/show.html.twig', [
            'entreprise' => $entreprise
        ]);
    }
}
