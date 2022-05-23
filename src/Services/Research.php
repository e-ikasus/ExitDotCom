<?php

namespace App\Services;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Constraints as Assert;


class Research extends AbstractController
{
	private $campus;

	private $searchOutingName;

	/**
	 * @Assert\LessThanOrEqual(propertyPath="dateOutingEnd")
	 */
	private $dateOutingStart;

	/**
	 * @Assert\GreaterThanOrEqual(propertyPath="dateOutingStart")
	 */
	private $dateOutingEnd;

	private $sortiesOrganisateur;
	private $sortiesNonInscrit;
	private $sortiesInscrit;
	private $sortiesPassees;

	private $outingCheckboxOptions;

	/**
	 * @return mixed
	 */
	public function getCampus()
	{
		return $this->campus;
	}

	/**
	 * @return mixed
	 */
	public function getOutingCheckboxOptions()
	{
		return $this->outingCheckboxOptions;
	}

	/**
	 * @param mixed $outingCheckboxOptions
	 */
	public function setOutingCheckboxOptions($outingCheckboxOptions): void
	{
		$this->outingCheckboxOptions = $outingCheckboxOptions;
	}

	/**
	 * @return mixed
	 */
	public function getSearchOutingName()
	{
		return $this->searchOutingName;
	}

	/**
	 * @return mixed
	 */
	public function getDateOutingStart()
	{
		return $this->dateOutingStart;
	}

	/**
	 * @return mixed
	 */
	public function getDateOutingEnd()
	{
		return $this->dateOutingEnd;
	}

	/**
	 * @return mixed
	 */
	public function getSortiesOrganisateur()
	{
		return $this->sortiesOrganisateur;
	}

	/**
	 * @return mixed
	 */
	public function getSortiesNonInscrit()
	{
		return $this->sortiesNonInscrit;
	}

	/**
	 * @return mixed
	 */
	public function getSortiesInscrit()
	{
		return $this->sortiesInscrit;
	}

	/**
	 * @return mixed
	 */
	public function getSortiesPassees()
	{
		return $this->sortiesPassees;
	}

	/**
	 * @param mixed $campus
	 */
	public function setCampus($campus): void
	{
		$this->campus = $campus;
	}

	/**
	 * @param mixed $searchOutingName
	 */
	public function setSearchOutingName($searchOutingName): void
	{
		$this->searchOutingName = $searchOutingName;
	}

	/**
	 * @param mixed $dateOutingStart
	 */
	public function setDateOutingStart($dateOutingStart): void
	{
		$this->dateOutingStart = $dateOutingStart;
	}

	/**
	 * @param mixed $dateOutingEnd
	 */
	public function setDateOutingEnd($dateOutingEnd): void
	{
		$this->dateOutingEnd = $dateOutingEnd;
	}

	/**
	 * @param mixed $sortiesOrganisateur
	 */
	public function setSortiesOrganisateur($sortiesOrganisateur): void
	{
		$this->sortiesOrganisateur = $sortiesOrganisateur;
	}

	/**
	 * @param mixed $sortiesNonInscrit
	 */
	public function setSortiesNonInscrit($sortiesNonInscrit): void
	{
		$this->sortiesNonInscrit = $sortiesNonInscrit;
	}

	/**
	 * @param mixed $sortiesInscrit
	 */
	public function setSortiesInscrit($sortiesInscrit): void
	{
		$this->sortiesInscrit = $sortiesInscrit;
	}

	/**
	 * @param mixed $sortiesPassees
	 */
	public function setSortiesPassees($sortiesPassees): void
	{
		$this->sortiesPassees = $sortiesPassees;
	}


}