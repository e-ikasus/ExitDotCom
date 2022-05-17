<?php

namespace App\Test\Controller;

use App\Entity\Participant;
use App\Repository\ParticipantRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ParticipantControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private ParticipantRepository $repository;
    private string $path = '/participant/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = (static::getContainer()->get('doctrine'))->getRepository(Participant::class);

        foreach ($this->repository->findAll() as $object) {
            $this->repository->remove($object, true);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Participant index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'participant[pseudo]' => 'Testing',
            'participant[roles]' => 'Testing',
            'participant[password]' => 'Testing',
            'participant[prenom]' => 'Testing',
            'participant[nom]' => 'Testing',
            'participant[telephone]' => 'Testing',
            'participant[email]' => 'Testing',
            'participant[administrateur]' => 'Testing',
            'participant[actif]' => 'Testing',
            'participant[photo]' => 'Testing',
            'participant[campus]' => 'Testing',
            'participant[sortiesInscrit]' => 'Testing',
        ]);

        self::assertResponseRedirects('/sweet/food/');

        self::assertSame(1, $this->repository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Participant();
        $fixture->setPseudo('My Title');
        $fixture->setRoles('My Title');
        $fixture->setPassword('My Title');
        $fixture->setPrenom('My Title');
        $fixture->setNom('My Title');
        $fixture->setTelephone('My Title');
        $fixture->setEmail('My Title');
        $fixture->setAdministrateur('My Title');
        $fixture->setActif('My Title');
        $fixture->setPhoto('My Title');
        $fixture->setCampus('My Title');
        $fixture->setSortiesInscrit('My Title');

        $this->repository->add($fixture, true);

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Participant');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Participant();
        $fixture->setPseudo('My Title');
        $fixture->setRoles('My Title');
        $fixture->setPassword('My Title');
        $fixture->setPrenom('My Title');
        $fixture->setNom('My Title');
        $fixture->setTelephone('My Title');
        $fixture->setEmail('My Title');
        $fixture->setAdministrateur('My Title');
        $fixture->setActif('My Title');
        $fixture->setPhoto('My Title');
        $fixture->setCampus('My Title');
        $fixture->setSortiesInscrit('My Title');

        $this->repository->add($fixture, true);

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'participant[pseudo]' => 'Something New',
            'participant[roles]' => 'Something New',
            'participant[password]' => 'Something New',
            'participant[prenom]' => 'Something New',
            'participant[nom]' => 'Something New',
            'participant[telephone]' => 'Something New',
            'participant[email]' => 'Something New',
            'participant[administrateur]' => 'Something New',
            'participant[actif]' => 'Something New',
            'participant[photo]' => 'Something New',
            'participant[campus]' => 'Something New',
            'participant[sortiesInscrit]' => 'Something New',
        ]);

        self::assertResponseRedirects('/participant/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getPseudo());
        self::assertSame('Something New', $fixture[0]->getRoles());
        self::assertSame('Something New', $fixture[0]->getPassword());
        self::assertSame('Something New', $fixture[0]->getPrenom());
        self::assertSame('Something New', $fixture[0]->getNom());
        self::assertSame('Something New', $fixture[0]->getTelephone());
        self::assertSame('Something New', $fixture[0]->getEmail());
        self::assertSame('Something New', $fixture[0]->getAdministrateur());
        self::assertSame('Something New', $fixture[0]->getActif());
        self::assertSame('Something New', $fixture[0]->getPhoto());
        self::assertSame('Something New', $fixture[0]->getCampus());
        self::assertSame('Something New', $fixture[0]->getSortiesInscrit());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Participant();
        $fixture->setPseudo('My Title');
        $fixture->setRoles('My Title');
        $fixture->setPassword('My Title');
        $fixture->setPrenom('My Title');
        $fixture->setNom('My Title');
        $fixture->setTelephone('My Title');
        $fixture->setEmail('My Title');
        $fixture->setAdministrateur('My Title');
        $fixture->setActif('My Title');
        $fixture->setPhoto('My Title');
        $fixture->setCampus('My Title');
        $fixture->setSortiesInscrit('My Title');

        $this->repository->add($fixture, true);

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/participant/');
        self::assertSame(0, $this->repository->count([]));
    }
}
