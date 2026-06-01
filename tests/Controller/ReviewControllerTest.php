<?php

namespace App\Tests\Controller;

use App\Entity\Review;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ReviewControllerTest extends WebTestCase
{
    /**
     * Segédmetódus a teszt-adatbázis sémájának letisztításához és újraépítéséhez.
     * Miért: Mivel nincs setUp(), ezt a kliens létrehozása után hívjuk meg,
     * így teljesen tiszta, izolált adatbázist kapunk minden tesztfutás elején.
     */
    private function initDatabase(EntityManagerInterface $entityManager): void
    {
        $metaData = $entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($entityManager);
        $schemaTool->dropSchema($metaData);
        $schemaTool->createSchema($metaData);
    }

    public function testSubmitReviewAndSeeOnHomepage(): void
    {
        // 1. Létrehozzuk a klienst (ez bebootolja a kernelt a háttérben biztonságosan)
        $client = static::createClient();

        // 2. Elkérjük az Entity Managert a friss kliens containeréből és inicializáljuk a DB-t
        $entityManager = static::getContainer()->get('doctrine.orm.entity_manager');
        $this->initDatabase($entityManager);

        // 3. Teszteljük az oldalt és a formot
        $crawler = $client->request('GET', '/');
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Vélemény elküldése')->form([
            'review[companyName]' => 'Google E2E Inc.',
            'review[rating]' => 4,
            'review[reviewText]' => 'Ez egy automatizált funkcionális teszt vélemény szövege.',
            'review[authorEmail]' => 'tester@e2e.com',
        ]);

        $client->submit($form);

        // Átirányítás ellenőrzése
        $this->assertResponseRedirects('/');
        $client->followRedirect();

        $this->assertSelectorTextContains('.alert-success', 'Köszönjük a véleményed!');
        $this->assertSelectorTextContains('body', 'Google E2E Inc.');
    }

    public function testCompanyStatisticsLogic(): void
    {
        // 1. Kliens létrehozása
        $client = static::createClient();

        // 2. Entity Manager elkérése és DB inicializálása
        $entityManager = static::getContainer()->get('doctrine.orm.entity_manager');
        $this->initDatabase($entityManager);

        // 3. Tesztadatok felvitele manuálisan
        $review1 = (new Review())->setCompanyName('Calc Kft.')->setRating(5)->setReviewText('Szuper')->setAuthorEmail('a@b.com');
        $review2 = (new Review())->setCompanyName('Calc Kft.')->setRating(3)->setReviewText('Oké')->setAuthorEmail('b@b.com');

        $review1->setInitialTimestamps();
        $review2->setInitialTimestamps();

        $entityManager->persist($review1);
        $entityManager->persist($review2);
        $entityManager->flush();

        // 4. Statisztika oldal megnyitása és ellenőrzése
        $client->request('GET', '/companies');

        $this->assertResponseIsSuccessful();

        // Az átlag (5+3)/2 = 4.00 -> '4,00'
        $this->assertSelectorTextContains('body', '4,00');
        // A darabszámnak 2-nek kell lennie
        $this->assertSelectorTextContains('body', '2');
    }
}
