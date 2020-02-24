<?php

namespace App\Entity;

use App\Entity\Compte;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

/** 
 * @ApiResource(
 *  normalizationContext={"groups"={"read"}},
 *  denormalizationContext={"groups"={"post"}},
 * collectionOperations={
 *          "post"={"access_control"="is_granted('POST', object)"}
 *     },
 * )
 * @ORM\Entity(repositoryClass="App\Repository\PartenaireRepository")
 */
class Partenaire
{
    /** 
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /** 
     * @ORM\Column(type="string", length=255)
     * @Groups({"post","read"})
     */
    private $nomComplet;

    /** 
     * @ORM\Column(type="string", length=255)
     * @Groups({"post","read"})
     */
    private $ninea;

    /** 
     * @ORM\Column(type="string", length=255)
     * @Groups({"post","read"})
     */
    private $registreCommercial;

    /** 
     * @ORM\Column(type="string", length=255)
     * @Groups({"post","read"})
     */
    private $adresse;

    /** 
     * @ORM\Column(type="string", length=255)
     * @Groups({"post","read"})
     */
    private $telephone;

    /** 
     * @ORM\OneToMany(targetEntity="App\Entity\Compte", mappedBy="partenaire")
     * @Groups({"post","read"})
     */
    private $comptes;

    /** 
     * @ORM\OneToMany(targetEntity="App\Entity\User", mappedBy="partenaire", cascade={"persist"})
     * @Groups({"post","read"})
     */
    private $userComptePartenaire;

    /** 
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="partenairesCreer")
     * @ORM\JoinColumn(nullable=false)
     */
    private $userCreateur;

    /** 
     * @ORM\OneToOne(targetEntity="App\Entity\Contrat", mappedBy="partenaire", cascade={"persist", "remove"})
     * @Groups({"post","read"})
     */
    private $contrat;

   


    public function __construct()
    {
        $this->comptes = new ArrayCollection();
        $this->userComptePartenaire = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomComplet(): ?string
    {
        return $this->nomComplet;
    }

    public function setNomComplet(string $nomComplet): self
    {
        $this->nomComplet = $nomComplet;

        return $this;
    }

    public function getNinea(): ?string
    {
        return $this->ninea;
    }

    public function setNinea(string $ninea): self
    {
        $this->ninea = $ninea;

        return $this;
    }

    public function getRegistreCommercial(): ?string
    {
        return $this->registreCommercial;
    }

    public function setRegistreCommercial(string $registreCommercial): self
    {
        $this->registreCommercial = $registreCommercial;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    /** 
     * @return Collection|Compte[]
     */
    public function getComptes(): Collection
    {
        return $this->comptes;
    }

    public function addCompte(Compte $compte): self
    {
        if (!$this->comptes->contains($compte)) {
            $this->comptes[] = $compte;
            $compte->setPartenaire($this);
        }

        return $this;
    }

    public function removeCompte(Compte $compte): self
    {
        if ($this->comptes->contains($compte)) {
            $this->comptes->removeElement($compte);
            // set the owning side to null (unless already changed)
            if ($compte->getPartenaire() === $this) {
                $compte->setPartenaire(null);
            }
        }

        return $this;
    }

    

    /**
     * @return Collection|User[]
     */
    public function getUserComptePartenaire(): Collection
    {
        return $this->userComptePartenaire;
    }

    public function addUserComptepartenaire(User $userComptePartenaire): self
    {
        if (!$this->userComptePartenaire->contains($userComptePartenaire)) {
            $this->userComptePartenaire[] = $userComptePartenaire;
            $userComptePartenaire->setPartenaire($this);
        }
return $this;
    }

    public function removeUserComptePartenaire(User $userComptePartenaire): self
    {
        if ($this->userComptePartenaire->contains($userComptePartenaire)) {
            $this->userComptePartenaire->removeElement($userComptePartenaire);
            // set the owning side to null (unless already changed)
            if ($userComptePartenaire->getPartenaire() === $this) {
                $userComptePartenaire->setPartenaire(null);
            }
        }

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

    public function getContrat(): ?Contrat
    {
        return $this->contrat;
    }

    public function setContrat(Contrat $contrat): self
    {
        $this->contrat = $contrat;

        // set the owning side of the relation if necessary
        if ($contrat->getPartenaire() !== $this) {
            $contrat->setPartenaire($this);
        }

        return $this;
    }   

}