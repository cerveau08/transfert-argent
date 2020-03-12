<?php

namespace App\Entity;

use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use App\Controller\CompteController;
use ApiPlatform\Core\Annotation\ApiFilter;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
/**
 * @ApiResource(
 *  normalizationContext={"groups"={"read"}},
 *  denormalizationContext={"groups"={"post"}},
 * collectionOperations={
 *         "get"={
 *  "controller"=CompteController::class},
 *         "post"={
 * "security"="is_granted(['ROLE_ADMIN_SYSTEM','ROLE_ADMIN'])", "security_message"="Seul ADMIN_SYSTEM ou ADMIN peut creer un compte"
 * }
 *     },
 * itemOperations={
 *     "get"={ 
 * "security"="is_granted(['ROLE_ADMIN_SYSTEM', 'ROLE_ADMIN', 'ROLE_PARTENAIRE', 'ROLE_ADMIN_PARTENAIRE'])"},
 *      "put"={"security"="is_granted(['ROLE_ADMIN_SYSTEM','ROLE_ADMIN'])", "security_message"="Seul ADMIN_SYST peut bloquer un user"}
 * } )
 * @ORM\Entity(repositoryClass="App\Repository\CompteRepository")
 * @ApiFilter(SearchFilter::class, properties={"partenaire.ninea": "exact"})
 */
class Compte
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"post","read"})
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"post","read"})
     */
    private $dateCreation;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"post","read"})
     */
    private $solde;

    /**
     * @ORM\Column(type="string")
     * @Groups({"post","read"})
     */
    private $numeroCompte;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Depot", mappedBy="compte", cascade={"persist"})
     * @Groups({"post","read"})
     * @MaxDepth (1) 
     */
    private $depot;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Partenaire", inversedBy="comptes", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"post","read"})
     */
    private $partenaire;


    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="comptesCreer")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"post","read"})
     */
    private $userCreateur;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"post","read"})   
     */
    private $statut;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Affectation", mappedBy="compte")
     */
    private $affectations;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Transaction", mappedBy="compteEmetteur")
     */
    private $transactionE;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Transaction", mappedBy="compteRecepteur")
     */
    private $transactionR;

    
    public function __construct()
    {
        $this->statut = "actif";
        $this->depot = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->affectations = new ArrayCollection();
        $this->transactionE = new ArrayCollection();
        $this->transactionR = new ArrayCollection();
        $this->dateCreation=  new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeInterface $dateCreation): self
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function getSolde(): ?int
    {
        return $this->solde;
    }

    public function setSolde(int $solde): self
    {
        $this->solde = $solde;

        return $this;
    }

    public function getNumeroCompte(): ?string
    {
        return $this->numeroCompte;
    }

    public function setNumeroCompte(string $numeroCompte): self
    {
        $this->numeroCompte = $numeroCompte;

        return $this;
    }

    /**
     * @return Collection|Depot[]
     */
    public function getDepot(): Collection
    {
        return $this->depot;
    }

    public function addDepot(Depot $depot): self
    {
        if (!$this->depot->contains($depot)) {
            $this->depot[] = $depot;
            $depot->setCompte($this);
        }

        return $this;
    }

    public function removeDepot(Depot $depot): self
    {
        if ($this->depot->contains($depot)) {
            $this->depot->removeElement($depot);
            // set the owning side to null (unless already changed)
            if ($depot->getCompte() === $this) {
                $depot->setCompte(null);
            }
        }

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

   
    public function getUserCreateur(): ?User
    {
        return $this->userCreateur;
    }

    public function setUserCreateur(?User $userCreateur): self
    {
        $this->userCreateur = $userCreateur;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): self
    {
        $this->statut = $statut;

        return $this;
    }

    /**
     * @return Collection|Affectation[]
     */
    public function getAffectations(): Collection
    {
        return $this->affectations;
    }

    public function addAffectation(Affectation $affectation): self
    {
        if (!$this->affectations->contains($affectation)) {
            $this->affectations[] = $affectation;
            $affectation->setCompte($this);
        }

        return $this;
    }

    public function removeAffectation(Affectation $affectation): self
    {
        if ($this->affectations->contains($affectation)) {
            $this->affectations->removeElement($affectation);
            // set the owning side to null (unless already changed)
            if ($affectation->getCompte() === $this) {
                $affectation->setCompte(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Transaction[]
     */
    public function getTransactionE(): Collection
    {
        return $this->transactionE;
    }

    public function addTransactionE(Transaction $transactionE): self
    {
        if (!$this->transactionE->contains($transactionE)) {
            $this->transactionE[] = $transactionE;
            $transactionE->setCompteRecepteur($this);
        }

        return $this;
    }

    public function removeTransactionE(Transaction $transactionE): self
    {
        if ($this->transactionE->contains($transactionE)) {
            $this->transactionE->removeElement($transactionE);
            // set the owning side to null (unless already changed)
            if ($transactionE->getCompteRecepteur() === $this) {
                $transactionE->setCompteRecepteur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Transaction[]
     */
    public function getTransactionR(): Collection
    {
        return $this->transactionR;
    }

    public function addTransactionR(Transaction $transactionR): self
    {
        if (!$this->transactionR->contains($transactionR)) {
            $this->transactionR[] = $transactionR;
            $transactionR->setCompteRecepteur($this);
        }

        return $this;
    }

    public function removeTransactionR(Transaction $transactionR): self
    {
        if ($this->transactionR->contains($transactionR)) {
            $this->transactionR->removeElement($transactionR);
            // set the owning side to null (unless already changed)
            if ($transactionR->getCompteRecepteur() === $this) {
                $transactionR->setCompteRecepteur(null);
            }
        }

        return $this;
    }

}
