<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

class PersonneSearch
{

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom_personne;


    public function getNom(): string
    {

        return $this->nom_personne;
    }

    public function setNom(?string $nom_personne): self
    {
        $this->nom_personne = $nom_personne;

        return $this;
    }
}
