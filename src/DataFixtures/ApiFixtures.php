<?php

namespace App\DataFixtures;

use App\Entity\Show;
use App\Entity\User;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ApiFixtures extends BaseFixtures
{
    private $passwordEncoder;
    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $passwordEncoder

    ) {
        parent::__construct($entityManager);
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $this->loadUser();
    }

    private function loadUser()
    {
        $data = $this->loadFixtures(User::class);

        foreach ($data as $elem) {
            $user = new User();
            $user
                ->setUsername($elem['username'])
                ->setPassword($this->passwordEncoder->encodePassword($user, $elem['password']))
                ->setRoles($elem['roles'])
                ->setDescription($elem['description'])
                ->setLastConnection($this->getDate($elem['lastConnection']))
                ->setCreatedAt($this->getDate($elem['createdAt']));

            $this->entityManager->persist($user);
        }

        $this->entityManager->flush();
    }
}
