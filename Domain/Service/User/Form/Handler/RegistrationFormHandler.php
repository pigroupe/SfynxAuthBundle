<?php
namespace Sfynx\AuthBundle\Domain\Service\User\Form\Handler;

use Sfynx\CoreBundle\Layers\Application\Validation\Handler\AbstractFormHandler;
use Sfynx\AuthBundle\Domain\Generalisation\Interfaces\UserInterface;
use Sfynx\AuthBundle\Domain\Service\User\Generalisation\Interfaces\UserManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class RegistrationFormHandler
 *
 * @category   Sfynx\AuthBundle
 * @package    Domain
 * @subpackage Service\User\Form\Handler
 */
class RegistrationFormHandler extends AbstractFormHandler
{
    /** @var UserInterface */
    protected $user;
    /** @var */
    protected $confirmation;
    /** @var UserManagerInterface */
    protected $userManager;

    /**
     * RegistrationFormHandler constructor.
     * @param FormInterface $form
     * @param RequestStack $request
     * @param UserManagerInterface $userManager
     * @param $mailer
     */
    public function __construct(FormInterface $form, RequestStack $request, UserManagerInterface $userManager, $mailer)
    {
        parent::__construct($form, $request->getCurrentRequest());

        $this->mailer = $mailer;
        $this->userManager = $userManager;
    }

    /**
     * @param UserInterface $user
     */
    public function setUser(UserInterface $user)
    {
        $this->user = $user;
    }

    /**
     * @param $confirmation
     */
    public function setConfirmation($confirmation)
    {
        $this->confirmation = $confirmation;
    }

    /**
     * @return array
     */
    protected function getValidMethods()
    {
        return array('POST');
    }

    /**
     * @return void
     */
    protected function onSuccess()
    {
        if ($this->confirmation) {
            $user->setEnabled(false);
            $this->mailer->sendConfirmationEmailMessage($this->user);
        } else {
            $user->setConfirmationToken(null);
            $user->setEnabled(true);
        }
        $user->setRoles(array('ROLE_ADMIN'));
        $user->setPermissions(array('VIEW', 'EDIT', 'CREATE', 'DELETE'));
        $this->userManager->update($this->user, true);
    }
}
