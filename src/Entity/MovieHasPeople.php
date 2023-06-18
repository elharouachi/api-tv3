<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Repository\MovieHasPeopleRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource(
 *     attributes={
 *         "force_eager"=false,
 *         "normalization_context"={"groups"={"read_movie_person"}},
 *         "denormalization_context"={"groups"={"write_movie_person"}},
 *         "order"={"id": "ASC"},
 *         "short_name"="mhp"
 *     },
 *     collectionOperations={
 *         "get"={"security"="is_granted('PUBLIC_ACCESS')"},
 *         "post"={"security"="is_granted('ROLE_WRITE_OBJECT')", "security_message"="Not authorized to create this entity"}
 *     },
 *     itemOperations={
 *         "get"={"security"="is_granted('PUBLIC_ACCESS')"},
 *         "put"={"security"="is_granted('ROLE_WRITE_OBJECT')", "security_message"="Not authorized to edit this entity"},
 *         "delete"={"security"="is_granted('ROLE_WRITE_OBJECT')", "security_message"="Not authorized to delete this entity"}
 *     }
 * )
 * @ORM\Entity(repositoryClass=MovieHasPeopleRepository::class)
 */
class MovieHasPeople
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @Groups({"read_movie_person"})
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"read_movie_person", "write_movie_person"})
     */
    private $role;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"read_movie_person", "write_movie_person"})
     */
    private $significance;

    /**
     * @ORM\ManyToOne(targetEntity=Movie::class, inversedBy="movieHasPeople")
     * @Groups({"read_movie_person", "write_movie_person", "read_people"})
     */
    private $movie;

    /**
     * @ORM\ManyToOne(targetEntity=People::class, inversedBy="peopleHasMovie")
     * @Groups({"read_movie_person", "write_movie_person", "read_movie"})
     */
    private $people;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getSignificance()
    {
        return $this->significance;
    }

    public function setSignificance($significance = null): self
    {
        $this->significance = $significance;

        return $this;
    }

    public function getMovie(): ?Movie
    {
        return $this->movie;
    }

    public function setMovie(?Movie $movie): self
    {
        $this->movie = $movie;

        return $this;
    }

    public function getPeople(): ?People
    {
        return $this->people;
    }

    public function setPeople(?People $people): self
    {
        $this->people = $people;

        return $this;
    }
}
