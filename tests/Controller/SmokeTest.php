<?php

// tests/Controller/SmokeTest.php

namespace App\Tests;

use Symfony\Component\Panther\PantherTestCase;

class SmokeTest extends PantherTestCase
{
    // /**
    //  * @dataProvider provideUrls
    //  */
    // public function testPageIsSuccessful1($pageName, $url)
    // {
    //     $client = self::createClient();
    //     $client->catchExceptions(false);
    //     $client->request('GET', $url);
    //     $response = $client->getResponse();

    //     self::assertTrue(
    //         $response->isSuccessful(),
    //         sprintf(
    //             'La page "%s" devrait être accessible, mais le code HTTP est "%s".',
    //             $pageName,
    //             $response->getStatusCode()
    //         )
    //     );
    // }

    /**
     * @dataProvider provideUrls
     */
    public function testPageIsSuccessful2($pageName, $url)
    {
        // $client = self::createClient(); //fonctionne toujours, au besoin
        $client = static::createPantherClient(['browser' => static::FIREFOX]);
        $crawler = $client->request('GET', $url);
        self::assertCount(
            1,
            $crawler->filter('title'),
            sprintf(
                'La page "%s" devrait être accessible.',
                $pageName
            )
        );
    }

    public function provideUrls()
    {
        return [
            'home' => ['home', '/'],
            'pastConcerts' => ['Photos concerts', '/previous/concerts/'],
            'pastConcert1' => ['Lire', '/previous/concerts/photos-la-peniche-cancale-14-novembre-2019-dijon'],
            'pastConcert2' => ['Lire', '/previous/concerts/photos-fete-de-la-musique-2019-jardin-des-apothicaires-dijon'],
        ];
    }
}
