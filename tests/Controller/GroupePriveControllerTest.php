<?php

namespace App\Test\Controller;

use App\Entity\GroupePrive;
use App\Repository\GroupePriveRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GroupePriveControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private GroupePriveRepository $repository;
    private string $path = '/groupe/prive/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = (static::getContainer()->get('doctrine'))->getRepository(GroupePrive::class);

        foreach ($this->repository->findAll() as $object) {
            $this->repository->remove($object, true);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('GroupePrive index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'groupe_prive[nom]' => 'Testing',
            'groupe_prive[participant]' => 'Testing',
        ]);

        self::assertResponseRedirects('/sweet/food/');

        self::assertSame(1, $this->repository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new GroupePrive();
        $fixture->setNom('My Title');
        $fixture->setParticipant('My Title');

        $this->repository->add($fixture, true);

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('GroupePrive');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new GroupePrive();
        $fixture->setNom('My Title');
        $fixture->setParticipant('My Title');

        $this->repository->add($fixture, true);

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'groupe_prive[nom]' => 'Something New',
            'groupe_prive[participant]' => 'Something New',
        ]);

        self::assertResponseRedirects('/groupe/prive/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getNom());
        self::assertSame('Something New', $fixture[0]->getParticipant());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new GroupePrive();
        $fixture->setNom('My Title');
        $fixture->setParticipant('My Title');

        $this->repository->add($fixture, true);

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/groupe/prive/');
        self::assertSame(0, $this->repository->count([]));
    }
}
