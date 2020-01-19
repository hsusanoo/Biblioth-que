<?php

namespace App\Entity;

use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LivreRepository")
 * @UniqueEntity(
 *     fields={"Isbn"},
 *     message="Ce numéro ISBN est déja utilisé"
 * )
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
     * @ORM\Column(type="text")
     */
    private $titrePrincipale;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $titreSecondaire;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     * @Assert\NotBlank()
     * @Assert\Length(
     *     min="4",
     *     max="4",
     *     exactMessage="Entrez une année valide"
     * )
     */
    private $dateEdition;

    /**
     * @ORM\Column(type="smallint",nullable=true)
     */
    private $prix;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $observation;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Exemplaire", mappedBy="livre", orphanRemoval=true,cascade={"persist"})
     */
    private $exemplaires;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Categorie", inversedBy="livres")
     */
    private $categorie;

    /**
     * @ORM\Column(type="string", length=255,nullable=true)
     * @Assert\Isbn(
     *     type="null",
     *     bothIsbnMessage="Entrez un ISBN valide"
     * )
     */
    private $Isbn;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Descripteur", mappedBy="livres",cascade={"persist"})
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

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateAquis;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Auteur", mappedBy="livres",cascade={"persist"})
     */
    private $auteurs;

    /**
     * @ORM\Column(type="datetime",nullable=true)
     */
    private $updated_at;

    /**
     * @ORM\Column(type="integer",nullable=true)
     */
    private $nPages;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="livres")
     */
    private $addedBy;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $Editeur;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="livresUpdated")
     */
    private $updatedBy;


    public function __construct()
    {
        $this->updated_at = new DateTime();
        $this->exemplaires = new ArrayCollection();
        $this->descripteurs = new ArrayCollection();
        $this->auteurs = new ArrayCollection();
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

    /**
     * @param mixed $descripteurs
     */
    public function setDescripteurs(Array $descripteurs): void
    {

        foreach ($this->descripteurs as $descripteur) {
            if ($descripteur instanceof Descripteur)
                $this->removeDescripteur($descripteur);
        }

        foreach ($descripteurs as $desc) {
            $desc->addLivre($this);
        }
        $this->descripteurs = $descripteurs;
    }

    public function removeDescripteur(Descripteur $descripteur): self
    {
        if ($this->descripteurs->contains($descripteur)) {
            $this->descripteurs->removeElement($descripteur);
            $descripteur->removeLivre($this);
        }

        return $this;
    }

    public function addDescripteur(Descripteur $descripteur): self
    {
        if (!$this->descripteurs->contains($descripteur)) {
            $this->descripteurs[] = $descripteur;
            $descripteur->addLivre($this);
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
    public function getCouvertureFile()//: File
    {
        return $this->couvertureFile;
    }

    /**
     * @param File $couvertureFile
     * @throws Exception
     */
    public function setCouvertureFile(File $couvertureFile): void
    {
        $this->couvertureFile = $couvertureFile;

        if ($this->couvertureFile instanceof UploadedFile) {
            $this->updated_at = new DateTime('now');
        }
    }

    public function getDateAquis(): ?DateTimeInterface
    {
        return $this->dateAquis;
    }

    public function setDateAquis(DateTimeInterface $dateAquis): self
    {
        $this->dateAquis = $dateAquis;

        return $this;
    }

    /**
     * @return Collection|Auteur[]
     */
    public function getAuteurs(): Collection
    {
        return $this->auteurs;
    }

    public function setAuteurs(Array $auteurs)
    {

        foreach ($this->auteurs as $auteur) {
            $this->removeAuteur($auteur);
        }

        foreach ($auteurs as $auteur) {
            $auteur->addLivre($this);
        }

        $this->auteurs = $auteurs;
    }

    public function removeAuteur(Auteur $auteur): self
    {
        if ($this->auteurs->contains($auteur)) {
            $this->auteurs->removeElement($auteur);
            $auteur->removeLivre($this);
        }

        return $this;
    }

    public function addAuteur(Auteur $auteur): self
    {
        if (!$this->auteurs->contains($auteur)) {
            $this->auteurs[] = $auteur;
            $auteur->addLivre($this);
        }

        return $this;
    }

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getNPages(): ?int
    {
        return $this->nPages;
    }

    public function setNPages(int $nPages): self
    {
        $this->nPages = $nPages;

        return $this;
    }

    public function getAddedBy(): ?User
    {
        return $this->addedBy;
    }

    public function setAddedBy(?User $addedBy): self
    {
        $this->addedBy = $addedBy;

        return $this;
    }

    public function getEditeur(): ?string
    {
        return $this->Editeur;
    }

    public function setEditeur(?string $Editeur): self
    {
        $this->Editeur = $Editeur;

        return $this;
    }

    public function getUpdatedBy(): ?User
    {
        return $this->updatedBy;
    }

    public function setUpdatedBy(?User $updatedBy): self
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }
}
