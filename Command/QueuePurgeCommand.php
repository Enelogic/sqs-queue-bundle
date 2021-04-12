<?php

namespace TriTran\SqsQueueBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use TriTran\SqsQueueBundle\Service\BaseQueue;

/**
 * Class QueuePurgeCommand.
 */
class QueuePurgeCommand extends Command implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('tritran:sqs_queue:purge')
            ->addArgument(
                'name',
                InputArgument::REQUIRED,
                'Queue ID which you want to send message'
            )
            ->addOption(
                'force',
                'f',
                InputOption::VALUE_NONE,
                'Set this parameter to execute this command'
            )
            ->setDescription('Deletes the messages in a queue specified by the QueueURL parameter.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        if (!$input->getOption('force')) {
            $io->note('Option --force is mandatory to drop data.');
            $io->warning('This action should not be used in the production environment.');

            return 1;
        }

        $queueName = $input->getArgument('name');
        if (!$this->container->has(sprintf('tritran.sqs_queue.%s', $queueName))) {
            throw new \InvalidArgumentException(sprintf('Queue [%s] does not exist.', $queueName));
        }

        $io->title(sprintf('Start purge all your message in SQS <comment>%s</comment>', $queueName));

        /** @var BaseQueue $queue */
        $queue = $this->container->get(sprintf('tritran.sqs_queue.%s', $queueName));
        $queue->purge();

        $io->text('All message in your specified queue were removed successfully');
        $io->success('Done');

        return 0;
    }
}
