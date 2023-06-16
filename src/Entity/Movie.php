<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Repository\MovieRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * * Can represent anyone who has the right to see the films
 *
 * @ApiResource(
 *     attributes={
 *         "force_eager"=false,
 *         "normalization_context"={"groups"={"read_movie"}},
 *         "denormalization_context"={"groups"={"write_movie"}},
 *         "order"={"id": "ASC"},
 *         "short_name"="s"
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
 * @ORM\Entity
 * @ORM\Table(name="movie")
 * @ORM\Entity(repositoryClass=MovieRepository::class)
 */
class Movie extends BaseApiEntity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @Groups({"read_movie", "read_movie_person"})
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     *  @var string title of movie
     * @ORM\Column(type="string", length=255)
     * @Groups({"read_movie", "write_movie", "read_movie_person", "read_people"})
     *
     * @ApiProperty(
     *     attributes={
     *         "swagger_context"={
     *             "example"="move title"
     *         }
     *     }
     * )
     */
    private $title;

    /**
     *   @var integer duration of movie
     * @ORM\Column(type="integer")
     * @Groups({"read_movie", "write_movie", "read_movie_person", "read_people"})
     *
     *      * @ApiProperty(
     *     attributes={
     *         "swagger_context"={
     *             "example"="152"
     *         }
     *     }
     * )
     */
    private $duration;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\People", inversedBy="movies")

     */
    private $people;

    /**
     * @ORM\ManyToMany(targetEntity=Type::class, mappedBy="movies", cascade={"persist"})
     * @Groups({"read_movie", "write_movie", "read_movie_person", "read_people"})
     */
    private $types;

    /**
     * @ORM\OneToMany(targetEntity=MovieHasPeople::class, mappedBy="movie")
     */
    private $movieHasPeople;

    /**
     *  @var string poster of movie
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"read_movie", "write_movie", "read_movie_person", "read_people"})
     *
     * @ApiProperty(
     *     attributes={
     *         "swagger_context"={
     *             "example"="move title"
     *         }
     *     }
     * )
     */
    private $poster;


    public function __construct()
    {
        $this->types = new ArrayCollection();
        $this->movieHasPeople = new ArrayCollection();
    }

    public function getId() :int
    {
        return $this->id;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title): void
    {
        $this->title = $title;
    }

    public function getDuration()
    {
        return $this->duration;
    }

    public function setDuration($duration): void
    {
        $this->duration = $duration;
    }

    public function getTypes()
    {
        return $this->types;
    }

    public function addType(Type $type): self
    {
        if (!$this->types->contains($type)) {
            $this->types[] = $type;
            $type->addMovie($this);
        }

        return $this;
    }

    public function removeType(Type $type): self
    {
        if ($this->types->removeElement($type)) {
            $type->removeMovie($this);
        }

        return $this;
    }

    public function getPeople()
    {
        return $this->people;
    }

    public function addPeople(People $person): self
    {
        if (!$this->people->contains($person)) {
            $this->people[] = $person;
            $person->addPeopleHasMovie($this);
        }

        return $this;
    }

    public function removePeople(People $people): self
    {
        if ($this->people->contains($people)) {
            $this->people->removeElement($people);
            $people->removePeopleHasMovie($this);
        }

        return $this;
    }

    public function getMovieHasPeople()
    {
        return $this->movieHasPeople;
    }

    public function addMovieHasPerson(MovieHasPeople $movieHasPerson): self
    {
        if (!$this->movieHasPeople->contains($movieHasPerson)) {
            $this->movieHasPeople[] = $movieHasPerson;
            $movieHasPerson->setMovie($this);
        }

        return $this;
    }

    public function removeMovieHasPerson(MovieHasPeople $movieHasPerson): self
    {
        if ($this->movieHasPeople->removeElement($movieHasPerson)) {
            // set the owning side to null (unless already changed)
            if ($movieHasPerson->getMovie() === $this) {
                $movieHasPerson->setMovie(null);
            }
        }

        return $this;
    }

    public function getPoster(): string
    {
        return $this->poster;
    }

    public function setPoster(string $poster): self
    {
        $this->poster = $poster;

        return $this;
    }
}
