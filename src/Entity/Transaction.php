<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass="App\Repository\TransactionRepository")
 */
class Transaction
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $code;

    /**
     * @ORM\Column(type="integer")
     */
    private $montant;

    /**
     * @ORM\Column(type="integer")
     */
    private $frais;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nomCompletE;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $typePieceE;

    /**
     * @ORM\Column(type="integer")
     */
    private $numeroPieceE;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateEnvoi;

    /**
     * @ORM\Column(type="integer")
     */
    private $telephoneE;

    /**
     * @ORM\Column(type="float")
     */
    private $commissionE;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nomCompletR;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $typePieceR;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $numeroPieceR;

    /**
     * @ORM\Column(type="integer")
     */
    private $telephoneR;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateRetrait;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $commisionR;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $commissionSysteme;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $taxeEtat;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="transactions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $userCompteE;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="transactions")
     */
    private $userCompteR;

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
}
