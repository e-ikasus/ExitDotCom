<?php

namespace App\Entity;

use App\Repository\GroupePriveRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=GroupePriveRepository::class)
 */
class GroupePrive
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotBlank(message="Veuillez saisir un nom")
     * @Assert\Regex(
     *     pattern="/^[[:alpha:]]([-' ]*[[:alpha:]])*$/",
     *     message="Seuls les lettres et les symboles - et ' sont autorisés"
     * )
     * @Assert\Length(
     *     max=30,
     *     maxMessage="Le nom doit faire au maximum {{ limit }} caractères"
     * )
     * @ORM\Column(type="string", length=50)
     */
    private $nom;

    /**
     * @ORM\ManyToMany(targetEntity=Participant::class, inversedBy="groupePrives")
     */
    private $participant;

    /**
     * @ORM\ManyToOne(targetEntity=Participant::class, inversedBy="groupesOrganises")
     * @ORM\JoinColumn(nullable=true)
     */
    private $createur;

    public function __construct()
    {
        $this->participant = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * @return Collection<int, Participant>
     */
    public function getParticipant(): Collection
    {
        return $this->participant;
    }

    public function addParticipant(Participant $participant): self
    {
        if (!$this->participant->contains($participant)) {
            $this->participant[] = $participant;
        }

        return $this;
    }

    public function removeParticipant(Participant $participant): self
    {
        $this->participant->removeElement($participant);

        return $this;
    }

    public function getCreateur(): ?Participant
    {
        return $this->createur;
    }

    public function setCreateur(?Participant $createur): self
    {
        $this->createur = $createur;

        return $this;
    }
}
