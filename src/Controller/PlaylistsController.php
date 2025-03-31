<?php

namespace App\Controller;

use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use App\Repository\PlaylistRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Playlist;

/**
 * Description of PlaylistsController
 *
 * @author emds
 */
class PlaylistsController extends AbstractController
{
    private const VIEW_PLAYLISTS = "pages/playlists.html.twig";
    

    /**
     *
     * @var PlaylistRepository
     */
    private $playlistRepository;

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

    public function __construct(
        PlaylistRepository $playlistRepository,
        CategorieRepository $categorieRepository,
        FormationRepository $formationRepository
    ) {
        $this->playlistRepository = $playlistRepository;
        $this->categorieRepository = $categorieRepository;
        $this->formationRepository = $formationRepository;
    }

    /**
     * @Route("/playlists", name="playlists")
     * @return Response
     */
    #[Route('/playlists', name: 'playlists')]
    public function index(): Response
    {
        $playlists = $this->playlistRepository->findAllOrderByName('ASC');
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::VIEW_PLAYLISTS, [
            'playlists' => $playlists,
            'categories' => $categories
        ]);
    }
    
    #[Route('/playlists/add', name: 'playlists.add')]
    public function add(Request $request): Response {
        $name = $request->get('name');
        $description = $request->get('description');
        $playlist = new Playlist();
        $playlist->setName($name);
        $playlist->setDescription($description);
        $this->playlistRepository->add($playlist);
        return $this->redirectToRoute('playlists');
    }

    #[Route('/playlists/edit', name: 'playlists.edit')]
    public function edit(Request $request): Response {
        $name = $request->get('name');
        $description = $request->get('description');
        $playlist = $this->playlistRepository->find($request->get('id'));
        $playlist->setName($name);
        $playlist->setDescription($description);
        $this->playlistRepository->update($playlist);
        return $this->redirectToRoute('playlists');
    }

    #[Route('/playlists/delete', name: 'playlists.delete')]
    public function delete(Request $request): Response {
        $playlist = $this->playlistRepository->find($request->get('id'));
        $this->playlistRepository->remove($playlist);
        return $this->redirectToRoute('playlists');
    }

    #[Route('/playlists/tri/{champ}/{ordre}', name: 'playlists.sort')]
    public function sort($champ, $ordre): Response
    {
       switch ($champ) {
           case "name":
                $playlists = $this->playlistRepository->findAllOrderByName($ordre);
                break;
            case "nbformations":
                $playlists = $this->playlistRepository->findAllOrderByNbFormations($ordre);
                break;
            default:
                $playlists = [];
                break;
     }

        $categories = $this->categorieRepository->findAll();
        return $this->render(self::VIEW_PLAYLISTS, [
            'playlists' => $playlists,
            'categories' => $categories
        ]);
    }

    #[Route('/playlists/recherche/{champ}/{table}', name: 'playlists.findallcontain')]
    public function findAllContain($champ, Request $request, $table = ""): Response
    {
        $valeur = $request->get("recherche");
        $playlists = $this->playlistRepository->findByContainValue($champ, $valeur, $table);
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::VIEW_PLAYLISTS, [
            'playlists' => $playlists,
            'categories' => $categories,
            'valeur' => $valeur,
            'table' => $table
        ]);
    }

    #[Route('/playlists/playlist/{id}', name: 'playlists.showone')]
    public function showOne($id): Response
    {
        $playlist = $this->playlistRepository->find($id);
        $playlistCategories = $this->categorieRepository->findAllForOnePlaylist($id);
        $playlistFormations = $this->formationRepository->findAllForOnePlaylist($id);
        return $this->render("pages/playlist.html.twig", [
            'playlist' => $playlist,
            'playlistcategories' => $playlistCategories,
            'playlistformations' => $playlistFormations
        ]);
    }

    

    
    
    
}
