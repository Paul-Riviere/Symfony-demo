<?php

namespace App\Model;

use DateTime;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class UserDTO
{
    public function __construct(
        #[Assert\NotBlank(message:'First name is required.')]
        #[Groups(['list_names', 'list_names_with_birthdate'])]
        public string $firstName,

        #[Assert\NotBlank]
        #[Groups(['list_names', 'list_names_with_birthdate'])]
        public string $lastName,

        #[Assert\GreaterThan(18)]
        public int $age,

        #[Groups(['list_names_with_birthdate'])]
        public DateTime $birthDate = new DateTime()
    ){
    }

    // Getters
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getAge(): int
    {
        return $this->age;
    }

    public function getBirthDate(): DateTime
    {
        return $this->birthDate;
    }

    // Setters
    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function setAge(int $age): void
    {
        $this->age = $age;
    }

    public function setBirthDate(DateTime $birthDate): void
    {
        $this->birthDate = $birthDate;
    }
}
