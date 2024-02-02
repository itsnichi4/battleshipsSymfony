<?php
namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // Create the first user
        $user1 = new User();
        $user1->setUsername('user');
        $user1->setEmail('user@example.com');
        $user1->setPassword('password');
        $user1->setCreatedAt(new \DateTimeImmutable());
        $user1->setUpdatedAt(new \DateTimeImmutable());
        $user1->setRoles(['ROLE_USER']);
        $manager->persist($user1);

        // Create the second user based on the first user with modifications
        $user2 = new User();
        $user2->setUsername($user1->getUsername() . '2'); // Append '2' to the username
        $user2->setEmail($user1->getEmail() . '2'); // Use the same email as the first user
        $user2->setPassword($user1->getPassword()); // Use the same password as the first user
        $user2->setCreatedAt(new \DateTimeImmutable());
        $user2->setUpdatedAt(new \DateTimeImmutable());
        $user2->setRoles(['ROLE_USER']);
        $manager->persist($user2);

        $user3 = new User();
        $user3->setUsername($user1->getUsername() . '3'); // Append '3' to the username
        $user3->setEmail($user1->getEmail() . '3'); // Use the same email as the first user
        $user3->setPassword($user1->getPassword()); // Use the same password as the first user
        $user3->setCreatedAt(new \DateTimeImmutable());
        $user3->setUpdatedAt(new \DateTimeImmutable());
        $user3->setRoles(['ROLE_USER']);
        $manager->persist($user3);

        $manager->flush();
    }
}