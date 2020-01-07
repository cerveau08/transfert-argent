<?php

namespace App\Entity;


use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
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
 *          "createAdmin"={
 *          "method"="POST",
 *          "path"="/users/admin/new",
 *              "security"="is_granted('ROLE_ADMIN_SYSTEM')", 
 *               "security_message"="Acces refuse. Seul Admin System peut creer un Admin"
 *                 },
 *         "createCaissier"={
 *          "method"="POST",
 *          "path"="/users/caissier/new",
 *              "security"="is_granted(['ROLE_ADMIN_SYSTEM','ROLE_ADMIN'])", 
 *              "security_message"="Acces refuse. Seul Admin System ou Admin peut creer un  Caissier"
 *                 }
 *             },
 *     itemOperations={
 *          "get"={
 *   "security"="is_granted('ROLE_ADMIN_SYSTEM')",
 *             "normalisation_context"={"groups"={"get"}}
 *              },
 *          "put"={
 *              "security"="is_granted('ROLE_ADMIN_SYSTEM')",
 *          "security_message"="Acces refuse. Seul Admin System peut bloquer un Admin ou Caissier"
 *                   },
 *          "delete"={"security"="is_granted('ROLE_ADMIN')"}
 *     }
 *   
 * )
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields={"username"}, message="Cet utilisateur existe déjà")
 * @UniqueEntity(fields={"email"}, message="Cet utilisateur existe déjà")
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

    public function __construct()
    {
        $this->isActive = true;
    }

    public function getId(): ?int
    {
        return $this->id;
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
}
