<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
/**
 * @ApiResource(
 *   attributes = { 
* "denormalization_context" = {"groups" = {"post"}}, 
* "normalization_context" = {"groups" = {"read"}, " enable_max_depth "= true}, 
*" force_eager "= false },
 * collectionOperations={
 *          "post"={"security"="is_granted(['ROLE_ADMIN_SYST','ROLE_ADMIN','ROLE_CAISSIER'])", "security_message"="Seul ADMIN_SYST peut creer un user"}
 *     },
 * )
 * @ORM\Entity(repositoryClass="App\Repository\DepotRepository")
 */
class Depot
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"post","read"})
     * @MaxDepth (1) 
     */
    private $dateDepot;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"post","read"})
     * @MaxDepth (1) 
     */
    private $montant;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Compte", inversedBy="depot")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"post","read"})
     * @MaxDepth (1) 
     */
    private $compte;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="depots")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"post","read"})
     * @MaxDepth (1) 
     */
    private $caissierAdd;
    public function __construct()
    {
        $this->dateDepot=  new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateDepot(): ?\DateTimeInterface
    {
        return $this->dateDepot;
    }

    public function setDateDepot(\DateTimeInterface $dateDepot): self
    {
        $this->dateDepot = $dateDepot;

        return $this;
    }

    public function getMontant(): ?string
    {
        return $this->montant;
    }

    public function setMontant(string $montant): self
    {
        $this->montant = $montant;

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

    public function getCaissierAdd(): ?User
    {
        return $this->caissierAdd;
    }

    public function setCaissierAdd(?User $caissierAdd): self
    {
        $this->caissierAdd = $caissierAdd;

        return $this;
    }
}
