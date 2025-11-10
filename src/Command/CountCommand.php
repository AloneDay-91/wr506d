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

    private MediaObjectRepository $mediaObjectRepository;

    public function __construct(ActorRepository $actorRepository, MovieRepository $movieRepository, CategoryRepository $categoryRepository, MediaObjectRepository $mediaObjectRepository)
    {
        parent::__construct();
        $this->actorRepository = $actorRepository;
        $this->movieRepository = $movieRepository;
        $this->categoryRepository = $categoryRepository;
        $this->mediaObjectRepository = $mediaObjectRepository;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('entity', InputArgument::OPTIONAL, 'Entity to display (All, Actors, Movies, Categories, MediaObjects)')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        // Determine which entity the user wants to display. If provided as argument use it, otherwise ask interactively.
        $entityArg = $input->getArgument('entity');

        $choices = ['All', 'Actors', 'Movies', 'Categories', 'MediaObjects'];

        if ($entityArg) {
            $selection = $entityArg;
        } else {
            $selection = $io->choice('Quelle entité voulez-vous afficher dans le tableau ?', $choices, 'All');
        }

        $selection = (string) $selection;

        $io->note(sprintf('Sélection: %s', $selection));

        /*
        $io->info('Nb of actors in database : ' .$this->actorRepository->count());
        $io->info('Nb of movies in database : ' .$this->movieRepository->count());
        $io->info('Nb of categories in database : ' .$this->categoryRepository->count());
        $imageCount = $this->mediaObjectRepository->count();
        $io->info('Nb of media objects in database : ' . $imageCount);
        */

        $header = [
            'Nom de l\'entité',
            'Nombre d\'éléments'
        ];

        $elements = [];

        // Prepare counts according to selection
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
            $elements[] = ['MediaObjects', $this->mediaObjectRepository->count()];
        }

        $io->table($header, $elements);

        $elements = [];

        // If the selection includes MediaObjects, show the media files table
        if ($sel === 'all' || $sel === 'mediaobjects') {
            $medias = $this->mediaObjectRepository->findAll();
            $totalSize = 0;
            foreach ($medias as $media) {
                $filePath = $media->filePath;
                $fullPath = '/var/www/html/wr506d/public/media/' . ($filePath ?? '');
                if ($filePath && file_exists($fullPath)) {
                    $filesize = filesize($fullPath);
                } else {
                    $filesize = 0;
                }
                $totalSize += $filesize;
                $elements[] = [
                    $filePath ?? '(no file)',
                    $filesize > 0 ? $filesize.' octets' : 'missing'
                ];
            }

            $header = [
                'Image',
                'Poids'
            ];

            $elements[] = [
                'Poids total',
                round(($totalSize/1024/1024), 2).' Mo'
            ];

            $io->table($header, $elements);
        }

        return Command::SUCCESS;
    }
}
