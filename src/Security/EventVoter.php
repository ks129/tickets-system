<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\Event;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class EventVoter extends Voter
{
    public const VIEW = 'view';
    public const EDIT = 'edit';
    public const CHECK = 'check';

    public function __construct(private readonly Security $security)
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, [self::VIEW, self::EDIT, self::CHECK])) {
            return false;
        }

        if (!$subject instanceof Event) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        $user = $token->getUser();

        return match ($attribute) {
            self::VIEW => $this->canView($subject, $user),
            self::EDIT => $this->canEdit($subject, $user),
            self::CHECK => $this->canCheck($subject, $user),
            default => throw new \LogicException('This code should not be reached!'),
        };
    }

    private function canView(Event $event, ?User $user): bool
    {
        if ($event->isPublic()) {
            return true;
        }

        return $event->getHosts()->contains($user);
    }

    private function canEdit(Event $event, ?User $user): bool
    {
        return $event->getHosts()->contains($user);
    }

    private function canCheck(Event $event, ?User $user): bool
    {
        return $event->getHosts()->contains($user) || $event->getTicketCheckers()->contains($user);
    }
}
