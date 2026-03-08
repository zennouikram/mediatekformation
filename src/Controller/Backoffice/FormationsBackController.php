<?php

namespace App\Controller\Backoffice;

use App\Entity\Formation;
use App\Form\FormationType;
use App\Repository\FormationRepository;
use App\Repository\CategorieRepository;
use App\Repository\PlaylistRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/formations')]
class FormationsBackController extends AbstractController
{
    private $formationRepository;
    private $categorieRepository;
    private $playlistRepository;

    public function __construct(
        FormationRepository $formationRepository,
        CategorieRepository $categorieRepository,
        PlaylistRepository $playlistRepository
    ) {
        $this->formationRepository = $formationRepository;
        $this->categorieRepository = $categorieRepository;
        $this->playlistRepository = $playlistRepository;
    }

    // 🔹 LISTE ADMIN
    #[Route('/', name: 'admin_formations')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $formations = $this->formationRepository->findAll();
        $categories = $this->categorieRepository->findAll();

        return $this->render('Backoffice/listeformations.html.twig', [
            'formations' => $formations,
            'categories' => $categories
        ]);
    }

    // 🔹 AJOUT
    #[Route('/add', name: 'admin_add_formation')]
    public function add(Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $formation = new Formation();
        $form = $this->createForm(FormationType::class, $formation);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($formation->getDate() > new \DateTime()) {
                $this->addFlash('error', 'La date ne peut pas être postérieure à aujourd’hui.');
                return $this->redirectToRoute('admin_add_formation');
            }

            $em->persist($formation);
            $em->flush();

            $this->addFlash('success', 'Formation ajoutée avec succès.');
            return $this->redirectToRoute('admin_formations');
        }

        return $this->render('Backoffice/addeditformations.html.twig', [
            'form' => $form->createView()
        ]);
    }

    // 🔹 MODIFICATION
    #[Route('/edit/{id}', name: 'admin_edit_formation')]
    public function edit(Formation $formation, Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($formation->getDate() > new \DateTime()) {
                $this->addFlash('error', 'La date ne peut pas être postérieure à aujourd’hui.');
                return $this->redirectToRoute('admin_edit_formation', ['id' => $formation->getId()]);
            }

            $em->flush();

            $this->addFlash('success', 'Formation modifiée.');
            return $this->redirectToRoute('admin_formations');
        }

        return $this->render('Backoffice/addeditformations.html.twig', [
            'form' => $form->createView()
        ]);
    }

    // 🔹 SUPPRESSION (avec CSRF)
    #[Route('/delete/{id}', name: 'admin_delete_formation', methods: ['POST'])]
    public function delete(Formation $formation, Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($this->isCsrfTokenValid('delete'.$formation->getId(), $request->request->get('_token'))) {

            $em->remove($formation);
            $em->flush();

            $this->addFlash('success', 'Formation supprimée.');
        }

        return $this->redirectToRoute('admin_formations');
    }
}
