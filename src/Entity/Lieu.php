<?php

namespace App\Entity;

use App\Repository\LieuRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=LieuRepository::class)
 */
class Lieu
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
     *     pattern="/^[[:alpha:]]([-' ]?[[:alpha:]])*$/",
     *     message="Seuls les lettres et les symboles - et ' sont autorisés"
     * )
     * @Assert\Length(
     *     max=48,
     *     maxMessage="Le nom doit faire au maximum {{ limit }} caractères"
     * )
     * @ORM\Column(type="string", length=64)
     */
    private $nom;

    /**
     * @Assert\NotBlank(message="Veuillez saisir une rue")
     * @Assert\Regex(
     *     pattern="/^[[:alpha:]]([-' ]*[[:alpha:]])*$/",
     *     message="Seuls les lettres et les symboles - et ' sont autorisés"
     * )
     *
     * @ORM\Column(type="string", length=128)
     */
    private $rue;

    /**
     * @Assert\NotBlank(message="Veuillez saisir une coordonée")
     * @Assert\Regex(
     *     pattern="/^-?\d+[.,]?\d*$/",
     *     message="Seuls les formats en degrés decimaux sont autorisés"
     * )
     *
     * @ORM\Column(type="float")
     */
    private $latitude;

    /**
     * @Assert\NotBlank(message="Veuillez saisir une coordonée")
     * @Assert\Regex(
     *     pattern="/^-?\d+[.,]?\d*$/",
     *     message="Seuls les formats en degrés decimaux sont autorisés"
     * )
     *
     * @ORM\Column(type="float")
     */
    private $longitude;

    /**
     * @ORM\ManyToOne(targetEntity=Ville::class, inversedBy="lieux")
     * @ORM\JoinColumn(nullable=false)
     */
    private $ville;

    /**
     * @ORM\OneToMany(targetEntity=Sortie::class, mappedBy="Lieu", orphanRemoval=true)
     */
    private $sorties;

    public function __construct()
    {
        $this->sorties = new ArrayCollection();
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

    public function getRue(): ?string
    {
        return $this->rue;
    }

    public function setRue(string $rue): self
    {
        $this->rue = $rue;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getVille(): ?Ville
    {
        return $this->ville;
    }

    public function setVille(?Ville $ville): self
    {
        $this->ville = $ville;

        return $this;
    }

    /**
     * @return Collection<int, Sortie>
     */
    public function getSorties(): Collection
    {
        return $this->sorties;
    }

    public function addSortie(Sortie $sortie): self
    {
        if (!$this->sorties->contains($sortie)) {
            $this->sorties[] = $sortie;
            $sortie->setLieu($this);
        }

        return $this;
    }

    public function removeSortie(Sortie $sortie): self
    {
        if ($this->sorties->removeElement($sortie)) {
            // set the owning side to null (unless already changed)
            if ($sortie->getLieu() === $this) {
                $sortie->setLieu(null);
            }
        }

        return $this;
    }
}
