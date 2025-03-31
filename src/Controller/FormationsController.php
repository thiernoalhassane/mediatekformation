<?php

namespace App\Controller;

use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use App\Repository\PlaylistRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Formation;
use App\Entity\Playlist;
use DateTime;

/**
 * Controleur des formations
 *
 * @author emds
 */
class FormationsController extends AbstractController {

    private const VIEW_FORMATIONS = "pages/formations.html.twig";
    private const VIEW_FORMATION = "pages/formation_form.html.twig";

    /**
     *
     * @var FormationRepository
     */
    private $formationRepository;

    /**
     *
     * @var CategorieRepository
     */
    private $categorieRepository;

    /**
     *
     * @var PlaylistRepository
     */
    private $playlistRepository;

    public function __construct(FormationRepository $formationRepository, CategorieRepository $categorieRepository, PlaylistRepository $playlistRepository) {
        $this->formationRepository = $formationRepository;
        $this->categorieRepository = $categorieRepository;
        $this->playlistRepository = $playlistRepository;
    }

    #[Route('/formations', name: 'formations')]
    public function index(): Response {
        $formations = $this->formationRepository->findAll();
        $categories = $this->categorieRepository->findAll();
        $playlists = $this->playlistRepository->findAll();

        return $this->render(self::VIEW_FORMATIONS, [
                    'formations' => $formations,
                    'categories' => $categories,
                    'playlists' => $playlists
        ]);
    }

    #[Route('/formations/add', name: 'formations.add')]
    public function add(Request $request): Response {
        $title = $request->get('title');
        $categories = $request->get('categories');
        $description = $request->get('description');
        $playlist = $this->playlistRepository->findBy(['id' => $request->get('playlist')], null, 1, 0);
        $video_id = $request->get('video_id');
        $formation = new Formation();
        $formation->setTitle($title);
        $formation->setDescription($description);
        $formation->setVideoId($video_id);
        $formation->setPlaylist($playlist[0]);
        $formation->setPublishedAt(new DateTime('now'));
        if (!empty($categories)) {
            foreach ($categories as $categoryId) {
                $category = $this->categorieRepository->find($categoryId); // Récupérer l'entité Category
                $formation->addCategory($category); // Ajouter la catégorie à la formation
            }
        }
        $this->formationRepository->add($formation);
        return $this->redirectToRoute('formations');
    }

    #[Route('/formations/edit', name: 'formations.edit')]
    public function edit(Request $request): Response {
        $title = $request->get('title');
        $description = $request->get('description');
                $categories = $request->get('categories');

        $playlist = $this->playlistRepository->findBy(['id' => $request->get('playlist')], null, 1, 0);
        $video_id = $request->get('video_id');
        $formation = $this->formationRepository->find($request->get('id'));
        $formation->setTitle($title);
        $formation->setDescription($description);
        $formation->setVideoId($video_id);
        $formation->setPlaylist($playlist[0]);
        $arraycategories = [];
        if (!empty($categories)) {
            foreach ($categories as $categoryId) {
                $category = $this->categorieRepository->find($categoryId); // Récupérer l'entité Category
                $arraycategories[] = $category;
            }
        }
        $formation->updateCategories($arraycategories);
        $this->formationRepository->update($formation);
        return $this->redirectToRoute('formations');
    }

    #[Route('/formations/delete', name: 'formations.delete')]
    public function delete(Request $request): Response {
        $formation = $this->formationRepository->find($request->get('id'));
        $this->formationRepository->remove($formation);
        return $this->redirectToRoute('formations');
    }

    #[Route('/formations/tri/{champ}/{ordre}/{table}', name: 'formations.sort')]
    public function sort($champ, $ordre, $table = ""): Response {
        $formations = $this->formationRepository->findAllOrderBy($champ, $ordre, $table);
        $categories = $this->categorieRepository->findAll();
        $playlists = $this->playlistRepository->findAll();

        return $this->render(self::VIEW_FORMATIONS, [
                    'playlists' => $playlists,
                    'formations' => $formations,
                    'categories' => $categories
        ]);
    }

    #[Route('/formations/recherche/{champ}/{table}', name: 'formations.findallcontain')]
    public function findAllContain($champ, Request $request, $table = ""): Response {
        $valeur = $request->get("recherche");
        $formations = $this->formationRepository->findByContainValue($champ, $valeur, $table);
        $categories = $this->categorieRepository->findAll();
        $playlists = $this->playlistRepository->findAll();
        return $this->render(self::VIEW_FORMATIONS, [
                    'playlists' => $playlists,
                    'formations' => $formations,
                    'categories' => $categories,
                    'valeur' => $valeur,
                    'table' => $table
        ]);
    }

    #[Route('/formations/formation/{id}', name: 'formations.showone')]
    public function showOne($id): Response {
        $formation = $this->formationRepository->find($id);
        return $this->render("pages/formation.html.twig", [
                    'formation' => $formation
        ]);
    }
}
