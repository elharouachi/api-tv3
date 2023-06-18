<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\MovieRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * * Can represent anyone who has the right to see the movies
 *
 * @ApiResource(
 *     attributes={
 *         "force_eager"=false,
 *         "normalization_context"={"groups"={"read_movie"}},
 *         "denormalization_context"={"groups"={"write_movie"}},
 *         "order"={"id": "ASC"},
 *         "short_name"="m"
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
     * @var string title of movie
     * @ORM\Column(type="string", length=255)
     * @Groups({"read_movie", "write_movie", "read_movie_person", "read_people"})
     * @Assert\NotBlank()
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
     * @var integer duration of movie
     * @ORM\Column(type="integer")
     * @Groups({"read_movie", "write_movie", "read_movie_person", "read_people"})
     * @Assert\NotBlank()
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
     * @Groups({"read_movie", "write_movie", "read_people"})
     * @ORM\ManyToMany(targetEntity="Type", inversedBy="movies", cascade={"persist"})
     * @ORM\JoinTable(name="movie_has_type")
     * @Assert\Valid
     */
    private $types;

    /**
     * @ORM\OneToMany(targetEntity=MovieHasPeople::class, mappedBy="movie")
     * @Groups({"read_movie", "write_movie"})
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
    private $poster = null;

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

    public function getPoster(): ?string
    {
        return $this->poster;
    }

    public function setPoster(string $poster): self
    {
        $this->poster = $poster;

        return $this;
    }


    public function denormalize(array &$data, string $apiVersion): void
    {
        foreach ($data as $field => $value) {
            if (!empty($value) && \is_array($value) && \is_array(\current($value))) {
                switch ($field) {
                    case 'types':
                        $this->handleMultipleIriConversion($data, $field, $value, 'types', $apiVersion);
                        break;
                }
            }
        }

    }
}
