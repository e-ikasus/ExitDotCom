<?php

namespace App\Entity;

use App\Repository\EtatRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Integer;

/**
 * @ORM\Entity(repositoryClass=EtatRepository::class)
 */
class Etat
{
	const OUVERTE = 0;
	const CLOTUREE = 1;
	const ENCOURS = 2;
	const TERMINEE = 3;
	const ANNULEE = 4;
	const ARCHIVEE = 5;
	const ENCREATION = 6;

	/**
	 * @ORM\Id
	 * @ORM\GeneratedValue
	 * @ORM\Column(type="integer")
	 */
	private $id;

	/**
	 * @ORM\Column(type="integer", unique=true)
	 */
	private $idLibelle;

	/**
	 * @ORM\Column(type="string", length=32)
	 */
	private $libelle;

	/**
	 * @ORM\OneToMany(targetEntity=Sortie::class, mappedBy="etat", orphanRemoval=true)
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

	public function getLibelle(): ?string
	{
		return $this->libelle;
	}

	public function setLibelle(string $libelle): self
	{
		$this->libelle = $libelle;

		return $this;
	}

	public function getIdLibelle(): ?int
	{
		return $this->idLibelle;
	}

	public function setIdLibelle(int $idLibelle): self
	{
		$this->idLibelle = $idLibelle;

		return $this;
	}

	/**
	 * @return Collection<int, Sortie>
	 */
	public function getSorties(): Collection
	{
		return $this->sorties;
	}

	public function addSorty(Sortie $sorty): self
	{
		if (!$this->sorties->contains($sorty))
		{
			$this->sorties[] = $sorty;
			$sorty->setEtat($this);
		}

		return $this;
	}

	public function removeSorty(Sortie $sorty): self
	{
		if ($this->sorties->removeElement($sorty))
		{
			// set the owning side to null (unless already changed)
			if ($sorty->getEtat() === $this)
			{
				$sorty->setEtat(null);
			}
		}

		return $this;
	}
}
