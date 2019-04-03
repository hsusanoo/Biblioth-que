<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LivreRepository")
 * @Vich\Uploadable()
 */
class Livre
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $titrePrincipale;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $titreSecondaire;

    /**
     * @ORM\Column(type="smallint")
     */
    private $dateEdition;

    /**
     * @ORM\Column(type="smallint")
     */
    private $prix;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $observation;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Exemplaire", mappedBy="livre", orphanRemoval=true)
     */
    private $exemplaires;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Categorie", inversedBy="livres")
     */
    private $categorie;

    /**
     * @ORM\Column(type="string", length=255)
	 * @Assert\Isbn(
	 *     type="null",
	 *     bothIsbnMessage="Entrez un Isbn valide"
	 * )
     */
    private $Isbn;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Descripteur", mappedBy="livres")
     */
    private $descripteurs;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $couverture;

	/**
	 * @var File|null
	 * @Assert\Image(maxSize="3M")
	 * @Vich\UploadableField(mapping="book_img",fileNameProperty="couverture")
	 */
    private $couvertureFile;


    public function __construct()
    {
        $this->exemplaires = new ArrayCollection();
        $this->descripteurs = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitrePrincipale(): ?string
    {
        return $this->titrePrincipale;
    }

    public function setTitrePrincipale(string $titrePrincipale): self
    {
        $this->titrePrincipale = $titrePrincipale;

        return $this;
    }

    public function getTitreSecondaire(): ?string
    {
        return $this->titreSecondaire;
    }

    public function setTitreSecondaire(?string $titreSecondaire): self
    {
        $this->titreSecondaire = $titreSecondaire;

        return $this;
    }

    public function getDateEdition(): ?int
    {
        return $this->dateEdition;
    }

    public function setDateEdition(int $dateEdition): self
    {
        $this->dateEdition = $dateEdition;

        return $this;
    }

    public function getPrix(): ?int
    {
        return $this->prix;
    }

    public function setPrix(int $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getObservation(): ?string
    {
        return $this->observation;
    }

    public function setObservation(?string $observation): self
    {
        $this->observation = $observation;

        return $this;
    }

    /**
     * @return Collection|Exemplaire[]
     */
    public function getExemplaires(): Collection
    {
        return $this->exemplaires;
    }

    public function addExemplaire(Exemplaire $exemplaire): self
    {
        if (!$this->exemplaires->contains($exemplaire)) {
            $this->exemplaires[] = $exemplaire;
            $exemplaire->setLivre($this);
        }

        return $this;
    }

    public function removeExemplaire(Exemplaire $exemplaire): self
    {
        if ($this->exemplaires->contains($exemplaire)) {
            $this->exemplaires->removeElement($exemplaire);
            // set the owning side to null (unless already changed)
            if ($exemplaire->getLivre() === $this) {
                $exemplaire->setLivre(null);
            }
        }

        return $this;
    }

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): self
    {
        $this->categorie = $categorie;

        return $this;
    }

    public function getIsbn(): ?string
    {
        return $this->Isbn;
    }

    public function setIsbn(string $Isbn): self
    {
        $this->Isbn = $Isbn;

        return $this;
    }

    /**
     * @return Collection|Descripteur[]
     */
    public function getDescripteurs(): Collection
    {
        return $this->descripteurs;
    }

    public function addDescripteur(Descripteur $descripteur): self
    {
        if (!$this->descripteurs->contains($descripteur)) {
            $this->descripteurs[] = $descripteur;
            $descripteur->addLivre($this);
        }

        return $this;
    }

    public function removeDescripteur(Descripteur $descripteur): self
    {
        if ($this->descripteurs->contains($descripteur)) {
            $this->descripteurs->removeElement($descripteur);
            $descripteur->removeLivre($this);
        }

        return $this;
    }

    public function getCouverture(): ?string
    {
        return $this->couverture;
    }

    public function setCouverture(?string $couverture): self
    {
        $this->couverture = $couverture;

        return $this;
    }

    /**
	 * @return File
	 */
	public function getCouvertureFile(): File
	{
		return $this->couvertureFile;
	}

	/**
	 * @param File $couvertureFile
	 */
	public function setCouvertureFile(File $couvertureFile): void
	{
		$this->couvertureFile = $couvertureFile;
	}
}
