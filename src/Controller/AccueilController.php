<?php
namespace App\Controller;

use App\Repository\FormationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Description of AccueilController
 *
 * @author emds
 */
class AccueilController extends AbstractController
{
    
    /**
     * @var FormationRepository
     */
    private $repository;
    
    /**
     *
     * @param FormationRepository $repository
     */
    public function __construct(FormationRepository $repository)
    {
        $this->repository = $repository;
    }
    
    #[Route('/login', name: 'login')]
    public function login(): Response
    {
        return $this->render('auth/login.html.twig');
    }
    
    #[Route('/login_check', name: 'login_check')]
    public function loginCheck(): void
    {
        // Cette méthode ne sera jamais appelée, 
        // car Symfony intercepte la route pour gérer l'authentification.
        throw new \Exception('This should never be reached!');
    }
    
    #[Route('/logout', name: 'logout')]
    public function logout(): void
    {
        // Cette méthode n'est jamais exécutée,
        // Symfony intercepte la route pour effectuer la déconnexion.
        throw new \Exception('Cette méthode ne doit pas être atteinte.');
    }
    
    #[Route('/', name: 'accueil')]
    public function index(): Response
    {
        $formations = $this->repository->findAllLasted(2);
        return $this->render("pages/accueil.html.twig", [
            'formations' => $formations
        ]);
    }
    
    #[Route('/cgu', name: 'cgu')]
    public function cgu(): Response
    {
        return $this->render("pages/cgu.html.twig");
    }
}
