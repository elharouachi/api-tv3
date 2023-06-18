<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

/**
 * *Can represent anyone who has the right to see the films
 *
 * @ApiResource(
 *     attributes={
 *         "force_eager"=false,
 *         "normalization_context"={"groups"={"read_people"}},
 *         "denormalization_context"={"groups"={"write_people"}},
 *         "order"={"id": "ASC"},
 *         "short_name"="p"
 *     },
 *     collectionOperations={
 *         "get"={"security"="is_granted('PUBLIC_ACCESS')", "security_message"="Not authorized to access this entity"},
 *         "post"={"security"="is_granted('ROLE_WRITE_OBJECT')", "security_message"="Not authorized to create this entity"}
 *     },
 *     itemOperations={
 *         "get"={"security"="is_granted('PUBLIC_ACCESS')"},
 *         "put"={"security"="is_granted('ROLE_WRITE_OBJECT')", "security_message"="Not authorized to edit this entity"},
 *         "delete"={"security"="is_granted('ROLE_WRITE_OBJECT')", "security_message"="Not authorized to delete this entity"}
 *     }
 * )
 * @ORM\Entity
 * @ORM\Table(name="people")
 */
class People extends BaseApiEntity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"read_people", "read_movie_person"})
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
     * @Groups({"read_people","write_people", "read_movie", "read_movie_person"})
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"read_people","write_people", "read_movie", "read_movie_person"})
     */
    private $lastname;

    /**
     *  @var string date of birthday a person
     * @ORM\Column(type="date")
     * @Groups({"read_people","write_people", "read_movie", "read_movie_person"})
     * @ApiProperty(
     *     attributes={
     *         "swagger_context"={
     *             "example"="2012-12-12"
     *         }
     *     }
     * )
     */
    private $dateOfBirth;

    /**
     * @var string nationality of person
     * @ORM\Column(type="string", length=255)
     * @Groups({"read_people","write_people", "read_movie", "read_movie_person"})
     *
     * @ApiProperty(
     *     attributes={
     *         "swagger_context"={
     *             "example"="france, germany"
     *         }
     *     }
     * )
     */
    private $nationality;

    /**
     * @Groups({"read_people", "write_people"})
     * @ORM\OneToMany(targetEntity="MovieHasPeople", mappedBy="people")
     */
    private $peopleHasMovie;

    public function __construct()
    {
        $this->peopleHasMovie = new ArrayCollection();
    }

    public function getId() :int
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

    public function getFirstname()
    {
        return $this->firstname;
    }

    public function setFirstname($firstname): void
    {
        $this->firstname = $firstname;
    }

    public function getLastname()
    {
        return $this->lastname;
    }

    public function setLastname($lastname): void
    {
        $this->lastname = $lastname;
    }

    public function getDateOfBirth()
    {
        return $this->dateOfBirth;
    }

    public function setDateOfBirth($dateOfBirth): void
    {
        $this->dateOfBirth = $dateOfBirth;
    }

    public function getNationality()
    {
        return $this->nationality;
    }

    public function setNationality($nationality): void
    {
        $this->nationality = $nationality;
    }

    public function getPeopleHasMovie()
    {
        return $this->peopleHasMovie;
    }

    public function addPeopleHasMovie(MovieHasPeople $movieHasPerson): self
    {
        if (!$this->peopleHasMovie->contains($movieHasPerson)) {
            $this->peopleHasMovie[] = $movieHasPerson;
            $this->peopleHasMovie->add($movieHasPerson);
        }

        return $this;
    }

    public function removePeopleHasMovie(MovieHasPeople $movieHasPerson): self
    {
        if ($this->peopleHasMovie->removeElement($movieHasPerson)) {
            // set the owning side to null (unless already changed)
            if ($movieHasPerson->getPeople() === $this) {
                $movieHasPerson->setPeople(null);
            }
        }

        return $this;
    }
}
