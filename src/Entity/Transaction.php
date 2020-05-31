<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 * normalizationContext={"groups"={"read"}},
 *  denormalizationContext={"groups"={"post"}},
 * *collectionOperations={
 *    "get",
 *         "post"={
 * "security"="is_granted(['ROLE_ADMIN_PARTENAIRE','ROLE_CAISSIER_PARTENAIRE','ROLE_PARTENAIRE'])", "security_message"="Seul les user affecter peuvent faire une transaction"
 * }
 *     }
 * )
 * @ORM\Entity(repositoryClass="App\Repository\TransactionRepository")
 * @ApiFilter(SearchFilter::class, properties={"code": "exact"})
 */
class Transaction
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"post","read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"post","read"})
     */
    private $code;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"post","read"})
     */
    private $montant;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"post","read"})
     */
    private $frais;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"post","read"})
     */
    private $nomCompletE;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"post","read"})
     */
    private $typePieceE;

    /**
     * @ORM\Column(type="bigint")
     * @Groups({"post","read"})
     */
    private $numeroPieceE;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"post","read"})
     */
    private $dateEnvoi;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"post","read"})
     */
    private $telephoneE;

    /**
     * @ORM\Column(type="float")
     * @Groups({"post","read"})
     */
    private $commissionE;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"post","read"})
     */
    private $nomCompletR;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"post","read"})
     */
    private $typePieceR;

    /**
     * @ORM\Column(type="bigint", nullable=true)
     * @Groups({"post","read"})
     */
    private $numeroPieceR;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"post","read"})
     */
    private $telephoneR;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"post","read"})
     */
    private $dateRetrait;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Groups({"post","read"})
     */
    private $commisionR;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $commissionSysteme;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Groups({"post","read"})
     */
    private $taxeEtat;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"post","read"})
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="transactionE")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"post","read"})
     */
    private $userCompteE;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="transactionR")
     * @Groups({"post","read"})
     */
    private $userCompteR;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Compte", inversedBy="transactionE")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"post","read"})
     */
    private $compteEmetteur;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Compte", inversedBy="transactionR")
     * @Groups({"post","read"})
     */
    private $compteRecepteur;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"post","read"})
     */
    private $etatPartE;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"post","read"})
     */
    private $etatPartR;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getMontant(): ?int
    {
        return $this->montant;
    }

    public function setMontant(int $montant): self
    {
        $this->montant = $montant;

        return $this;
    }

    public function getFrais(): ?int
    {
        return $this->frais;
    }

    public function setFrais(int $frais): self
    {
        $this->frais = $frais;

        return $this;
    }

    public function getNomCompletE(): ?string
    {
        return $this->nomCompletE;
    }

    public function setNomCompletE(string $nomCompletE): self
    {
        $this->nomCompletE = $nomCompletE;

        return $this;
    }

    public function getTypePieceE(): ?string
    {
        return $this->typePieceE;
    }

    public function setTypePieceE(string $typePieceE): self
    {
        $this->typePieceE = $typePieceE;

        return $this;
    }

    public function getNumeroPieceE(): ?int
    {
        return $this->numeroPieceE;
    }

    public function setNumeroPieceE(int $numeroPieceE): self
    {
        $this->numeroPieceE = $numeroPieceE;

        return $this;
    }

    public function getDateEnvoi(): ?\DateTimeInterface
    {
        return $this->dateEnvoi;
    }

    public function setDateEnvoi(\DateTimeInterface $dateEnvoi): self
    {
        $this->dateEnvoi = $dateEnvoi;

        return $this;
    }

    public function getTelephoneE(): ?int
    {
        return $this->telephoneE;
    }

    public function setTelephoneE(int $telephoneE): self
    {
        $this->telephoneE = $telephoneE;

        return $this;
    }

    public function getCommissionE(): ?float
    {
        return $this->commissionE;
    }

    public function setCommissionE(float $commissionE): self
    {
        $this->commissionE = $commissionE;

        return $this;
    }

    public function getNomCompletR(): ?string
    {
        return $this->nomCompletR;
    }

    public function setNomCompletR(string $nomCompletR): self
    {
        $this->nomCompletR = $nomCompletR;

        return $this;
    }

    public function getTypePieceR(): ?string
    {
        return $this->typePieceR;
    }

    public function setTypePieceR(?string $typePieceR): self
    {
        $this->typePieceR = $typePieceR;

        return $this;
    }

    public function getNumeroPieceR(): ?int
    {
        return $this->numeroPieceR;
    }

    public function setNumeroPieceR(?int $numeroPieceR): self
    {
        $this->numeroPieceR = $numeroPieceR;

        return $this;
    }

    public function getTelephoneR(): ?int
    {
        return $this->telephoneR;
    }

    public function setTelephoneR(int $telephoneR): self
    {
        $this->telephoneR = $telephoneR;

        return $this;
    }

    public function getDateRetrait(): ?\DateTimeInterface
    {
        return $this->dateRetrait;
    }

    public function setDateRetrait(?\DateTimeInterface $dateRetrait): self
    {
        $this->dateRetrait = $dateRetrait;

        return $this;
    }

    public function getCommisionR(): ?float
    {
        return $this->commisionR;
    }

    public function setCommisionR(?float $commisionR): self
    {
        $this->commisionR = $commisionR;

        return $this;
    }

    public function getCommissionSysteme(): ?float
    {
        return $this->commissionSysteme;
    }

    public function setCommissionSysteme(?float $commissionSysteme): self
    {
        $this->commissionSysteme = $commissionSysteme;

        return $this;
    }

    public function getTaxeEtat(): ?float
    {
        return $this->taxeEtat;
    }

    public function setTaxeEtat(?float $taxeEtat): self
    {
        $this->taxeEtat = $taxeEtat;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getUserCompteE(): ?User
    {
        return $this->userCompteE;
    }

    public function setUserCompteE(?User $userCompteE): self
    {
        $this->userCompteE = $userCompteE;

        return $this;
    }

    public function getUserCompteR(): ?User
    {
        return $this->userCompteR;
    }

    public function setUserCompteR(?User $userCompteR): self
    {
        $this->userCompteR = $userCompteR;

        return $this;
    }

    public function getCompteEmetteur(): ?Compte
    {
        return $this->compteEmetteur;
    }

    public function setCompteEmetteur(?Compte $compteEmetteur): self
    {
        $this->compteEmetteur = $compteEmetteur;

        return $this;
    }

    public function getCompteRecepteur(): ?Compte
    {
        return $this->compteRecepteur;
    }

    public function setCompteRecepteur(?Compte $compteRecepteur): self
    {
        $this->compteRecepteur = $compteRecepteur;

        return $this;
    }

    public function getEtatPartE(): ?string
    {
        return $this->etatPartE;
    }

    public function setEtatPartE(?string $etatPartE): self
    {
        $this->etatPartE = $etatPartE;

        return $this;
    }

    public function getEtatPartR(): ?string
    {
        return $this->etatPartR;
    }

    public function setEtatPartR(?string $etatPartR): self
    {
        $this->etatPartR = $etatPartR;

        return $this;
    }
}
