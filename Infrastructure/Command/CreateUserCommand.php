<?php
/**
 * This file is part of the <Auth> project.
 *
 * @category   Auth
 * @package    Command
 * @subpackage Lots
 * @author     Etienne de Longeaux <etienne.delongeaux@gmail.com>
 * @copyright  2015 Le Melee
 * @version    2.0
 * @since      2015-01-22
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Sfynx\AuthBundle\Infrastructure\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand as containerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Sfynx\ToolBundle\Util\PiFileManager;

/**
 * Command to create user visite data test
 *
 * <code>
 * php app/console sfynx:user:create
 * </code>
 *
 * @category   Auth
 * @package    Command
 * @subpackage Lots
 * @author     Etienne de Longeaux <etienne.delongeaux@gmail.com>
 * @copyright  2015 Le Melee
 * @version    2.0
 * @since      2015-01-22
 */
class CreateUserCommand extends containerAwareCommand
{
    /**
     * @var object $em THe entity manager service
     * @access protected
     */
    protected $em;

    /**
     * The configure method
     *
     * @access protected
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('sfynx:user:create')
            ->setDescription('Charger les données des utilisateurs')
            ->addOption('firstId', null, InputOption::VALUE_REQUIRED, 'First id user value')
            ->addOption('lastId', null, InputOption::VALUE_REQUIRED, 'Last id user value')
            ->addOption('pathData', null, InputOption::VALUE_REQUIRED, 'pathData where the data csv file is generate')
            ->setHelp(
                <<<EOT
La commande <info>sfynx:user:create</info> permet de charger les données tests des utilisateurs.

Utilisation de la commande:

<info>php app/console sfynx:user:create --firstId=4 --lastId=10000 --pathData=./config/gatling/data --env=test</info>

EOT
            );
    }

    /**
     * The execute method
     *
     * @param InputInterface  $input  The Input class
     * @param OutputInterface $output The output class
     *
     * @access protected
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $firstId = (int) $input->getOption('firstId');
        $lastId  = (int) $input->getOption('lastId');
        $root_dir  = (string) $input->getOption('pathData');

        $start_date = time();
        $this->em   = $this->getContainer()->get('doctrine')->getManager('default');
        PiFileManager::mkdirr($root_dir);
        $path   = $root_dir . "/connexion_sfynx.csv";
        file_put_contents($path, 'username,password'."\n", LOCK_EX);

        $output->writeln('First id user : ' . $firstId);
        $output->writeln('last id user : ' . $lastId);

        $output->writeln(" - début de chargement de donnée");
        
        $sqluser ="INSERT INTO `fos_user` (`lang_code`,  `username`, `username_canonical`, `email`, `email_canonical`, `enabled`, `salt`, "
            ."`password`, `last_login`, `locked`, `expired`, `expires_at`, `confirmation_token`, `password_requested_at`, "
            ."`roles`, `permissions`, `credentials_expired`, `credentials_expire_at`, `name`, `nickname`, "
            ."`created_at`) VALUES "
            ."('fr_FR', :user, :user, :user, :user, 1, '73rwkq08i3wo0ow840c0g48k800o0ok', "
            ."'xREF5HHqHbZmlmoO3+s5BXtVtYa3TIf8Wv+IVXJsQwx+0FG1WnqxjJ46toQ1u/+v1lfKOwo90FqEbqiPDzDo+g==', '2014-11-13 00:00:00', "
            ."0, 0, NULL, NULL, NULL, 'a:1:{i:0;s:9:\"ROLE_USER\";}', 'a:4:{i:0;s:4:\"VIEW\";i:1;s:4:\"EDIT\";i:2;s:6:\"CREATE\";i:3;s:6:\"DELETE\";}', 0, NULL, :user, :user, '2014-11-12 17:55:21' "
            .");";

        $this->em->getConnection()->beginTransaction();
        $stmtuser = $this->em->getConnection()->prepare($sqluser);
        try {
            $psw = "user";
            $start_date_user = time();
            for ($j=$firstId; $j<= $lastId; $j++) {
                $user_value = 'newuser'.$j;
                $params = array('user'=> $user_value, 'user'=> $user_value, 'user'=> $user_value,
                    'user'=> $user_value, 'user'=> $user_value, 'user'=> $user_value);
                $stmtuser->execute($params);
                //
                file_put_contents($path, "$user_value,$psw\n", FILE_APPEND);
            }
            $end_date_user = time();
            $duration_user = $end_date_user - $start_date_user;
            $output->writeln(" - fin de chargement de données des users en ". $duration_user ."s");     
            $this->em->flush();
            // Try and commit the transaction
            $this->em->getConnection()->commit();            
        } catch (\Exception $e) {
        	$output->writeln($e->getMessage());        	
        	// Rollback the failed transaction attempt
        	$this->em->getConnection()->rollback();
        	$this->em->close();
        }
        
        $end_date = time();
        $duration = $end_date - $start_date;

        $output->writeln(" - fin de chargement de donnée en ". $duration ."s");
    }
}
