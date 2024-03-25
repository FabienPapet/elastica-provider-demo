<?php

namespace App\Command;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Elastica\Client;
use Elastica\Document;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:index',
    description: 'Add a short description for your command',
)]
class IndexCommand extends Command
{
    public function __construct(
        private Client $client,
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->client->getIndex('products')->delete();
        $this->client->getIndex('products')->create();


        $this->entityManager->getRepository(Product::class)
            ->createQueryBuilder('p')
            ->delete()
            ->getQuery()
            ->execute();

        if (($handle = fopen("open4goods-full-gtin-dataset.csv", "r")) !== false) {
            while (($data = fgetcsv($handle)) !== false) {
                $output->write('.');

                $this->import($data);
            }
            fclose($handle);
        }

        return Command::SUCCESS;
    }

    private function import(array $csv)
    {
        [
            $code,
            $brand,
            $model,
            $name,
            $last_updated,
            $gs1_country,
            $gtinType,
            $offers_count,
            $min_price,
            $min_price_compensation,
            $currency,
            $categories,
            $url,
        ] = $csv;

        $data = [
            'code' => $code,
            'brand' => $brand,
//                'model' => $model,
            'name' => $name,
            'last_updated' => $last_updated,
            'gs1_country' => $gs1_country,
            'gtinType' => $gtinType,
            'offers_count' => (int) $offers_count,
            'min_price' => (float) $min_price,
            'min_price_compensation' => (float) $min_price_compensation,
            'currency' => $currency,
            'categories' => $categories,
            'url' => $url,
        ];


        $product = new Product();
        $product->setCode($code);
        $product->setBrand($brand);
        $product->setModel($model);
        $product->setName($name);
        $product->setCurrency($currency);
        $product->setLastUpdated(
            \DateTimeImmutable::createFromFormat('U', $last_updated
            )
        );

        $this->entityManager->persist($product);
        $this->entityManager->flush();
        $this->entityManager->clear();

        $document = new Document($code, $data);

        $this->client->getIndex('products')->addDocument($document);
    }
}
