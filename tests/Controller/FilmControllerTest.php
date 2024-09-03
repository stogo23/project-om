<?php

namespace App\Tests\Controller;

use App\Entity\Film;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class FilmControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $repository;
    private string $path = '/film/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->repository = $this->manager->getRepository(Film::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Film index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'film[Titre]' => 'Testing',
            'film[Description]' => 'Testing',
            'film[Annee]' => 'Testing',
            'film[Genre]' => 'Testing',
            'film[Image]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->repository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Film();
        $fixture->setTitre('My Title');
        $fixture->setDescription('My Title');
        $fixture->setAnnee('My Title');
        $fixture->setGenre('My Title');
        $fixture->setImage('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Film');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Film();
        $fixture->setTitre('Value');
        $fixture->setDescription('Value');
        $fixture->setAnnee('Value');
        $fixture->setGenre('Value');
        $fixture->setImage('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'film[Titre]' => 'Something New',
            'film[Description]' => 'Something New',
            'film[Annee]' => 'Something New',
            'film[Genre]' => 'Something New',
            'film[Image]' => 'Something New',
        ]);

        self::assertResponseRedirects('/film/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getTitre());
        self::assertSame('Something New', $fixture[0]->getDescription());
        self::assertSame('Something New', $fixture[0]->getAnnee());
        self::assertSame('Something New', $fixture[0]->getGenre());
        self::assertSame('Something New', $fixture[0]->getImage());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Film();
        $fixture->setTitre('Value');
        $fixture->setDescription('Value');
        $fixture->setAnnee('Value');
        $fixture->setGenre('Value');
        $fixture->setImage('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/film/');
        self::assertSame(0, $this->repository->count([]));
    }
}
