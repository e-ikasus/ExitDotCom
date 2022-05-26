<?php

namespace App\Entity;

use App\Repository\ParticipantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ParticipantRepository::class)
 * @UniqueEntity(fields={"pseudo"}, message="Il existe déja un compte avec ce pseudo")
 * @UniqueEntity(fields={"email"}, message="Il existe déja un compte avec cet email")
 */
class Participant implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotBlank(message="Veuillez saisir un pseudo")
     * @Assert\Length(
     *      min=3,
     *      max=12,
     *      minMessage="Le pseudo doit faire au minimum {{ limit }} caractères",
     *      maxMessage="Le pseudo doit faire au maximum {{ limit }} caractères"
     * )
     *
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $pseudo;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @Assert\NotBlank(message="Veuillez saisir un mot de passe")
     * @Assert\Regex(
     *     pattern="/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[-+!;:#*$@%_])([-+!;:#*$@%_\w]{6,48})$/",
     *     message="Le mot de passe doit contenir une majuscule, une minuscule, un chiffre, un des caractères suivants : -+!;:#*$@%_ et faire entre 6 et 48 caractères"
     * )
     *
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @Assert\NotBlank(message="Veuillez saisir un prénom")
     * @Assert\Regex(
     *     pattern="/^[[:alpha:]]([-' ]*[[:alpha:]])*$/",
     *     message="Seuls les lettres et les symboles - et ' sont autorisés"
     * )
     * @Assert\Length(
     *     max = 24,
     *     maxMessage="Le prénom doit faire au maximum {{ limit }} caractères"
     * )
     *
     * @ORM\Column(type="string", length=64)
     */
    private $prenom;

    /**
     * @Assert\NotBlank(message="Veuillez saisir un nom")
     * @Assert\Regex(
     *     pattern="/^[[:alpha:]]([-' ]*[[:alpha:]])*$/",
     *     message="Seuls les lettres et les symboles - et ' sont autorisés"
     * )
     * @Assert\Length(
     *     max=48,
     *     maxMessage="Le nom doit faire au maximum {{ limit }} caractères"
     * )
     *
     * @ORM\Column(type="string", length=64)
     */
    private $nom;

    /**
     * @Assert\Regex(
     *     pattern="/^(\+33|0)[1-79]\d{8}$/",
     *     message="Le numéro doit être au format +33 ou 0 suivi du reste du numéro, les 08 ne sont pas acceptés"
     * )
     * @Assert\NotBlank(message="Veuillez saisir un numéro de téléphone")
     *
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $telephone;

    /**
     * @Assert\NotBlank(message="Veuillez saisir un email")
     * @Assert\Email(
     *     message="'{{ value }}' n'est pas un email valide."
     * )
     *
     * @ORM\Column(type="string", length=64)
     */
    private $email;

    /**
     * @ORM\Column(type="boolean")
     */
    private $administrateur;

    /**
     * @ORM\Column(type="boolean")
     */
    private $actif;

    /**
     * @Assert\NotBlank(message="Veuillez selectionner un campus")
     *
     * @ORM\ManyToOne(targetEntity=Campus::class, inversedBy="participants")
     * @ORM\JoinColumn(nullable=false)
     */
    private $campus;

    /**
     *
     * !! retrait de orphanRemoval=true pour éviter que la suppression d'un participant supprime aussi la sortie !!
     *
     * @ORM\OneToMany(targetEntity=Sortie::class, mappedBy="organisateur")
     */
    private $sortiesOrganisees;

    /**
     * @ORM\ManyToMany(targetEntity=Sortie::class, inversedBy="participants")
     */
    private $sortiesInscrit;

    /**
     * @Assert\File(
     *     maxSize="10M",
     *     mimeTypes={"image/jpeg","image/png"},
     *     mimeTypesMessage="Merci d'utiliser un des formats suivant : .jpg .jpeg .png",
     *     maxSizeMessage="L'image fait ({{ size }} {{ suffix }}). La taille maximale autorisée est de {{ limit }} {{ suffix }}."
     * )
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $photo;

    /**
     * @ORM\ManyToMany(targetEntity=GroupePrive::class, mappedBy="participant")
     */
    private $groupePrives;

    /**
     * @ORM\OneToMany(targetEntity=GroupePrive::class, mappedBy="createur")
     */
    private $groupesOrganises;

    public function __construct()
    {
        $this->sortiesOrganisees = new ArrayCollection();
        $this->sortiesInscrit = new ArrayCollection();
        $this->groupePrives = new ArrayCollection();
        $this->groupesOrganises = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): self
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string)$this->pseudo;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string)$this->pseudo;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
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

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function isAdministrateur(): ?bool
    {
        return $this->administrateur;
    }

    public function setAdministrateur(bool $administrateur): self
    {
        $this->administrateur = $administrateur;

        return $this;
    }

    public function isActif(): ?bool
    {
        return $this->actif;
    }

    public function setActif(bool $actif): self
    {
        $this->actif = $actif;

        return $this;
    }

    public function getCampus(): ?Campus
    {
        return $this->campus;
    }

    public function setCampus(?Campus $campus): self
    {
        $this->campus = $campus;

        return $this;
    }

    /**
     * @return Collection<int, Sortie>
     */
    public function getSortiesOrganisees(): Collection
    {
        return $this->sortiesOrganisees;
    }

    public function addSortiesOrganisee(Sortie $sortiesOrganisee): self
    {
        if (!$this->sortiesOrganisees->contains($sortiesOrganisee)) {
            $this->sortiesOrganisees[] = $sortiesOrganisee;
            $sortiesOrganisee->setOrganisateur($this);
        }

        return $this;
    }

    public function removeSortiesOrganisee(Sortie $sortiesOrganisee): self
    {
        if ($this->sortiesOrganisees->removeElement($sortiesOrganisee)) {
            // set the owning side to null (unless already changed)
            if ($sortiesOrganisee->getOrganisateur() === $this) {
                $sortiesOrganisee->setOrganisateur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Sortie>
     */
    public function getSortiesInscrit(): Collection
    {
        return $this->sortiesInscrit;
    }

    public function addSortiesInscrit(Sortie $sortiesInscrit): self
    {
        if (!$this->sortiesInscrit->contains($sortiesInscrit)) {
            $this->sortiesInscrit[] = $sortiesInscrit;
        }

        return $this;
    }

    public function removeSortiesInscrit(Sortie $sortiesInscrit): self
    {
        $this->sortiesInscrit->removeElement($sortiesInscrit);

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    /**
     * @return Collection<int, GroupePrive>
     */
    public function getGroupePrives(): Collection
    {
        return $this->groupePrives;
    }

    public function addGroupePrife(GroupePrive $groupePrife): self
    {
        if (!$this->groupePrives->contains($groupePrife)) {
            $this->groupePrives[] = $groupePrife;
            $groupePrife->addParticipant($this);
        }

        return $this;
    }

    public function removeGroupePrife(GroupePrive $groupePrife): self
    {
        if ($this->groupePrives->removeElement($groupePrife)) {
            $groupePrife->removeParticipant($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, GroupePrive>
     */
    public function getGroupesOrganises(): Collection
    {
        return $this->groupesOrganises;
    }

    public function addGroupesOrganise(GroupePrive $groupesOrganise): self
    {
        if (!$this->groupesOrganises->contains($groupesOrganise)) {
            $this->groupesOrganises[] = $groupesOrganise;
            $groupesOrganise->setCreateur($this);
        }

        return $this;
    }

    public function removeGroupesOrganise(GroupePrive $groupesOrganise): self
    {
        if ($this->groupesOrganises->removeElement($groupesOrganise)) {
            // set the owning side to null (unless already changed)
            if ($groupesOrganise->getCreateur() === $this) {
                $groupesOrganise->setCreateur(null);
            }
        }

        return $this;
    }
}
