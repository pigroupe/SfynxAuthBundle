<?php
namespace Sfynx\AuthBundle\Presentation\Coordination\User\Query;

use Symfony\Component\HttpFoundation\Response;
use Sfynx\CoreBundle\Layers\Presentation\Coordination\Generalisation\AbstractSelectAjaxController;

/**
 * Index controller.
 *
 * @category   Sfynx\AuthBundle
 * @package    Presentation
 * @subpackage Coordination\User\Query
 * @author     Etienne de Longeaux <etienne.delongeaux@gmail.com>
 */
class SelectAjaxController extends AbstractSelectAjaxController
{
    /** @var string */
    protected $autorization_role = 'ROLE_EDITOR';

    /**
     * get entities in ajax request for select form.
     *
     * @return Response
     * @access public
     */
    protected function init(): void
    {
        // we set param
        $this->setParams('pagination', $this->request->get('pagination', null));
        $this->setParams('max', $this->request->get('max', 10));
        $this->setParams('keyword', [
            0 => [
                'field_name' => 'title',
                'field_value' => $this->request->get('keyword', ''),
                'field_trans' => true,
                'field_trans_name' => 'trans',
            ],
        ]);
        $this->setParam('cacheQuery_hash', [
            'time'      => 3600,
            'namespace' => 'hash_list_auth_user'
        ]);
        $this->setParams('query', $this->manager->getQueryRepository()->getAllEditorUsersWithLazy());
    }

    /**
     * Select all entities.
     *
     * @param array  $entities
     * @param string $locale
     *
     * @return Response
     * @access public
     */
    protected function renderQuery($entities, $locale)
    {
        $tab = [];
        foreach ($entities as $obj) {
            $content  = $obj->getId();
            $username = $obj->getUsername();
            $mail     = $obj->getEmail();
            $name     = $obj->getName();
            $nickame  = $obj->getNickname();
            if ($username) {
                $content .=  "- " . $username;
            }
            if ($mail) {
                $content .=  "- " . $mail;
            }
            if ($name && $nickame) {
                $content .=  " (" . $name . " ". $nickame . ")";
            }
            $tab[] = [
                'id'   => $obj->getId(),
                'text' => $content
            ];
        }
        return $tab;
    }
}