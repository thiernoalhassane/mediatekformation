<?php


use App\Entity\Categorie;
use App\Entity\Formation;
use App\Entity\Playlist;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CategoryRepositoryTest extends KernelTestCase
{
    /** @var EntityManagerInterface */
    private $entityManager;

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
        // Récupère l'EntityManager depuis le conteneur de services
        $this->entityManager = self::getContainer()->get('doctrine')->getManager();
        $this->categorieRepository = $this->entityManager->getRepository(Categorie::class);

        // On peut nettoyer la table pour chaque test si nécessaire
        $connection = $this->entityManager->getConnection();
        $platform = $connection->getDatabasePlatform();
    }

    #[\Override]
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
        $this->entityManager = null;
    }

    public function testAddAndFindCategorie(): void
    {
        $categorie = new Categorie();
        $categorie->setName('Test Catégorie');

        // Ajout via le repository
        $this->categorieRepository->add($categorie);

        // Recherche de l'entité insérée
        $retrieved = $this->categorieRepository->findOneBy(['name' => 'Test Catégorie']);
        $this->assertNotNull($retrieved);
        $this->assertEquals('Test Catégorie', $retrieved->getName());
    }

    public function testUpdateCategorie(): void
    {

        $categorie = new Categorie();
        $categorie->setName('Catégorie à Mettre à Jour');
        $this->categorieRepository->add($categorie);

        // Mise à jour
        $categorie->setName('Catégorie Mise à Jour');
        $this->categorieRepository->update($categorie);

        // Vérification de la mise à jour
        $retrieved = $this->categorieRepository->findOneBy(['id' => $categorie->getId()]);
        $this->assertNotNull($retrieved);
        $this->assertEquals('Catégorie Mise à Jour', $retrieved->getName());
    }

    public function testRemoveCategorie(): void
    {

        $categorie = new Categorie();
        $categorie->setName('Catégorie à Supprimer');
        $this->categorieRepository->add($categorie);
        $id = $categorie->getId();

        // Suppression
        $this->categorieRepository->remove($categorie);

        // Vérification que l'entité a été supprimée
        $retrieved = $this->categorieRepository->find($id);
        $this->assertNull($retrieved);
    }

    
    public function testFindAllOrderByName(): void
    {
        // Insertion de plusieurs catégories
        $categorieA = new Categorie();
        $categorieA->setName('Alpha');
        $this->categorieRepository->add($categorieA);

        $categorieB = new Categorie();
        $categorieB->setName('Beta');
        $this->categorieRepository->add($categorieB);

        $this->entityManager->flush();

        $result = $this->categorieRepository->findAllOrderByName('ASC');
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
        $this->assertEquals('Alpha', $result[0]->getName());
    }

    public function testFindByContainValue(): void
    {
        // Insertion d'une catégorie avec un nom spécifique
        $categorie = new Categorie();
        $categorie->setName('Spécial Catégorie');
        $this->categorieRepository->add($categorie);
        $this->entityManager->flush();

        // Test avec une valeur non vide
        $result = $this->categorieRepository->findByContainValue('name', 'Spécial');
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
        $this->assertEquals('Spécial Catégorie', $result[0]->getName());

        // Test avec une valeur vide qui utilise findAllOrderByName
        $resultEmpty = $this->categorieRepository->findByContainValue('name', '');
        $this->assertIsArray($resultEmpty);
        // On s'attend à récupérer toutes les catégories (au moins une dans ce test)
        $this->assertNotEmpty($resultEmpty);
    }
}
