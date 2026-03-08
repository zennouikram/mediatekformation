<?php

namespace App\Controller\Backoffice;

use App\Entity\Categorie;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/categories')]
class CategoriesBackController extends AbstractController
{
    private $categorieRepository;

    public function __construct(CategorieRepository $categorieRepository)
    {
        $this->categorieRepository = $categorieRepository;
    }

    // 🔹 LISTE + MINI FORMULAIRE
    #[Route('/', name: 'admin_categories')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $categories = $this->categorieRepository->findAll();

        return $this->render('Backoffice/categories.html.twig', [
            'categories' => $categories
        ]);
    }

    // 🔹 AJOUT DIRECT
    #[Route('/add', name: 'admin_add_category', methods: ['POST'])]
    public function add(Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $name = trim($request->request->get('name'));

        if (empty($name)) {
            $this->addFlash('error', 'Le nom est obligatoire.');
            return $this->redirectToRoute('admin_categories');
        }

        // Vérification unicité
        $existing = $this->categorieRepository->findOneBy(['name' => $name]);

        if ($existing) {
            $this->addFlash('error', 'Cette catégorie existe déjà.');
            return $this->redirectToRoute('admin_categories');
        }

        $categorie = new Categorie();
        $categorie->setName($name);

        $em->persist($categorie);
        $em->flush();

        $this->addFlash('success', 'Catégorie ajoutée.');
        return $this->redirectToRoute('admin_categories');
    }

    // 🔹 SUPPRESSION (SEULEMENT SI PAS UTILISÉE)
    #[Route('/delete/{id}', name: 'admin_delete_category', methods: ['POST'])]
    public function delete(Categorie $categorie, Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if (!$this->isCsrfTokenValid('delete'.$categorie->getId(), $request->request->get('_token'))) {
            return $this->redirectToRoute('admin_categories');
        }

        // Vérification : catégorie utilisée ?
        if (count($categorie->getFormations()) > 0) {
            $this->addFlash('error', 'Impossible de supprimer une catégorie liée à une formation.');
            return $this->redirectToRoute('admin_categories');
        }

        $em->remove($categorie);
        $em->flush();

        $this->addFlash('success', 'Catégorie supprimée.');
        return $this->redirectToRoute('admin_categories');
    }
}
