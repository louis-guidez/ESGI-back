<?php

namespace App\Entity;

use App\Repository\ConversationRepository;
use App\Entity\Message;
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

    /**
     * @var Collection<int, Message>
     */
    #[ORM\OneToMany(mappedBy: 'conversation', targetEntity: Message::class)]
    private Collection $messages;

    public function __construct()
    {
        $this->utilisateurConversations = new ArrayCollection();
        $this->messages = new ArrayCollection();
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

    /**
     * @return Collection<int, Message>
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): static
    {
        if (!$this->messages->contains($message)) {
            $this->messages->add($message);
            $message->setConversation($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): static
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getConversation() === $this) {
                $message->setConversation(null);
            }
        }

        return $this;
    }
}
