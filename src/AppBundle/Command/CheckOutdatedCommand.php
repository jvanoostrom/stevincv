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

        $sql = "SELECT curriculumvitae.user_id, fos_user.email, personalia.first_name, personalia.last_name FROM curriculumvitae JOIN fos_user on curriculumvitae.user_id=fos_user.id join personalia on fos_user.personalia_id=personalia.id WHERE curriculumvitae.updated_at < \"".$boundaryDate."\" GROUP BY curriculumvitae.user_id";
        $query = $em->getConnection()->prepare($sql);
        $query->execute();
        $users = $query->fetchAll();

        foreach($users as $user)
        {
            $userid = $user['user_id'];
            $sql = "SELECT curriculumvitae.curriculumvitae_name FROM curriculumvitae JOIN fos_user on curriculumvitae.user_id=fos_user.id join personalia on fos_user.personalia_id=personalia.id WHERE curriculumvitae.updated_at < \"".$boundaryDate."\" AND curriculumvitae.user_id = $userid";
            $query = $em->getConnection()->prepare($sql);
            $query->execute();
            $cvs = $query->fetchAll();


            $message = \Swift_Message::newInstance()
                ->setSubject('Je hebt verouderde CV\'s')
                ->setFrom('vanoostrom@stevin.com')
                ->setTo($user['email'])
                ->setBody(
                    $this->getContainer()->get('templating')->render(
                        'admin/verouderde_cvs_email.html.twig',
                        array(
                            'first_name' => $user['first_name'],
                            'last_name' => $user['last_name'],
                            'cvs' => $cvs,
                        ),
                        'text/html'

                    )
                );

            $this->getContainer()->get('mailer')->send($message);
        }


    }

}