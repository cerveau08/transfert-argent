<?php

namespace App\Entity;


use Doctrine\ORM\Mapping as ORM;
use App\Controller\UserController;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Symfony\Component\HttpFoundation\File\File;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;

/**
 * An offer from my shop - this description will be automatically extracted form the PHPDoc to document the API.
 *
 * @ApiResource(
 * collectionOperations={
 *          "get"={"security"="is_granted(['ROLE_ADMIN_ SYSTEM','ROLE_ADMIN'])",
 *            "normalisation_context"={"groups"={"get"}},
 *         },
 *         "UserImage"={
 *             "method"="POST",
 *             "controller"=UserController::class,
 *             "deserialize"=false,
 *             "access_control"="is_granted('ROLE_USER')",
 *             "validation_groups"={"Default", "user_object_create"},
 *             "openapi_context"={
 *                 "requestBody"={
 *                     "content"={
 *                         "multipart/form-data"={
 *                             "schema"={
 *                                 "type"="object",
 *                                 "properties"={
 *                                     "file"={
 *                                         "type"="string",
 *                                         "format"="binary"
 *                                     }
 *                                 }
 *                             }
 *                         }
 *                     }
 *                 }
 *             }
 *          },
 *          "createAdmin"={
 *          "method"="POST",
 *          "path"="/users/admin/new",
 *              "security"="is_granted('ROLE_ADMIN_SYSTEM')", 
 *               "security_message"="Acces refuse. Seul Admin System peut creer un Admin"
 *                 },
 *          "createCaissier"={
 *          "method"="POST",
 *          "path"="/users/caissier/new",
 *              "security"="is_granted(['ROLE_ADMIN_SYSTEM','ROLE_ADMIN'])", 
 *              "security_message"="Acces refuse. Seul Admin System ou Admin peut creer un  Caissier"
 *                 }
 *             },
 *     itemOperations={
 *          "get"={
 *              "security"="is_granted('ROLE_ADMIN_SYSTEM')",
 *             "normalisation_context"={"groups"={"get"}}
 *              },
 *          "bloquerAdmin"={
 *             "method"="PUT",
 *             "path"="/users/admin/{id}",
 *              "security"="is_granted('ROLE_ADMIN_SYSTEM')",
 *              "security_message"="Acces refuse. Seul Admin System peut bloquer un Admin"
 *                   },
 *          "bloquerCaissier"={
 *             "method"="PUT",
 *             "path"="/users/caissier/{id}",
 *              "security"="is_granted(['ROLE_ADMIN_SYSTEM','ROLE_ADMIN'])",
 *              "security_message"="Acces refuse. Seul Admin System ou un Admin peut bloquer un Caissier"
 *                   },
 *          "delete"={"security"="is_granted('ROLE_ADMIN')"}
 *     }
 *   
 * )
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields={"username"}, message="Cet utilisateur existe déjà")
 * @UniqueEntity(fields={"email"}, message="Cet utilisateur existe déjà")
 * @Vich\Uploadable
 */
class User implements AdvancedUserInterface
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
     * @ORM\Column(type="json")
     * @Groups("get")
     */
    private $roles = [];

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
     *  @Groups("get")
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
     * * @Groups("get")
     * @ApiSubresource(maxDepth=1) 
     */
    private $profil;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Le champ ne doit pas être vide")
     * @Assert\Length(min="2", minMessage="Ce champ doit contenir un minimum de {{ limit }} caractères", max="255", maxMessage="Ce champ doit contenir un maximum de {{ limit }} caractères")
     *  @Groups("get")
     */
    private $login;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Depot", mappedBy="caissierAdd")
     */
    private $depots;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Compte", mappedBy="adminCreateur")
     */
    private $comptes;

     /**
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    private $imagename;

    /**
     * @Vich\UploadableField(mapping="product_images", fileNameProperty="image")
     * @var File
     */
    private $imageFile;

    /**
     * @ORM\Column(type="datetime")
     * @var \DateTime
     */
    private $updatedAt;

    public function __construct()
    {
        $this->isActive = true;
        $this->depots = new ArrayCollection();
        $this->comptes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImagename(): ?string
    {
        return $this->imagename;
    }

    public function setImagename(string $imagename): void
    {
        $this->imagename = $imagename;
    }


    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param null | File $imageFile
     */
    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        // Only change the updated af if the file is really uploaded to avoid database updates.
        // This is needed when the file should be set when loading the entity.
        if ($this->imageFile instanceof UploadedFile) {
            $this->updatedAt = new \DateTime('now');
        }
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
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
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * Set the value of roles
   
     * @return  self
     */ 
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
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

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setLogin(string $login): self
    {
        $this->login = $login;

        return $this;
    }

    public function isAccountNonExpired(){
        return true;
    }
     public function isAccountNonLocked(){
         return true;
     }
     public function isCredentialsNonExpired()
     {
         return true;
     }
     public function isEnabled(){
         return $this->isActive;
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
             $compte->setAdminCreateur($this);
         }

         return $this;
     }

     public function removeCompte(Compte $compte): self
     {
         if ($this->comptes->contains($compte)) {
             $this->comptes->removeElement($compte);
             // set the owning side to null (unless already changed)
             if ($compte->getAdminCreateur() === $this) {
                 $compte->setAdminCreateur(null);
             }
         }

         return $this;
     }
}
