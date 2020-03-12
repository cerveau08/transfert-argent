<?php

namespace App\Entity;

use DateTime;
use App\Entity\Partenaire;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\HttpFoundation\JsonResponse;

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

    public function genContrat($data,$termes){

        //id  partenaire (on a id partenaire si le partenaire existe)
        $rc=$data->getRegistreCommercial();
        $nin=$data->getNinea();
        $nom=$data->getNomComplet();
        $date=new DateTime();
        $dates=$date->format("d-m-Y");
        $word = ["rco", "ninea", "nom","date"];
        $replace   = [$rc, $nin, $nom,$dates];

        $contrat = str_replace($word, $replace, $termes);   
        $response = new JsonResponse($contrat);
        return $response;
    }

}
