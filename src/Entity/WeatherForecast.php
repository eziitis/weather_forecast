<?php

namespace App\Entity;

use App\Repository\WeatherForecastRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WeatherForecastRepository::class)]
class WeatherForecast
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $ip_address = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $weather_forecast = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIpAddress(): ?string
    {
        return $this->ip_address;
    }

    public function setIpAddress(string $ip_address): self
    {
        $this->ip_address = $ip_address;

        return $this;
    }

    public function getWeatherForecast(): ?string
    {
        return $this->weather_forecast;
    }

    public function setWeatherForecast(string $weather_forecast): self
    {
        $this->weather_forecast = $weather_forecast;

        return $this;
    }
}
