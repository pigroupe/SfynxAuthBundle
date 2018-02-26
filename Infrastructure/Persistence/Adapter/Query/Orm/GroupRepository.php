<?php
namespace Sfynx\AuthBundle\Infrastructure\Persistence\Adapter\Query\Orm;

use Doctrine\ORM\EntityRepository;
use Sfynx\CoreBundle\Layers\Domain\Repository\Query\TranslationRepositoryInterface;
use Sfynx\CoreBundle\Layers\Infrastructure\Persistence\Adapter\Generalisation\Orm\AbstractQueryRepository;

/**
 * Group Repository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 *
 * @category   Sfynx\AuthBundle
 * @package    Infrastructure
 * @subpackage Persistence\Repository\Query\Orm
 * @author     Etienne de Longeaux <etienne.delongeaux@gmail.com>
 * @since      2012-01-06
 */
class GroupRepository extends AbstractQueryRepository implements TranslationRepositoryInterface
{
    /**
     * Query for formType choice type
     *
     * @param string $field
     * @param string $ORDER
     * @param null $enabled
     * @param bool $is_checkRoles
     * @param bool $with_archive
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function formAllOrderByField($field = 'createat', $ORDER = "DESC", $enabled = null, $with_archive = false)
    {
        $query = $this->createQueryBuilder('a')
            ->select("a.id, a.name");
        if (!$with_archive){
            $query->where('a.archived IS NULL');
        }
        if ( !(null === $enabled) ) {
            $query
                ->andWhere('a.enabled = :enabled')
                ->setParameters(array(
                    'enabled'    => $enabled,
                ));
        }
        $query->orderBy("a.{$field}", $ORDER);

        return $query;
    }
}
