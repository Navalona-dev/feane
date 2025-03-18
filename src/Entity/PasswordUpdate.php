<?php

namespace App\Entity;

use App\Repository\PasswordUpdateRepository;

use Symfony\Component\Validator\Constraints as Assert;

class PasswordUpdate
{
    
    private ?int $id = null;

    private ?string $oldPassword = null;

    #[Assert\Length(
        min: 6,
        minMessage: 'Votre mot de passe doit au moins 6 caractères',
    )]
    private ?string $newPassword = null;

    #[Assert\EqualTo(propertyPath: 'newPassword', message: 'Vous n\'avez pas correctement confirmer vote mot de passe' )]
    private ?string $confirmPassword = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOldPassword(): ?string
    {
        return $this->oldPassword;
    }

    public function setOldPassword(string $oldPassword): self
    {
        $this->oldPassword = $oldPassword;

        return $this;
    }

    public function getNewPassword(): ?string
    {
        return $this->newPassword;
    }

    public function setNewPassword(string $newPassword): self
    {
        $this->newPassword = $newPassword;

        return $this;
    }

    public function getConfirmPassword(): ?string
    {
        return $this->confirmPassword;
    }

    public function setConfirmPassword(string $confirmPassword): self
    {
        $this->confirmPassword = $confirmPassword;

        return $this;
    }
}
