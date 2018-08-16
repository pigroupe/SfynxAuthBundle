<?php
namespace Sfynx\AuthBundle\Presentation\Request\User\Command;

use Sfynx\CoreBundle\Layers\Presentation\Request\Generalisation\AbstractFormRequest;

/**
 * Class FormRequest
 *
 * @category Sfynx\AuthBundle
 * @package Presentation
 * @subpackage Request\User\Command
 * @author     Etienne de Longeaux <etienne.delongeaux@gmail.com>
 */
class FormRequest extends AbstractFormRequest
{
    /**
     * @var array $defaults List of default values for optional parameters.
     */
    protected $defaults = [
        'entityId' => null,
        'password' => null,
        'plainPassword' => null,
        'lastLogin' => null,
        'confirmationToken' => null,
        'passwordRequestedAt' => null,
        'username' => null,
        'usernameCanonical' => null,
        'name' => null,
        'nickname' => null,
        'email' => null,
        'emailCanonical' => null,
        'birthday' => null,
        'address' => null,
        'country' => null,
        'city' => null,
        'zipCode' => null,
        'createdAt' => null,
        'updatedAt' => null,
        'publishedAt' => null,
        'archiveAt' => null,
        'expiresAt' => null,
        'credentialsExpireAt' => null,
        'enabled' => true,
        'startAt' => null,
        'endAt' => null,
        'groups' => null,
        'roles' => null,
        'permissions' => null,
        'applicationTokens' => null,
        'langCode' => null,
        'credentialsExpired' => false,
        'locked' => false,
        'expired' => false,
        'archived' => false,
        'globalOptIn' => false,
        'siteOptIn' => false,
    ];

    /**
     * @var string[] $required List of required parameters.
     */
    protected $required = [
//        'entityId'
    ];

    /**
     * @var array[] $allowedTypes List of allowed types for each parameter.
     */
    protected $allowedTypes = [
        'entityId' => ['int', 'null'],
        'password' => ['string', 'null'],
        'plainPassword' => ['array', 'null'],
        'lastLogin' => ['string', 'null'],
        'confirmationToken' => ['string', 'null'],
        'passwordRequestedAt' => ['string', 'null'],
        'username' => ['string', 'null'],
        'usernameCanonical' => ['string', 'null'],
        'name' => ['string', 'null'],
        'nickname' => ['string', 'null'],
        'email' => ['string', 'null'],
        'emailCanonical' => ['string', 'null'],
        'birthday' => ['string', 'null'],
        'address' => ['string', 'null'],
        'country' => ['string', 'null'],
        'city' => ['string', 'null'],
        'zipCode' => ['string', 'null'],
        'createdAt' => ['string', 'null'],
        'updatedAt' => ['string', 'null'],
        'publishedAt' => ['string', 'null'],
        'archiveAt' => ['string', 'null'],
        'expiresAt' => ['string', 'null'],
        'credentialsExpireAt' => ['string', 'null'],
        'groups' => ['array', 'null'],
        'roles' => ['array', 'null'],
        'permissions' => ['array', 'null'],
        'applicationTokens' => ['array', 'null'],
        'langCode' => ['string', 'null'],
        'credentialsExpired' => ['bool', 'null'],
        'locked' => ['bool', 'null'],
        'archived' => ['bool', 'null'],
        'expired' => ['bool', 'null'],
        'globalOptIn' => ['bool', 'null'],
        'siteOptIn' => ['bool', 'null'],
        'enabled' => ['bool', 'null'],
        'startAt' => ['DateTime', 'null'],
        'endAt' => ['DateTime', 'null'],
    ];

    /**
     * @return void
     */
    protected function setOptions()
    {
        $this->options = $this->request->getRequest()->get('user_from');

        foreach (['archived', 'expired', 'enabled', 'siteOptIn', 'globalOptIn'] as $data) {
            if (isset($this->options[$data])) {
                $this->options[$data] = (boolean)$this->options[$data];
            }
        }
        $id = $this->request->get('id', '');
        $this->options['entityId'] = ('' !== $id) ? (int)$id : null;

        /* datetime transformation */
        foreach ([
            'startAt',
            'endAt',
        ] as $data) {
            $this->options[$data] = !empty($this->options[$data]) ? new \DateTime($this->options[$data]) : new \DateTime();
        }

        $this->options = (null !== $this->options) ? $this->options : [];
    }
}
