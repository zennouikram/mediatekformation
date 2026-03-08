<?php

namespace App\Controller\Backoffice;

use App\Entity\Playlist;
use App\Form\PlaylistType;
use App\Repository\PlaylistRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/playlists')]
class PlaylistsBackController extends AbstractController
{
    private $playlistRepository;

    public function __construct(PlaylistRepository $playlistRepository)
    {
        $this->playlistRepository = $playlistRepository;
    }

    // 🔹 LISTE ADMIN
    #[Route('/', name: 'admin_playlists')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $playlists = $this->playlistRepository->findAllOrderByName('ASC');

        return $this->render('Backoffice/listeplaylists.html.twig', [
            'playlists' => $playlists
        ]);
    }

    // 🔹 AJOUT
    #[Route('/add', name: 'admin_add_playlist')]
    public function add(Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $playlist = new Playlist();
        $form = $this->createForm(PlaylistType::class, $playlist);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->persist($playlist);
            $em->flush();

            $this->addFlash('success', 'Playlist ajoutée.');
            return $this->redirectToRoute('admin_playlists');
        }

        return $this->render('Backoffice/addeditplaylists.html.twig', [
            'form' => $form->createView(),
            'playlist' => $playlist
        ]);
    }

    // 🔹 MODIFICATION
    #[Route('/edit/{id}', name: 'admin_edit_playlist')]
    public function edit(Playlist $playlist, Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(PlaylistType::class, $playlist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->flush();

            $this->addFlash('success', 'Playlist modifiée.');
            return $this->redirectToRoute('admin_playlists');
        }

        return $this->render('Backoffice/addeditplaylists.html.twig', [
            'form' => $form->createView(),
            'playlist' => $playlist
        ]);
    }

    // 🔹 SUPPRESSION (SEULEMENT SI AUCUNE FORMATION)
    #[Route('/delete/{id}', name: 'admin_delete_playlist', methods: ['POST'])]
    public function delete(Playlist $playlist, Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if (!$this->isCsrfTokenValid('delete'.$playlist->getId(), $request->request->get('_token'))) {
            return $this->redirectToRoute('admin_playlists');
        }

        // Vérification : aucune formation liée
        if (count($playlist->getFormations()) > 0) {
            $this->addFlash('error', 'Impossible de supprimer une playlist contenant des formations.');
            return $this->redirectToRoute('admin_playlists');
        }

        $em->remove($playlist);
        $em->flush();

        $this->addFlash('success', 'Playlist supprimée.');
        return $this->redirectToRoute('admin_playlists');
    }
}
