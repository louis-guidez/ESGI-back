<?php

namespace App\Entity;

use App\Repository\ConversationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ConversationRepository::class)]
class Conversation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $dateCreation = null;

    /**
     * @var Collection<int, UtilisateurConversation>
     */
    #[ORM\OneToMany(targetEntity: UtilisateurConversation::class, mappedBy: 'conversation')]
    private Collection $utilisateurConversations;

    public function __construct()
    {
        $this->utilisateurConversations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateCreation(): ?\DateTime
    {
        return $this->dateCreation;
    }

    public function setDateCreation(?\DateTime $dateCreation): static
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    /**
     * @return Collection<int, UtilisateurConversation>
     */
    public function getUtilisateurConversations(): Collection
    {
        return $this->utilisateurConversations;
    }

    public function addUtilisateurConversation(UtilisateurConversation $utilisateurConversation): static
    {
        if (!$this->utilisateurConversations->contains($utilisateurConversation)) {
            $this->utilisateurConversations->add($utilisateurConversation);
            $utilisateurConversation->setConversation($this);
        }

        return $this;
    }

    public function removeUtilisateurConversation(UtilisateurConversation $utilisateurConversation): static
    {
        if ($this->utilisateurConversations->removeElement($utilisateurConversation)) {
            // set the owning side to null (unless already changed)
            if ($utilisateurConversation->getConversation() === $this) {
                $utilisateurConversation->setConversation(null);
            }
        }

        return $this;
    }
}
