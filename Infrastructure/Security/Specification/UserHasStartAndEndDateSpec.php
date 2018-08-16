<?php
namespace Sfynx\AuthBundle\Infrastructure\Security\Specification;

use Sfynx\SpecificationBundle\Specification\AbstractSpecification;

/**
 * Class UserHasStartAndEndDateSpec
 * @package Sfynx\AuthBundle\Infrastructure\Security\Specification
 */
class UserHasStartAndEndDateSpec
{
    /**
     * Vérification si l'utilisateur qui se connecte a une date de début et date de fin et soit la date
     * de début est postérieure à la date de connexion, soit la date de fin est antérieure à la date de connexion
     *
     *
     * @param $currentUser
     * @return bool
     */
    public function isSatisfiedBy($currentUser):bool
    {
        /* No check for users which have ROLE_ADMIN */
        if (!is_null($currentUser) && ($currentUser->hasRole('ROLE_ADMIN') == false) ) {
            $userStartDate = null;
            $userEndDate = null;
            
            if ($currentUser->getStartAt() != null) {
                $userStartDate = date_format($currentUser->getStartAt(), 'Y-m-d');
            }
            if ($currentUser->getEndAt() != null) {
                $userEndDate = date_format($currentUser->getEndAt(), 'Y-m-d');
            }
            $currentDate = date_format(new \Datetime('NOW'), 'Y-m-d');

            if (
                !is_null($userStartDate) && //L'utilisateur a une date de début
                !is_null($userEndDate) && //L'utilisateur a une date de fin
                /** La date de début est postérieure à la date de connexion ou la date de fin est antérieure à la de connexion */
                ($currentDate < $userStartDate || $currentDate > $userEndDate)
            ) {
                return true;
            }
            return false;
        }
        return false;
    }
}
