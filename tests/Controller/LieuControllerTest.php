<?php

namespace App\Test\Controller;

use App\Entity\Lieu;
use App\Repository\LieuRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LieuControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private LieuRepository $repository;
    private string $path = '/lieu/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = (static::getContainer()->get('doctrine'))->getRepository(Lieu::class);

        foreach ($this->repository->findAll() as $object) {
            $this->repository->remove($object, true);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Lieu index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'lieu[nom]' => 'Testing',
            'lieu[rue]' => 'Testing',
            'lieu[latitude]' => 'Testing',
            'lieu[longitude]' => 'Testing',
            'lieu[ville]' => 'Testing',
        ]);

        self::assertResponseRedirects('/sweet/food/');

        self::assertSame(1, $this->repository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Lieu();
        $fixture->setNom('My Title');
        $fixture->setRue('My Title');
        $fixture->setLatitude('My Title');
        $fixture->setLongitude('My Title');
        $fixture->setVille('My Title');

        $this->repository->add($fixture, true);

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Lieu');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Lieu();
        $fixture->setNom('My Title');
        $fixture->setRue('My Title');
        $fixture->setLatitude('My Title');
        $fixture->setLongitude('My Title');
        $fixture->setVille('My Title');

        $this->repository->add($fixture, true);

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'lieu[nom]' => 'Something New',
            'lieu[rue]' => 'Something New',
            'lieu[latitude]' => 'Something New',
            'lieu[longitude]' => 'Something New',
            'lieu[ville]' => 'Something New',
        ]);

        self::assertResponseRedirects('/lieu/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getNom());
        self::assertSame('Something New', $fixture[0]->getRue());
        self::assertSame('Something New', $fixture[0]->getLatitude());
        self::assertSame('Something New', $fixture[0]->getLongitude());
        self::assertSame('Something New', $fixture[0]->getVille());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Lieu();
        $fixture->setNom('My Title');
        $fixture->setRue('My Title');
        $fixture->setLatitude('My Title');
        $fixture->setLongitude('My Title');
        $fixture->setVille('My Title');

        $this->repository->add($fixture, true);

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/lieu/');
        self::assertSame(0, $this->repository->count([]));
    }
}
