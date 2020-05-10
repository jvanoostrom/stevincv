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
        $boundaryDate = date('Y-m-d',strtotime('- 3 months'));
        //$boundaryDate = date('Y-m-d',strtotime('- 1 day'));
        $em = $this->getContainer()->get('doctrine')->getManager();

        //$sql = "SELECT curriculumvitae.user_id, fos_user.email, personalia.first_name, personalia.last_name FROM curriculumvitae JOIN fos_user on curriculumvitae.user_id=fos_user.id join personalia on fos_user.personalia_id=personalia.id WHERE curriculumvitae.updated_at < \"".$boundaryDate."\" AND curriculumvitae.user_id = 1 AND fos_user.get_three_months_email = 1 GROUP BY curriculumvitae.user_id";
        //$sql = "SELECT curriculumvitae.user_id, fos_user.email, personalia.first_name, personalia.last_name FROM curriculumvitae JOIN fos_user on curriculumvitae.user_id=fos_user.id join personalia on fos_user.personalia_id=personalia.id WHERE curriculumvitae.updated_at < \"".$boundaryDate."\" AND fos_user.get_three_months_email = 1 GROUP BY curriculumvitae.user_id";
        $sql = "SELECT * FROM (SELECT curriculumvitae.user_id, fos_user.email, personalia.first_name, personalia.last_name, max(curriculumvitae.updated_at) as updatedat FROM curriculumvitae JOIN fos_user ON curriculumvitae.user_id=fos_user.id join personalia ON fos_user.personalia_id=personalia.id WHERE fos_user.get_three_months_email = 1 GROUP BY curriculumvitae.user_id) as innertable WHERE updatedat < \"".$boundaryDate."\"";
        $query = $em->getConnection()->prepare($sql);
        $query->execute();
        $users = $query->fetchAll();

        foreach($users as $user) {
            $userid = $user['user_id'];
            $sql = "SELECT curriculumvitae.curriculumvitae_name FROM curriculumvitae JOIN fos_user on curriculumvitae.user_id=fos_user.id join personalia on fos_user.personalia_id=personalia.id WHERE curriculumvitae.updated_at < \"" . $boundaryDate . "\" AND curriculumvitae.user_id = $userid";
            $query = $em->getConnection()->prepare($sql);
            $query->execute();
            $cvs = $query->fetchAll();

            $message = \Swift_Message::newInstance()
                ->setSubject('Je hebt verouderde CV\'s')
                ->setFrom(array('info@stevin.com' => 'Stevin Technology Consultants'))
                ->setTo($user['email'])
                ->setBody(
                    $this->getContainer()->get('templating')->render(
                        'admin/email/verouderde_cvs.html.twig',
                        array(
                            'first_name' => $user['first_name'],
                            'last_name' => $user['last_name'],
                            'cvs' => $cvs,
                            'threemonthsago' => $boundaryDate
                        )

                    )
                )
                ->setContentType("text/html");

            $this->getContainer()->get('mailer')->send($message);

            $sql = "UPDATE fos_user SET count_three_months_email = count_three_months_email + 1 WHERE id = ".$userid;
            $query = $em->getConnection()->prepare($sql);
            $query->execute();

        }


    }

}