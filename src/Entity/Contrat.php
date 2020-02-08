<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass="App\Repository\ContratRepository")
 */
class Contrat
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
    private $information;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateCreation;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Partenaire", inversedBy="contrat", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $partenaire;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $comptes;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $article1;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $article2;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $article3;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $article4;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $article5;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $signature;

    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInformation(): ?string
    {
        return $this->information;
    }

    public function setInformation(string $information): self
    {
        $this->information = $information;

        return $this;
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

    public function getPartenaire(): ?Partenaire
    {
        return $this->partenaire;
    }

    public function setPartenaire(Partenaire $partenaire): self
    {
        $this->partenaire = $partenaire;

        return $this;
    }

    public function getComptes(): ?string
    {
        return $this->comptes;
    }

    public function setComptes(string $comptes): self
    {
        $this->comptes = $comptes;

        return $this;
    }

    public function getArticle1(): ?string
    {
        return $this->article1;
    }

    public function setArticle1(string $article1): self
    {
        $this->article1 = $article1;

        return $this;
    }

    public function getArticle2(): ?string
    {
        return $this->article2;
    }

    public function setArticle2(string $article2): self
    {
        $this->article2 = $article2;

        return $this;
    }

    public function getArticle3(): ?string
    {
        return $this->article3;
    }

    public function setArticle3(string $article3): self
    {
        $this->article3 = $article3;

        return $this;
    }

    public function getArticle4(): ?string
    {
        return $this->article4;
    }

    public function setArticle4(string $article4): self
    {
        $this->article4 = $article4;

        return $this;
    }

    public function getArticle5(): ?string
    {
        return $this->article5;
    }

    public function setArticle5(string $article5): self
    {
        $this->article5 = $article5;

        return $this;
    }

    public function getSignature(): ?string
    {
        return $this->signature;
    }

    public function setSignature(string $signature): self
    {
        $this->signature = $signature;

        return $this;
    }

}
