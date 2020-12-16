<?php

namespace TriTran\SqsQueueBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use TriTran\SqsQueueBundle\Service\QueueManager;

/**
 * Class QueueAttrCommand.
 */
class QueueAttrCommand extends Command implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('tritran:sqs_queue:attr')
            ->addArgument(
                'url',
                InputArgument::REQUIRED,
                'Queue Url which you want to retrieve its attributes'
            )
            ->setDescription('Retrieve the attribute of a specified queue');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $queueUrl = $input->getArgument('url');

        $io = new SymfonyStyle($input, $output);
        $io->title(sprintf('Start getting the attributes of queue URL <comment>%s</comment>', $queueUrl));

        /** @var QueueManager $queueManager */
        $queueManager = $this->container->get('tritran.sqs_queue.queue_manager');
        $result = $queueManager->getQueueAttributes($queueUrl);
        $io->table(['Attribute Name', 'Value'], array_map(function ($k, $v) {
            return [$k, $v];
        }, array_keys($result), $result));

        $io->text('Updated successfully');
        $io->success('Done');

        return 0;
    }
}
