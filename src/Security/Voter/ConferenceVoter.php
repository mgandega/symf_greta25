<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class ConferenceVoter extends Voter
{
    public const DELETE = 'POST_DELETE';
    public const EDIT = 'POST_EDIT';
    public const VIEW = 'POST_VIEW';
    public const CREATE = 'POST_CREATE';
    public const COMMENT = 'POST_COMMENT';

/*************  ✨ Codeium Command ⭐  *************/
    /**
     * Whether this voter supports the given attribute.
     * @param string $attribute An attribute to check, such as 'EDIT' or 'VIEW'
     * @param mixed $subject The object to access, which is a Conference in this case
     * @return bool true if this Voter can process the attribute on the subject, false otherwise
     */
/******  48f0a3e0-ed33-4125-9749-eb1985033ac6  *******/
    protected function supports(string $attribute, mixed $subject): bool
    {
        
        if($attribute === self::CREATE) {
           return in_array($attribute, [self::EDIT, self::VIEW, self::DELETE, self::CREATE, self::COMMENT]);
        }elseif($attribute === self::EDIT || $attribute === self::VIEW || $attribute === self::DELETE || self::COMMENT ) {
            
            // dd(in_array($attribute, [self::EDIT, self::VIEW, self::DELETE, self::CREATE]));
            return in_array($attribute, [self::EDIT, self::VIEW, self::DELETE, self::COMMENT])
            && $subject instanceof \App\Entity\Conference;
        }
        return false;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            
            case self::CREATE:
                if($user){
                    return true;
                }
                break;

            case self::EDIT:
                // $subject est une instance de Conference ($subject => $conference)
                // dd($subject->getUser()->getId() === $user->getId() || in_array('ROLE_ADMIN', $user->getRoles()));
                if($subject->getUser() === $user || in_array('ROLE_ADMIN', $user->getRoles())) {
                        return true; // signifie que c'est autorisé
                }
                // logic to determine if the user can EDIT
                // return true or false
                break;

            case self::DELETE:
                // logic to determine if the user can VIEW
                if($subject->getUser() === $user || in_array('ROLE_ADMIN', $user->getRoles())) {
                    return true;
                }
                break;

            case self::VIEW:
                return true;
                // logic to determine if the user can VIEW
                // return true or false
                break;
            case self::COMMENT:
                if (!empty($user)) {
                  return true;
                }
                  
                break;
            
        }

        return false;
    }
}
