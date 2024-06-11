<?php

namespace App\Entity;

use App\Repository\FavoritoRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FavoritoRepository::class)]
class Favorito
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'favoritos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $idUsuario = null;

    #[ORM\ManyToOne(inversedBy: 'favoritos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Producto $idProducto = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdUsuario(): ?User
    {
        return $this->idUsuario;
    }

    public function setIdUsuario(?User $idUsuario): static
    {
        $this->idUsuario = $idUsuario;

        return $this;
    }

    public function getIdProducto(): ?Producto
    {
        return $this->idProducto;
    }

    public function setIdProducto(?Producto $idProducto): static
    {
        $this->idProducto = $idProducto;

        return $this;
    }
}
