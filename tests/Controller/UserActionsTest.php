<?php

namespace App\Tests;

use Symfony\Component\Panther\PantherTestCase;

class UserActionsTest extends PantherTestCase
{
    /**
     * Un utilisateur peut s'inscrire sur le Blog.
     */
    public function testRegistration()
    {
        $client = static::createPantherClient(['browser' => static::FIREFOX]);
        $crawler = $client->request('GET', '/previous/concerts/photos-fete-de-la-musique-2019-jardin-des-apothicaires-dijon');

        // Ce formulaire est généré en Javascript
        //$client->waitFor('#inscription-form');

        // Soumission du formulaire
        $client->submitForm('Envoyer', [
            'comments[pseudo]' => 'zozor',
            'comments[content]' => 'Zoz0rIsHome Zoz0rIsHome Zoz0rIsHome Zoz0rIsHome',
        ]);

        // Redirection de l'utilisateur nouvellement inscrit vers l'accueil
        $this->assertSame(self::$baseUri.'/previous/concerts/photos-fete-de-la-musique-2019-jardin-des-apothicaires-dijon', $client->getCurrentURL());

        // Notification de succès en Javascript
        $client->waitFor('div.success');
        //$this->assertSame('Commentaire enregistré', $crawler->filter('div.success')->text());

        // L'utilisateur est bien authentifié
        //$this->assertSame('Zozor', $crawler->filter('#user-profile span:first-child')->text());
    }
}
