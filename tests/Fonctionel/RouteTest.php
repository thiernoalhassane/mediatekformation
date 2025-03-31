<?php
// tests/Functional/RoutesTest.php


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class RouteTest extends WebTestCase
{
    public function testLoginPageIsAccessible(): void
    {
        $client = static::createClient();
        $client->request('GET', '/login');

        // Vérifier que la page de login est accessible
        $this->assertResponseIsSuccessful();

    }
    
     public function testCguPageIsAccessible(): void
    {
        $client = static::createClient();
        $client->request('GET', '/cgu');

        // Vérifier que la page des CGU est accessible
        $this->assertResponseIsSuccessful();
    }
    
    public function testCategoriesPageIsNotAccessibleIfNotLoggedIn(): void
    {
        $client = static::createClient();
        $client->request('GET', '/categories');

        // Vérifier que la page des catégories n'est pas accessible si non connecté
        $this->assertResponseRedirects('/login');
    }
    
    public function testFormationsPageIsNotAccessibleIfNotLoggedIn(): void
    {
        $client = static::createClient();
        $client->request('GET', '/formations');

        // Vérifier que la page des formations n'est pas accessible si non connecté
        $this->assertResponseRedirects('/login');
    }
    
    public function testPlaylistsPageIsNotAccessibleIfNotLoggedIn(): void
    {
        $client = static::createClient();
        $client->request('GET', '/playlists');

        // Vérifier que la page des playlists n'est pas accessible si non connecté
        $this->assertResponseRedirects('/login');
    }
    
    public function testAccueilPageIsAccessible(): void
    {        
        $client = static::createClient();        
        $client->request('GET', '/');
        $this->assertResponseIsSuccessful();
    }
    
     public function testCategoriesPageIsAccessibleIfLoggedIn(): void
    {
        $client = static::createClient();
        $client->request('GET', '/login');
        $client->submitForm('Se connecter', [
            '_username' => 'user',
            '_password' => 'password'
        ]);
        $client->request('GET', '/categories');

        // Vérifier que la page des catégories est accessible après connexion
        $this->assertResponseIsSuccessful();
    }
    
    public function testCategoriesSortPageIsAccessible(): void
    {
        $client = static::createClient();
        $client->request('GET', '/login');
        $client->submitForm('Se connecter', [
            '_username' => 'user',
            '_password' => 'password'
        ]);
        $client->request('GET', '/categories/tri/name/ASC');

        // Vérifier que la page de tri des catégories est accessible
        $this->assertResponseIsSuccessful();
    }
    
    public function testCategoriesSearchPageIsAccessible(): void
    {
        $client = static::createClient(); 
        $client->request('GET', '/login');
        $client->submitForm('Se connecter', [
            '_username' => 'user',
            '_password' => 'password'
        ]);
        $client->request('GET', '/categories/recherche/name');

        // Vérifier que la page de recherche des catégories est accessible
        $this->assertResponseIsSuccessful();
    }

    

    public function testFormationsPageIsAccessibleIfLoggedIn(): void
    {
        $client = static::createClient();
       $client->request('GET', '/login');
        $client->submitForm('Se connecter', [
            '_username' => 'user',
            '_password' => 'password'
        ]);
        $client->request('GET', '/formations');

        // Vérifier que la page des formations est accessible après connexion
        $this->assertResponseIsSuccessful();
        
    }

    public function testFormationsSortPageIsAccessible(): void
    {
        $client = static::createClient();
        $client->request('GET', '/login');
        $client->submitForm('Se connecter', [
            '_username' => 'user',
            '_password' => 'password'
        ]);
        $client->request('GET', '/formations/tri/title/ASC');

        // Vérifier que la page de tri des formations est accessible
        $this->assertResponseIsSuccessful();
    }

    public function testFormationsSearchPageIsAccessible(): void
    {
        $client = static::createClient();
        $client->request('GET', '/login');
        $client->submitForm('Se connecter', [
            '_username' => 'user',
            '_password' => 'password'
        ]);
        $client->request('GET', '/formations/recherche/title');

        // Vérifier que la page de recherche des formations est accessible
        $this->assertResponseIsSuccessful();
    }

    

    public function testPlaylistsPageIsAccessibleIfLoggedIn(): void
    {
        $client = static::createClient();
        $client->request('GET', '/login');
        $client->submitForm('Se connecter', [
            '_username' => 'user',
            '_password' => 'password'
        ]);
        $client->request('GET', '/playlists');

        // Vérifier que la page des playlists est accessible après connexion
        $this->assertResponseIsSuccessful();
     
    }

    public function testPlaylistsSortPageIsAccessible(): void
    {
        $client = static::createClient();
        $client->request('GET', '/login');
        $client->submitForm('Se connecter', [
            '_username' => 'user',
            '_password' => 'password'
        ]);
        $client->request('GET', '/playlists/tri/name/ASC');

        // Vérifier que la page de tri des playlists est accessible
        $this->assertResponseIsSuccessful();
        
    }

    public function testPlaylistsSearchPageIsAccessible(): void
    {
        $client = static::createClient();
        $client->request('GET', '/login');
        $client->submitForm('Se connecter', [
            '_username' => 'user',
            '_password' => 'password'
        ]);
        $client->request('GET', '/playlists/recherche/name');

        // Vérifier que la page de recherche des playlists est accessible
        $this->assertResponseIsSuccessful();
      
    }

    public function testShowOneFormationPageIsAccessible(): void
    {
        $client = static::createClient();
        $client->request('GET', '/login');
        $client->submitForm('Se connecter', [
            '_username' => 'user',
            '_password' => 'password'
        ]);
        $client->request('GET', '/formations/formation/1');

        // Vérifier que la page de détails d'une formation est accessible
        $this->assertResponseIsSuccessful();
      
    }

    public function testShowOnePlaylistPageIsAccessible(): void
    {
        $client = static::createClient();$client->request('GET', '/login');
        $client->submitForm('Se connecter', [
            '_username' => 'user',
            '_password' => 'password'
        ]);
        $client->request('GET', '/playlists/playlist/1');

        // Vérifier que la page de détails d'une playlist est accessible
        $this->assertResponseIsSuccessful();
      
    }
 
    
   
/*
    
 
   

    

   

    

    
 * 
 */
}

