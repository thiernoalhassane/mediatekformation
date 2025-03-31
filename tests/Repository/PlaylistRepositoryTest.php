<?php

use App\Entity\Playlist;
use App\Repository\PlaylistRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PlaylistRepositoryTest extends KernelTestCase
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var PlaylistRepository */
    private $playlistRepository;

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
        $this->playlistRepository = $this->entityManager->getRepository(Playlist::class);
    }

    #[\Override]
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
        $this->entityManager = null;
    }

    public function testAddPlaylist(): void
    {
        $name = 'Playlist Test';
        $description = 'Description de la playlist';

        $playlist = new Playlist();
        $playlist->setName($name);
        $playlist->setDescription($description);

        $this->playlistRepository->add($playlist);

        // Recherche de la playlist insérée
        $retrieved = $this->playlistRepository->findOneBy(['name' => 'Playlist Test']);
        $this->assertNotNull($retrieved);
        $this->assertEquals($name, $retrieved->getName());
        $this->assertEquals($description, $retrieved->getDescription());
    }

    public function testUpdatePlaylist(): void
    {
        $playlist = $this->playlistRepository->find(1); // Supposons qu'une playlist existe avec cet ID
        $playlist->setName('Playlist Mise à Jour');
        $playlist->setDescription('Nouvelle description');

        $this->playlistRepository->update($playlist);

        $retrieved = $this->playlistRepository->find($playlist->getId());
        $this->assertEquals('Playlist Mise à Jour', $retrieved->getName());
        $this->assertEquals('Nouvelle description', $retrieved->getDescription());
    }

    public function testRemovePlaylist(): void
    {
        $playlist = new Playlist();
        $playlist->setName('Playlist à Supprimer');
        $playlist->setDescription('Description');
        $this->playlistRepository->add($playlist);
        $id = $playlist->getId();

        // Suppression
        $this->playlistRepository->remove($playlist);

        // Vérification que l'entité a été supprimée
        $retrieved = $this->playlistRepository->find($id);
        $this->assertNull($retrieved);
    }

    public function testFindAllOrderByName(): void
    {
        $playlistA = new Playlist();
        $playlistA->setName('Alpha Playlist');
        $playlistA->setDescription('Description');

        $this->playlistRepository->add($playlistA);

        $playlistB = new Playlist();
        $playlistB->setName('Beta Playlist');
        $playlistB->setDescription('Beta Playlist Description');

        $this->playlistRepository->add($playlistB);

        $this->entityManager->flush();

        $result = $this->playlistRepository->findAllOrderByName('ASC');
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
        $this->assertEquals('Alpha Playlist', $result[0]->getName());
    }

    public function testFindByContainValue(): void
    {
        $playlist = new Playlist();
        $playlist->setName('Playlist Spéciale');
        $playlist->setDescription('Description');
        $this->playlistRepository->add($playlist);
        $this->entityManager->flush();

        // Test avec une valeur spécifique
        $result = $this->playlistRepository->findByContainValue('name', 'Spéciale');
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
        $this->assertEquals('Playlist Spéciale', $result[0]->getName());

        // Test avec une valeur vide
        $resultEmpty = $this->playlistRepository->findByContainValue('name', '');
        $this->assertIsArray($resultEmpty);
        $this->assertNotEmpty($resultEmpty);
    }
}

