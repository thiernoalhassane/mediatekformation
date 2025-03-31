<?php

namespace App\Controller;

use App\Repository\CategorieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Categorie;

/**
 * Description of PlaylistsController
 *
 * @author emds
 */
class CategorieController extends AbstractController
{
    private const VIEW_CATEGORIE = "pages/categorie.html.twig";
    

    
    /**
     *
     * @var CategorieRepository
     */
    private $categorieRepository;

    public function __construct(
        CategorieRepository $categorieRepository,
    ) {
        $this->categorieRepository = $categorieRepository;
    }

    /**
     * @Route("/playlists", name="playlists")
     * @return Response
     */
    #[Route('/categories', name: 'categories')]
    public function index(): Response
    {
        
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::VIEW_CATEGORIE, [
            'categories' => $categories
        ]);
    }
    
    #[Route('/categories/add', name: 'categories.add')]
    public function add(Request $request): Response {
        $name = $request->get('name');
        $categorie = new Categorie();
        $categorie->setName($name);
        $this->categorieRepository->add($categorie);
        return $this->redirectToRoute('categories');
    }

    #[Route('/categories/edit', name: 'categories.edit')]
    public function edit(Request $request): Response {
        $name = $request->get('name');
        $categorie = $this->categorieRepository->find($request->get('id'));
        $categorie->setName($name);
        $this->categorieRepository->update($categorie);
        return $this->redirectToRoute('categories');
    }

    #[Route('/categories/delete', name: 'categories.delete')]
    public function delete(Request $request): Response {
        $categorie = $this->categorieRepository->find($request->get('id'));
        $this->categorieRepository->remove($categorie);
        return $this->redirectToRoute('categories');
    }

    #[Route('/categories/tri/{champ}/{ordre}', name: 'categories.sort')]
    public function sort($champ, $ordre): Response
    {
       switch ($champ) {
           case "name":
                $categories = $this->categorieRepository->findAllOrderByName($ordre);
                break;
            
            default:
                $categories = [];
                break;
     }
        return $this->render(self::VIEW_CATEGORIE, [
            'categories' => $categories
        ]);
    }

    #[Route('/categories/recherche/{champ}/{table}', name: 'categories.findallcontain')]
    public function findAllContain($champ, Request $request, $table = ""): Response
    {
        $valeur = $request->get("recherche");
        $categories = $this->categorieRepository->findByContainValue($champ, $valeur, $table);
        return $this->render(self::VIEW_CATEGORIE, [
            'categories' => $categories,
            'valeur' => $valeur,
            'table' => $table
        ]);
    }   
    
}
