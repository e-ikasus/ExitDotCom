<?php

namespace App\Entity;

use App\Repository\LogDebugRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LogDebugRepository::class)
 */
class LogDebug
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\ManyToOne(targetEntity=Sortie::class)
     */
    private $sortie;

    /**
     * @ORM\ManyToOne(targetEntity=Etat::class)
     */
    private $ancienEtat;

    /**
     * @ORM\ManyToOne(targetEntity=Etat::class)
     */
    private $nouvelEtat;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $content;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getSortie(): ?Sortie
    {
        return $this->sortie;
    }

    public function setSortie(?Sortie $sortie): self
    {
        $this->sortie = $sortie;

        return $this;
    }

    public function getAncienEtat(): ?Etat
    {
        return $this->ancienEtat;
    }

    public function setAncienEtat(?Etat $ancienEtat): self
    {
        $this->ancienEtat = $ancienEtat;

        return $this;
    }

    public function getNouvelEtat(): ?Etat
    {
        return $this->nouvelEtat;
    }

    public function setNouvelEtat(?Etat $nouvelEtat): self
    {
        $this->nouvelEtat = $nouvelEtat;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }
}
