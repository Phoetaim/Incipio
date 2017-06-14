<?php
/**
 * Created by PhpStorm.
 * User: Antoine
 * Date: 13/11/2016
 * Time: 15:53.
 */

namespace Mgate\PersonneBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/** @ORM\MappedSuperclass
 * Class gathering adresse related informations. Factorize code in an unique class.
 */
class Adressable implements AnonymizableInterface
{
    /**
     * @var string
     *
     * @Groups({"gdpr"})
     *
     * @ORM\Column(name="adresse", type="string", length=127, nullable=true)
     */
    private $adresse;

    /**
     * @var int(5)
     *
     * @Groups({"gdpr"})
     *
     * @ORM\Column(name="codepostal", type="integer", nullable=true)
     */
    private $codepostal;

    /**
     * @var string
     *
     * @Groups({"gdpr"})
     *
     * @ORM\Column(name="ville", type="string", length=63, nullable=true)
     */
    private $ville;

    /**
     * @var string
     *
     * @Groups({"gdpr"})
     *
     * @ORM\Column(name="pays", type="string", length=63, nullable=true)
     */
    private $pays;

    /**
     * {@inheritdoc}
     */
    public function anonymize(): void
    {
        $this->adresse = null;
        $this->codepostal = null;
        $this->ville = null;
        $this->pays = null;
    }

    /**
     * Set adresse.
     *
     * @param string $adresse
     *
     * @return Adressable
     */
    public function setAdresse(?string $adresse)
    {
        $this->adresse = $adresse;

        return $this;
    }

    /**
     * Get adresse.
     *
     * @return string
     */
    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    /**
     * Set codepostal.
     *
     * @param int $codepostal
     *
     * @return Adressable
     */
    public function setCodePostal(?int $codepostal)
    {
        $this->codepostal = $codepostal;

        return $this;
    }

    /**
     * Get codePostal.
     *
     * @return int
     */
    public function getCodePostal(): ?int
    {
        return $this->codepostal;
    }

    /**
     * Set ville.
     *
     * @param string $ville
     *
     * @return Adressable
     */
    public function setVille(?string $ville)
    {
        $this->ville = $ville;

        return $this;
    }

    /**
     * Get ville.
     *
     * @return string
     */
    public function getVille(): ?string
    {
        return $this->ville;
    }

    /**
     * Set codepostal.
     *
     * @param string $pays
     *
     * @return Adressable
     */
    public function setPays(?string $pays)
    {
        $this->pays = $pays;

        return $this;
    }

    /**
     * Get pays.
     *
     * @return string
     */
    public function getPays(): ?string
    {
        return $this->pays;
    }
}
