<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource(
 * collectionOperations={"get"},
 * attributes={"pagination_enabled"=false}
 *)
 * @ORM\Entity(repositoryClass="App\Repository\TarifRepository")
 */
class Tarif
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $borneInf;

    /**
     * @ORM\Column(type="integer")
     */
    private $borneSup;

    /**
     * @ORM\Column(type="string")
     */
    private $frais;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBorneInf(): ?int
    {
        return $this->borneInf;
    }

    public function setBorneInf(int $borneInf): self
    {
        $this->borneInf = $borneInf;

        return $this;
    }

    public function getBorneSup(): ?int
    {
        return $this->borneSup;
    }

    public function setBorneSup(int $borneSup): self
    {
        $this->borneSup = $borneSup;

        return $this;
    }

    public function getFrais(): ?string
    {
        return $this->frais;
    }

    public function setFrais(string $frais): self
    {
        $this->frais = $frais;

        return $this;
    }
}
