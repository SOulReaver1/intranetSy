<?php

namespace App\Command;

use App\Repository\CustomerFilesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class AddDepartmentToCustomer extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:add-department';
    public $client;
    public $customerFilesRepository;
    public $google_key;
    public $manager; 

    public function __construct(HttpClientInterface $client, CustomerFilesRepository $customerFilesRepository, EntityManagerInterface $manager)
    {
        $this->client = $client;
        $this->customerFilesRepository = $customerFilesRepository;
        $this->google_key = $_ENV['GOOGLE_MAPS_API_KEY'];
        $this->manager = $manager;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
        ->setHelp('This command allows you to create a user...')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $customersWithNoDepartment = $this->customerFilesRepository->findBy(['department' => null]);
        if(!empty($customersWithNoDepartment)) {
            $output->writeln([
                count($customersWithNoDepartment).' customers found !',
                '======================',
                '',
            ]);
            foreach ($customersWithNoDepartment as $key => $value) {
                $longitude = $value->getLng();
                $latitude = $value->getLat();
                $name = $value->getName();
                $response = $this->client->request(
                    'GET',
                    "https://maps.googleapis.com/maps/api/geocode/json?latlng=$latitude,$longitude&result_type=administrative_area_level_2&key=$this->google_key"
                );
                $result = json_decode($response->getContent());
                if($result->status === "OK") {
                    $result = json_decode($response->getContent());
                    $result = $result->results[0];
                    $department = array_filter($result->address_components, function($context) {
                        return in_array("administrative_area_level_2", $context->types);
                    }); 
                    $department = $department[0]->long_name; 
                    $value->setDepartment($department);
                    $place_id = $result->place_id;  
                    $value->setPlaceId($place_id);
                    // $value->setDepartment($department)
                    $this->manager->persist($value);
                    $this->manager->flush();
                    $output->writeln("$name region added !");
                }else {
                    $output->writeln(['An error occurred !', '', json_encode($result, JSON_PRETTY_PRINT)]);
                    return Command::FAILURE;
                }
            }

        }else {
            $output->writeln('No customer found !');
        }

        return Command::SUCCESS;
    }
}