<?php
namespace Sfynx\AuthBundle\Infrastructure\Persistence\Adapter\Command\Orm;

use Sfynx\AuthBundle\Domain\Repository\Command\UserCommandRepositoryInterface;
use Sfynx\CoreBundle\Layers\Application\Command\Generalisation\Interfaces\CommandInterface;
use Sfynx\CoreBundle\Layers\Domain\Repository\Command\BuildRepositoryInterface;
use Doctrine\ORM\EntityRepository;
use Sfynx\CoreBundle\Layers\Infrastructure\Persistence\Adapter\Generalisation\Orm\AbstractCommandRepository;

/**
 * Langue Repository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 *
 * @category   Sfynx\AuthBundle
 * @package    Infrastructure
 * @subpackage Persistence\Repository\Command\Orm
 * @author     Etienne de Longeaux <etienne.delongeaux@gmail.com>
 * @since      2012-01-06
 */
class LangueRepository extends AbstractCommandRepository implements UserCommandRepositoryInterface
{
}
