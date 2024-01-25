<?php

namespace App\Entity;

use App\Repository\DonneeRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DonneeRepository::class)
 */
class Donnee
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     */
    private $temps;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $t;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $h;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $v;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $tds;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $ph;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $pgf;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $pr;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $vfe;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $vnc;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTemps(): ?float
    {
        return $this->temps;
    }

    public function setTemps(float $temps): self
    {
        $this->temps = $temps;

        return $this;
    }

    public function getT(): ?float
    {
        return $this->t;
    }

    public function setT(?float $t): self
    {
        $this->t = $t;

        return $this;
    }

    public function getH(): ?float
    {
        return $this->h;
    }

    public function setH(?float $h): self
    {
        $this->h = $h;

        return $this;
    }

    public function getV(): ?float
    {
        return $this->v;
    }

    public function setV(?float $v): self
    {
        $this->v = $v;

        return $this;
    }

    public function getTds(): ?int
    {
        return $this->tds;
    }

    public function setTds(?int $tds): self
    {
        $this->tds = $tds;

        return $this;
    }

    public function getPh(): ?float
    {
        return $this->ph;
    }

    public function setPh(?float $ph): self
    {
        $this->ph = $ph;

        return $this;
    }

    public function getPgf(): ?float
    {
        return $this->pgf;
    }

    public function setPgf(?float $pgf): self
    {
        $this->pgf = $pgf;

        return $this;
    }

    public function getPr(): ?float
    {
        return $this->pr;
    }

    public function setPr(?float $pr): self
    {
        $this->pr = $pr;

        return $this;
    }

    public function getVfe(): ?int
    {
        return $this->vfe;
    }

    public function setVfe(?int $vfe): self
    {
        $this->vfe = $vfe;

        return $this;
    }

    public function getVnc(): ?int
    {
        return $this->vnc;
    }

    public function setVnc(?int $vnc): self
    {
        $this->vnc = $vnc;

        return $this;
    }
}
