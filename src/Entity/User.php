<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User entity.
 *
 * @ORM\Entity
 * @ORM\Table(name="app_users")
 * @UniqueEntity("username")
 */
class User implements UserInterface
{
    /**
     * @var string The entity Id
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="App\Doctrine\UuidGenerator")
     * @ORM\Column(type="guid")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=150, unique=true)
     * @Assert\NotBlank(normalizer="trim")
     */
    private $username;

    /**
     * @var string The hashed password
     *
     * @ORM\Column(type="text")
     * @Assert\NotBlank(normalizer="trim")
     */
    private $password;

    /**
     * @var array
     *
     * @ORM\Column(type="json")
     * @Assert\NotBlank
     */
    private $roles = [];

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     * @Assert\NotBlank(normalizer="trim")
     */
    private $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastConnection;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    public function __construct()
    {
        $this->setRoles(['ROLE_READ_DEFAULT']);
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles()
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function addRole(string $role): self
    {
        if (!\in_array($role, $this->roles)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = \trim($description);

        return $this;
    }

    public function getLastConnection(): ?\DateTime
    {
        return $this->lastConnection;
    }

    public function setLastConnection(?\DateTime $lastConnection)
    {
        $this->lastConnection = $lastConnection;

        return $this;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        return null;
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
}
