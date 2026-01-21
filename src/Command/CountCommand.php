<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Repository\ActorRepository;
use App\Repository\MovieRepository;
use App\Repository\CategoryRepository;
use App\Repository\MediaObjectRepository;

#[AsCommand(
    name: 'app:count-command',
    description: 'Add a short description for your command',
)]
class CountCommand extends Command
{
    private ActorRepository $actorRepository;

    private MovieRepository $movieRepository;

    private CategoryRepository $categoryRepository;

    private MediaObjectRepository $mediaObjectRepo;

    public function __construct(
        ActorRepository $actorRepository,
        MovieRepository $movieRepository,
        CategoryRepository $categoryRepository,
        MediaObjectRepository $mediaObjectRepo
    ) {
        parent::__construct();
        $this->actorRepository = $actorRepository;
        $this->movieRepository = $movieRepository;
        $this->categoryRepository = $categoryRepository;
        $this->mediaObjectRepo = $mediaObjectRepo;
    }

    protected function configure(): void
    {
        $this
            ->addArgument(
                'entity',
                InputArgument::OPTIONAL,
                'Entity to display (All, Actors, Movies, Categories, MediaObjects)'
            )
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $selection = $this->getEntitySelection($input, $io);

        $io->note(sprintf('Sélection: %s', $selection));

        $this->displayEntityCounts($io, $selection);
        $this->displayMediaObjectsTable($io, $selection);

        return Command::SUCCESS;
    }

    private function getEntitySelection(InputInterface $input, SymfonyStyle $io): string
    {
        $entityArg = $input->getArgument('entity');
        $choices = ['All', 'Actors', 'Movies', 'Categories', 'MediaObjects'];

        if ($entityArg) {
            return (string) $entityArg;
        }

        return $io->choice(
            'Quelle entité voulez-vous afficher dans le tableau ?',
            $choices,
            'All'
        );
    }

    private function displayEntityCounts(SymfonyStyle $io, string $selection): void
    {
        $header = ['Nom de l\'entité', 'Nombre d\'éléments'];
        $elements = $this->buildEntityCountsData($selection);

        $io->table($header, $elements);
    }

    private function buildEntityCountsData(string $selection): array
    {
        $elements = [];
        $sel = strtolower($selection);

        if ($sel === 'all' || $sel === 'actors') {
            $elements[] = ['Actors', $this->actorRepository->count()];
        }
        if ($sel === 'all' || $sel === 'movies') {
            $elements[] = ['Movies', $this->movieRepository->count()];
        }
        if ($sel === 'all' || $sel === 'categories') {
            $elements[] = ['Categories', $this->categoryRepository->count()];
        }
        if ($sel === 'all' || $sel === 'mediaobjects') {
            $elements[] = ['MediaObjects', $this->mediaObjectRepo->count()];
        }

        return $elements;
    }

    private function displayMediaObjectsTable(SymfonyStyle $io, string $selection): void
    {
        $sel = strtolower($selection);

        if ($sel !== 'all' && $sel !== 'mediaobjects') {
            return;
        }

        $medias = $this->mediaObjectRepo->findAll();
        $elements = [];
        $totalSize = 0;

        foreach ($medias as $media) {
            $fileData = $this->getMediaFileData($media);
            $totalSize += $fileData['size'];
            $elements[] = [
                $fileData['path'],
                $fileData['display']
            ];
        }

        $elements[] = [
            'Poids total',
            round(($totalSize / 1024 / 1024), 2) . ' Mo'
        ];

        $io->table(['Image', 'Poids'], $elements);
    }

    private function getMediaFileData($media): array
    {
        $filePath = $media->filePath;
        $fullPath = '/var/www/html/wr506d/public/media/' . ($filePath ?? '');
        $filesize = 0;

        if ($filePath && file_exists($fullPath)) {
            $filesize = filesize($fullPath);
        }

        return [
            'path' => $filePath ?? '(no file)',
            'size' => $filesize,
            'display' => $filesize > 0 ? $filesize . ' octets' : 'missing'
        ];
    }
}
