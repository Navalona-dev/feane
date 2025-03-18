<?php

namespace App\Entity;

use App\Repository\BookingRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BookingRepository::class)]
class Booking
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'bookings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $booker = null;

    #[ORM\ManyToOne(inversedBy: 'bookings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Table $tableRestaurant = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateBooking = null;

    #[ORM\Column]
    private ?bool $isPaid = false;

    #[ORM\Column]
    private ?float $price = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $message = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $bookingHour = null;

    #[ORM\Column(length: 255)]
    private ?string $reference = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $stripeSessionId = null;

    #[ORM\Column(length: 255)]
    private ?string $resetToken = null;

    #[ORM\Column]
    private ?bool $isConfirmed = false;

    public function inReservedDate($date, $heure) {
        $notAvailableHours = $this->tableRestaurant->getNotAvailableHours($date, $heure);

        $bookingDate = $this->getDateBooking();
        $bookingHeure = $this->getBookingHour();

        $available = true;

        foreach($notAvailableHours as $notAvailableHour) {
            if($notAvailableHour == $bookingHeure->format('H:i') && $date->format('m-d-Y') == $bookingDate->format('m-d-Y')) {
                $available = false;
            }
        }

        return $available;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBooker(): ?User
    {
        return $this->booker;
    }

    public function setBooker(?User $booker): static
    {
        $this->booker = $booker;

        return $this;
    }

    public function getTableRestaurant(): ?Table
    {
        return $this->tableRestaurant;
    }

    public function setTableRestaurant(?Table $tableRestaurant): static
    {
        $this->tableRestaurant = $tableRestaurant;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getDateBooking(): ?\DateTimeInterface
    {
        return $this->dateBooking;
    }

    public function setDateBooking(\DateTimeInterface $dateBooking): static
    {
        $this->dateBooking = $dateBooking;

        return $this;
    }

    public function isIsPaid(): ?bool
    {
        return $this->isPaid;
    }

    public function setIsPaid(bool $isPaid): static
    {
        $this->isPaid = $isPaid;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

   public function getMessage(): ?string
   {
       return $this->message;
   }

   public function setMessage(?string $message): static
   {
       $this->message = $message;

       return $this;
   }

   public function getBookingHour(): ?\DateTimeInterface
   {
       return $this->bookingHour;
   }

   public function setBookingHour(\DateTimeInterface $bookingHour): static
   {
       $this->bookingHour = $bookingHour;

       return $this;
   }

   public function getReference(): ?string
   {
       return $this->reference;
   }

   public function setReference(string $reference): static
   {
       $this->reference = $reference;

       return $this;
   }

   public function getStripeSessionId(): ?string
   {
       return $this->stripeSessionId;
   }

   public function setStripeSessionId(?string $stripeSessionId): static
   {
       $this->stripeSessionId = $stripeSessionId;

       return $this;
   }

   public function getResetToken(): ?string
   {
       return $this->resetToken;
   }

   public function setResetToken(string $resetToken): static
   {
       $this->resetToken = $resetToken;

       return $this;
   }

   public function isIsConfirmed(): ?bool
   {
       return $this->isConfirmed;
   }

   public function setIsConfirmed(bool $isConfirmed): static
   {
       $this->isConfirmed = $isConfirmed;

       return $this;
   }
}
