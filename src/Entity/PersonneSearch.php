<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

class PersonneSearch
{

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nomPersonne;


    public function getNomPersonne()
    {
        return $this->nomPersonne;
    }

    public function setNomPersonne(?string $nomPersonne): self
    {
        $this->nomPersonne = $nomPersonne;

        return $this;
    }
}
