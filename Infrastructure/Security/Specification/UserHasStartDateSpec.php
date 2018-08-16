<?php
namespace Sfynx\AuthBundle\Infrastructure\Security\Specification;

use Sfynx\SpecificationBundle\Specification\AbstractSpecification;
use Sfynx\SpecificationBundle\Specification\Logical\AndSpec;

/**
 * Class UserHasStartDateSpec
 *
 * Vérification si l'utilisateur qui se connecte a une date de début, pas de
 * date de fin et la date de début est postérieure à la date de connexion
 *
 * @package Sfynx\AuthBundle\Infrastructure\Security\Specification
 */
class UserHasStartDateSpec
{
    /**
     * @param $currentUser
     * @return bool
     */
    public function isSatisfiedBy($currentUser):bool
    {
        /* No check for users which have ROLE_ADMIN */
        if (!is_null($currentUser) && ($currentUser->hasRole('ROLE_ADMIN') == false) ) {
            $userStartDate = null;
            $userEndDate = null;
            
            if ( $currentUser->getStartAt() != null) {
                $userStartDate = date_format($currentUser->getStartAt(), 'Y-m-d');
            } 
            if ( $currentUser->getEndAt() != null) {
                $userEndDate = date_format($currentUser->getEndAt(), 'Y-m-d');
            } 
            $currentDate = date_format(new \Datetime('NOW'), 'Y-m-d');
            //$userIsActive = $currentUser->isEnabled(); //&& !$userIsActive

            /**
             * L'utilisateur a une date de début, il n'a pas de date de fin
             * La date de début du compte est dans le futur
             *
             * On retourne true si ces conditions sont réunies
             *
             */
            return (!is_null($userStartDate) && is_null($userEndDate) && $currentDate < $userStartDate)?true:false;
        }
        return false;
    }
}
