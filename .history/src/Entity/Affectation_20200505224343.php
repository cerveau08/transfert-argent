<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;


/**
 * @ApiResource(
 * 
 * normalizationContext={
 *         "groups"={"read"}
 *     },
 * denormalizationContext={
 *         "groups"={"post"}
 *     },
 * collectionOperations={
 *          "get",
 *          "post"={
 * "security"="is_granted(['ROLE_ADMIN_PARTENAIRE','ROLE_pARTENAIRE'])", "security_message"="Seul ADMIN_ ou ADMIN peut creer un compte"
 * }
 *             },
 *     itemOperations={
 *          "get"={
 *   "security"="is_granted('ROLE_ADMIN_PARTENAIRE')",
 *           "security_message" = "Acces Refuse. Vous n'avez pas ce droit",
 *              },
 *        "put"={
 *        "controller"=AffectationController::class
 *         }
 *     }
 * )
 * @ApiFilter(SearchFilter::class, properties={"userComptePartenaire": "exact"})
 * @ORM\Entity(repositoryClass="App\Repository\AffectationRepository")
 */
class Affectation
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Groups({"post","read"})
     * @ORM\Column(type="date")
     */
    private $dateDebut;

    /**
     * @Groups({"post","read"})
     * @ORM\Column(type="date")
     */
    private $dateFin;

    /**
     * @Groups({"post","read"})
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="affectations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $userComptePartenaire;

    /**
     * @Groups({"post","read"})
     * @ORM\ManyToOne(targetEntity="App\Entity\Compte", inversedBy="affectations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $compte;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="userAffectes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $userQuiAffecte;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTimeInterface $dateDebut): self
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(\DateTimeInterface $dateFin): self
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    public function getUserComptePartenaire(): ?User
    {
        return $this->userComptePartenaire;
    }

    public function setUserComptePartenaire(?User $userComptePartenaire): self
    {
        $this->userComptePartenaire = $userComptePartenaire;

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

    public function getUserQuiAffecte(): ?User
    {
        return $this->userQuiAffecte;
    }

    public function setUserQuiAffecte(?User $userQuiAffecte): self
    {
        $this->userQuiAffecte = $userQuiAffecte;

        return $this;
    }
}
