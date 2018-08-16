<?php
namespace Sfynx\AuthBundle\Infrastructure\Security\Specification;

use Sfynx\SpecificationBundle\Specification\AbstractSpecification;

/**
 * Class UserHasEndDateSpec
 * @package Sfynx\AuthBundle\Infrastructure\Security\Specification
 */
class UserHasEndDateSpec
{
    /**
     * Vérification si l'utilisateur qui se connecte a une date de fin, pas de
     * date de début et la date de fin est antérieure à la date de connexion
     *
     * @param $user
     * @return bool
     */
    public function isSatisfiedBy($user):bool
    {
        /* No check for users which have ROLE_ADMIN */
        if (!\is_null($user) && ($user->hasRole('ROLE_ADMIN')  == false) ) {
            $userStartDate = null;
            $userEndDate = null;

            if ($user->getStartAt()!= null) {
                $userStartDate = \date_format($user->getStartAt(), 'Y-m-d');
            }
            if ($user->getEndAt() != null) {
                $userEndDate = \date_format($user->getEndAt(), 'Y-m-d');
            } 
            $currentDate = \date_format(new \Datetime('NOW'), 'Y-m-d');

            /**
             * L'utilisateur a une date de fin, il n'a pas de date de début
             * La date de fin du compte est dans le passé
             *
             * On retourne true si ces conditions sont réunies
             */
            return (!\is_null($userEndDate) && \is_null($userStartDate) && $currentDate > $userEndDate) ? true : false;
        }
        return false;
    }
}
