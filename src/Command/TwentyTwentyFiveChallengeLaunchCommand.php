<?php
namespace App\Command;

use App\Challenges\ChallengeFactory;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsCommand(
    name: 'txurdi:challenge-launch',
    description: 'Launch a day challenge.',
    help: 'Launch a day challenge.',)]
class TwentyTwentyFiveChallengeLaunchCommand extends Command
{
    private SymfonyStyle $io;
    private string $projectDir;
    public function __construct(
//        private readonly EntityManagerInterface $entityManager,
        private readonly ChallengeFactory $challengeFactory,
        #[Autowire('%kernel.project_dir%')] string $projectDir
    )
    {
        $this->projectDir = $projectDir;
        parent::__construct();
    }

    public function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    public function interact(InputInterface $input, OutputInterface $output): void
    {
        // ...
    }


    public function __invoke(
        #[Argument('Year')] string $year,
        #[Argument('Day')] string $day,
        #[Argument('Half')] string $half,
        #[Argument('Debug')] string $debug = 'false',
    ): int
    {
        $result = '???';
        $this->io->writeln('Year: '.$year);
        $this->io->writeln('Day: '.$day);
        $this->io->writeln('Half: '.$half);
        $this->io->writeln('Debug: '.$debug);

        $startedAt = microtime(true);

        try {
            $challenge = $this->challengeFactory->createChallenge($year, $day, $this->projectDir, $debug);
            $result = $challenge->execute($half);
        } catch (\Throwable $error) {
            $this->io->error($error);
            return Command::FAILURE;
        }

        $finishedAt = microtime(true);
        $this->io->warning('Tiempo: ' . ($finishedAt - $startedAt));
        $this->io->success('PASSWORD: '.$result);
        return Command::SUCCESS;
    }

}
