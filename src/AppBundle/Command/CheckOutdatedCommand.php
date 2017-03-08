<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CheckOutdatedCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('app:check-outdated')
            ->setDescription('Checks outdated CVs and sends an e-mail.')
            ->setHelp('Only use this in CRON jobs. This command scouts the database for outdated CVs, i.e. those that have not been updated for 3 months.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //$boundaryDate = date("Y-m-d",strtotime("- 3 months"));
        $boundaryDate = date('Y-m-d',strtotime('- 1 day'));
        $em = $this->getContainer()->get('doctrine')->getManager();

        $sql = "SELECT curriculumvitae.curriculumvitae_name, curriculumvitae.user_id, fos_user.email, personalia.first_name, personalia.last_name FROM curriculumvitae JOIN fos_user on curriculumvitae.user_id=fos_user.id join personalia on fos_user.personalia_id=personalia.id WHERE curriculumvitae.updated_at < \"".$boundaryDate."\"";
        $query = $em->getConnection()->prepare($sql);
        $query->execute();
        $users = $query->fetchAll();

        foreach($users as $key => $item)
        {
            $arr[$item['user_id']][$key] = $item;
            $output->writeln("Item: ".$item['user_id']."   Key: ".$key."   CVNaam: ".$item['curriculumvitae_name']."   email: ".$item['email']."   Voornaam: ".$item['first_name']."   achternaam: ".$item['last_name']);
            //$output->writeln("Item: ".$item['user_id']);
            //$output->writeln("Item: ".$item['user_id']);
        }
        $output->writeln("");
        $output->writeln($arr[1][1]);



//        foreach($users as $user)
//            {
//                $output->writeln("User ID: ".$user['user_id']."  CV Naam: ".$user['curriculumvitae_name']);
//            }



        //select user_id, curriculumvitae_name from curriculumvitae where updated_at < DATE_ADD(NOW(), INTERVAL -1 DAY)


    }

}