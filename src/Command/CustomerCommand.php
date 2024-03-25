<?php

namespace App\Command;

use Elastica\Client;
use Elastica\Document;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:customer',
)]
class CustomerCommand extends Command
{
    public function __construct(
        private Client $client,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
//        $this->client->getIndex('customers')->delete();
        $this->client->getIndex('customers')->create();


        $customers = [
            [
                'firstname' => 'John',
                'lastname' => 'Doe',
                'id' => 1
            ],
            [
                'firstname' => 'Jane',
                'lastname' => 'Doe',
                'id' => 2
            ],
            [
                'firstname' => 'Alice',
                'lastname' => '',
                'id' => 3
            ],
            [
                'firstname' => 'Bob',
                'lastname' => '',
                'id' => 4
            ],
            [
                'firstname' => 'Webby',
                'lastname' => 'Spider',
                'id' => 5
            ]
        ];

        foreach ($customers as $customer) {
            $document = new Document($customer['id'], $customer);
            $this->client->getIndex('customers')->addDocument($document);
        }

        return Command::SUCCESS;
    }
}
