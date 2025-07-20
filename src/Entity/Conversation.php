<?php

namespace App\Entity;

use App\Repository\ConversationRepository;
use App\Entity\Message;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Message;
use App\Entity\Utilisateur;

#[ORM\Entity(repositoryClass: ConversationRepository::class)]
#[ORM\Table(uniqueConstraints: [new ORM\UniqueConstraint(name: 'UNIQ_PARTICIPANTS', columns: ['participant1_id', 'participant2_id'])])]
class Conversation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $dateCreation = null;

    /**
     * @var Collection<int, Message>
     */
    #[ORM\OneToMany(mappedBy: 'conversation', targetEntity: Message::class, orphanRemoval: true)]
    private Collection $messages;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(nullable: false)] // ou true selon ton besoin
    private ?Utilisateur $utilisateurA = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $utilisateurB = null;


    /**
     * @var Collection<int, Message>
     */
    #[ORM\OneToMany(mappedBy: 'conversation', targetEntity: Message::class)]
    private Collection $messages;

    public function __construct()
    {
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

    public function getUtilisateurA(): ?Utilisateur
    {
        return $this->utilisateurA;
    }

    public function setUtilisateurA(?Utilisateur $utilisateurA): static
    {
        $this->utilisateurA = $utilisateurA;

        return $this;
    }

    public function getUtilisateurB(): ?Utilisateur
    {
        return $this->utilisateurB;
    }

    public function setUtilisateurB(?Utilisateur $utilisateurB): static
    {
        $this->utilisateurB = $utilisateurB;

        return $this;
    }
}
