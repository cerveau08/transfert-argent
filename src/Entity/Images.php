<?php
// api/src/Entity/MediaObject.php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Controller\ImageController;
use App\Controller\CreateMediaObjectAction;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ApiResource(
 *     iri="http://schema.org/image",
 *     normalizationContext={
 *         "groups"={"media_object_read"}
 *     },
 *     collectionOperations={
 *         "post"={
 *             "controller"=ImageController::class,
 *             "deserialize"=false,
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
 *         },
 *         "get"
 *     },
 *     itemOperations={
 *         "get"
 *     }
 * )
 */
class Images
{
    /**
     *
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     * @ORM\Id
     */
    protected $id;

    /**
     * @ApiProperty(iri="http://schema.org/image")
     * 
     */
    public $file;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $image;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", mappedBy="image", cascade={"persist", "remove"})
     */
    private $users;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getUsers(): ?User
    {
        return $this->users;
    }

    public function setUsers(?User $users): self
    {
        $this->users = $users;

        // set (or unset) the owning side of the relation if necessary
        $newImage = null === $users ? null : $this;
        if ($users->getImage() !== $newImage) {
            $users->setImage($newImage);
        }

        return $this;
    }
}