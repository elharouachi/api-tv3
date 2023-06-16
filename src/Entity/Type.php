<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

/**
 *  * @ApiResource(
 *     attributes={
 *         "force_eager"=false,
 *         "normalization_context"={"groups"={"read_type"}},
 *         "denormalization_context"={"groups"={"write_type"}},
 *         "order"={"id": "ASC"},
 *         "short_name"="s"
 *     },

 *     itemOperations={
 *         "get"={"security"="is_granted('PUBLIC_ACCESS')"},
 *         "put"={"security"="is_granted('ROLE_WRITE_OBJECT')", "security_message"="Not authorized to edit this entity"},
 *         "delete"={"security"="is_granted('ROLE_WRITE_OBJECT')", "security_message"="Not authorized to delete this entity"}
 *     }
 * )
 *
 * @ORM\Entity(repositoryClass=TypeRepository::class)
 * @ORM\Table(name="type")
 */
class Type
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *
     * @Groups({"read_type", "read_movie"})
     *
     * @ApiProperty(
     *     attributes={
     *         "swagger_context"={
     *             "example"="15"
     *         }
     *     }
     * )
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"read_type", "read_movie"})
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity=Movie::class, inversedBy="types", cascade={"persist"})
     * @ORM\JoinTable(name="movie_has_type")
     */
    private $movies;

    public function __construct()
    {
        $this->movies = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|Movie[]
     */
    public function getMovies(): Collection
    {
        return $this->movies;
    }

    public function addMovie(Movie $movie): self
    {
        if (!$this->movies->contains($movie)) {
            $this->movies[] = $movie;
        }

        return $this;
    }

    public function removeMovie(Movie $movie): self
    {
        $this->movies->removeElement($movie);

        return $this;
    }
}
