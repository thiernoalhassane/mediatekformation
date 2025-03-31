
<?php

use App\Entity\Formation;
use App\Entity\Playlist;
use App\Entity\Categorie;
use App\Repository\FormationRepository;
use App\Repository\PlaylistRepository;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class FormationRepositoryTest extends KernelTestCase
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var FormationRepository */
    private $formationRepository;

    /** @var PlaylistRepository */
    private $playlistRepository;

    /** @var CategorieRepository */
    private $categorieRepository;

    /**
     * Cette méthode permet d'indiquer à PHPUnit la classe de votre Kernel.
     */
    #[\Override]
    protected static function getKernelClass(): string
    {
        return \App\Kernel::class;
    }

    #[\Override]
    protected function setUp(): void
    {
        self::bootKernel();
        $this->entityManager = self::getContainer()->get('doctrine')->getManager();
        $this->formationRepository = $this->entityManager->getRepository(Formation::class);
        $this->playlistRepository = $this->entityManager->getRepository(Playlist::class);
        $this->categorieRepository = $this->entityManager->getRepository(Categorie::class);
    }

    #[\Override]
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
        $this->entityManager = null;
    }

    public function testAddFormation(): void
    {
        $title = 'Formation Test';
        $description = 'Description de la formation';
        $playlist = $this->playlistRepository->find(1); // Supposons que cette playlist existe
        $video_id = '12345';
        $categories = [$this->categorieRepository->find(1)]; // Supposons qu'une catégorie existe
        $formation = new Formation();
        $formation->setTitle($title);
        $formation->setDescription($description);
        $formation->setVideoId($video_id);
        $formation->setPlaylist($playlist);
        $formation->setPublishedAt(new \DateTime('now'));
        foreach ($categories as $category) {
            $formation->addCategory($category);
        }
        $this->formationRepository->add($formation);
        $retrieved = $this->formationRepository->findBy(['title' => 'Formation Test'],null,1,0);
        $this->assertNotNull($retrieved);
        $this->assertEquals($title, $retrieved[0]->getTitle());
        $this->assertEquals($description, $retrieved[0]->getDescription());
        $this->assertEquals($video_id, $retrieved[0]->getVideoId());
        $this->assertNotNull($retrieved[0]->getPlaylist());
    }

    public function testUpdateFormation(): void
    {
        $formation = $this->formationRepository->find(1); // Supposons qu'une formation existe avec cet ID
        $formation->setTitle('Formation Mise à Jour');
        $formation->setDescription('Nouvelle description');
        $formation->setVideoId('67890');
        
        $newPlaylist = $this->playlistRepository->find(1); // Supposons qu'une autre playlist existe
        $formation->setPlaylist($newPlaylist);
        
        $newCategories = [$this->categorieRepository->find(2)]; // Supposons qu'une autre catégorie existe
        $formation->updateCategories($newCategories);
        
        $this->formationRepository->update($formation);

        $retrieved = $this->formationRepository->find($formation->getId());
        $this->assertEquals('Formation Mise à Jour', $retrieved->getTitle());
        $this->assertEquals('Nouvelle description', $retrieved->getDescription());
        $this->assertEquals('67890', $retrieved->getVideoId());
        $this->assertEquals($newPlaylist, $retrieved->getPlaylist());
    }

    public function testRemoveFormation(): void
    {
        $formation = new Formation();
        $formation->setTitle('Formation à Supprimer');
        $formation->setDescription('Description');
        $this->formationRepository->add($formation);
        $id = $formation->getId();

        // Suppression
        $this->formationRepository->remove($formation);

        // Vérification que l'entité a été supprimée
        $retrieved = $this->formationRepository->find($id);
        $this->assertNull($retrieved);
    }

    public function testFindAllOrderByName(): void
    {
        $formationA = new Formation();
        $formationA->setTitle('Alpha Formation');
        $formationA->setDescription('Nouvelle description');
        $formationA->setVideoId('67890');
        $playlistA = $this->playlistRepository->find(1); 
        $categoriesA = [$this->categorieRepository->find(1)]; 
        foreach ($categoriesA as $category) {
            $formationA->addCategory($category);
        }
        $formationA->setPlaylist($playlistA);
        $formationA->setPublishedAt(new \DateTime('now'));

        $this->formationRepository->add($formationA);

        $formationB = new Formation();
        $formationB->setTitle('Beta Formation');
        $formationB->setDescription('Nouvelle description');
        $formationB->setVideoId('123456');
        $playlistB = $this->playlistRepository->find(1); 
        $categoriesB = [$this->categorieRepository->find(1)];
        foreach ($categoriesB as $category) {
            $formationB->addCategory($category);
        }
        $formationB->setPlaylist($playlistB);
        $formationB->setPublishedAt(new \DateTime('now'));

        $this->formationRepository->add($formationB);

        $this->entityManager->flush();

        $result = $this->formationRepository->findAllOrderBy(champ:'title',ordre:'ASC');
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
        $this->assertEquals('Alpha Formation', $result[0]->getTitle());
    }

    public function testFindByContainValue(): void
    {
        $formation = new Formation();
        $formation->setTitle('Formation Spéciale');
        $formation->setDescription('Description');
        $formation->setVideoId('45789');
        $playlist = $this->playlistRepository->find(1); // Supposons que cette playlist existe
        $formation->setPlaylist($playlist);
        $formation->setPublishedAt(new \DateTime('now'));
        $categories = [$this->categorieRepository->find(1)]; // Supposons qu'une catégorie existe
        foreach ($categories as $category) {
            $formation->addCategory($category);
        }
        $this->formationRepository->add($formation);
        $this->entityManager->flush();

        // Test avec une valeur spécifique
        $result = $this->formationRepository->findByContainValue('title', 'Spéciale');
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
        $this->assertEquals('Formation Spéciale', $result[0]->getTitle());

        // Test avec une valeur vide
        $resultEmpty = $this->formationRepository->findByContainValue('title', '');
        $this->assertIsArray($resultEmpty);
        $this->assertNotEmpty($resultEmpty);
    }
}
