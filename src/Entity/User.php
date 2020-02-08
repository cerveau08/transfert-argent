<?php

namespace App\Entity;


use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ApiResource(
 * collectionOperations={
 *          "post"={"access_control"="is_granted('POST', object)"}
 *     },
 * )
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields={"username"}, message="Cet utilisateur existe déjà")
 * @UniqueEntity(fields={"email"}, message="Cet utilisateur existe déjà")
 * @Vich\Uploadable
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255,  unique=true)
     * @Assert\NotBlank(message="Le champ ne doit pas être vide")
     * @Assert\Email(
     *     message = "Votre Email'{{ value }}' n'est pas un email valide."
     * )
     * @Groups("get")
     */
    private $email;


    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Groups("get")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Le champ ne doit pas être vide")
     * @Assert\Length(min="2", minMessage="Ce champ doit contenir un minimum de {{ limit }} caractères", max="255", maxMessage="Ce champ doit contenir un maximum de {{ limit }} caractères")
     * @Groups("get")
     */
    private $username;

    /**
     * @ORM\Column(type="boolean")
     * @Groups("get")
     */
    private $isActive;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Profil", inversedBy="users")
     * @ORM\JoinColumn(nullable=false)
     * @Groups("get")
     * @ApiSubresource(maxDepth=1) 
     */
    private $profil;


    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Depot", mappedBy="caissierAdd")
     */
    private $depots;

    /**
     * @var MediaObject|null
     *
     * @ORM\ManyToOne(targetEntity=MediaObject::class)
     * @ORM\JoinColumn(nullable=true)
     * @ApiProperty(iri="http://schema.org/image")
     */
    public $image;


    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Compte", inversedBy="users")
     */
    private $compte;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Partenaire", inversedBy="user", cascade={"persist", "remove"})
     */
    private $partenaire;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Transaction", mappedBy="userCompteE")
     */
    private $transactions;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Compte", mappedBy="userCreateur")
     */
    private $comptesCreer;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Partenaire", mappedBy="userCreateur")
     */
    private $partenairesCreer;

    public function __construct()
    {
        $this->isActive = true;
        $this->depots = new ArrayCollection();
        $this->transactions = new ArrayCollection();
        $this->comptesCreer = new ArrayCollection();
        $this->partenairesCreer = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles()
    {
        $roles[] = strtoupper($this->profil->getLibelle());
       return array_unique($roles); 
    }


    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getProfil(): ?Profil
    {
        return $this->profil;
    }

    public function setProfil(?Profil $profil): self
    {
        $this->profil = $profil;

        return $this;
    }


     /**
      * @return Collection|Depot[]
      */
     public function getDepots(): Collection
     {
         return $this->depots;
     }

     public function addDepot(Depot $depot): self
     {
         if (!$this->depots->contains($depot)) {
             $this->depots[] = $depot;
             $depot->setCaissierAdd($this);
         }

         return $this;
     }

     public function removeDepot(Depot $depot): self
     {
         if ($this->depots->contains($depot)) {
             $this->depots->removeElement($depot);
             // set the owning side to null (unless already changed)
             if ($depot->getCaissierAdd() === $this) {
                 $depot->setCaissierAdd(null);
             }
         }

         return $this;
     }


     public function getCompte(): ?Compte
     {
         return $this->compte;
     }

     public function setCompte(?Compte $compte): self
     {
         $this->compte = $compte;

         return $this;
     }

     public function getPartenaire(): ?Partenaire
     {
         return $this->partenaire;
     }

     public function setPartenaire(?Partenaire $partenaire): self
     {
         $this->partenaire = $partenaire;

         return $this;
     }

     /**
      * @return Collection|Transaction[]
      */
     public function getTransactions(): Collection
     {
         return $this->transactions;
     }

     public function addTransaction(Transaction $transaction): self
     {
         if (!$this->transactions->contains($transaction)) {
             $this->transactions[] = $transaction;
             $transaction->setUserCompteE($this);
         }

         return $this;
     }

     public function removeTransaction(Transaction $transaction): self
     {
         if ($this->transactions->contains($transaction)) {
             $this->transactions->removeElement($transaction);
             // set the owning side to null (unless already changed)
             if ($transaction->getUserCompteE() === $this) {
                 $transaction->setUserCompteE(null);
             }
         }

         return $this;
     }

     /**
      * @return Collection|Compte[]
      */
     public function getComptesCreer(): Collection
     {
         return $this->comptesCreer;
     }

     public function addComptesCreer(Compte $comptesCreer): self
     {
         if (!$this->comptesCreer->contains($comptesCreer)) {
             $this->comptesCreer[] = $comptesCreer;
             $comptesCreer->setUserCreateur($this);
         }

         return $this;
     }

     public function removeComptesCreer(Compte $comptesCreer): self
     {
         if ($this->comptesCreer->contains($comptesCreer)) {
             $this->comptesCreer->removeElement($comptesCreer);
             // set the owning side to null (unless already changed)
             if ($comptesCreer->getUserCreateur() === $this) {
                 $comptesCreer->setUserCreateur(null);
             }
         }

         return $this;
     }

     /**
      * @return Collection|Partenaire[]
      */
     public function getPartenairesCreer(): Collection
     {
         return $this->partenairesCreer;
     }

     public function addPartenairesCreer(Partenaire $partenairesCreer): self
     {
         if (!$this->partenairesCreer->contains($partenairesCreer)) {
             $this->partenairesCreer[] = $partenairesCreer;
             $partenairesCreer->setUserCreateur($this);
         }

         return $this;
     }

     public function removePartenairesCreer(Partenaire $partenairesCreer): self
     {
         if ($this->partenairesCreer->contains($partenairesCreer)) {
             $this->partenairesCreer->removeElement($partenairesCreer);
             // set the owning side to null (unless already changed)
             if ($partenairesCreer->getUserCreateur() === $this) {
                 $partenairesCreer->setUserCreateur(null);
             }
         }

         return $this;
     }


   /* public function getImage(): ?Images
    {
        return $this->image;
    }

    public function setImage(?Images $image): self
    {
        $this->image = $image;

        return $this;
    } 
    */
}
