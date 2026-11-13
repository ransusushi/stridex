<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\ContactMessage;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Product;
use App\Entity\User;
use App\Repository\ContactMessageRepository;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use App\Service\ProductManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpKernel\KernelInterface;

#[AsCommand(
    name: 'app:seed',
    description: 'Seeds StrideX products, orders, and contact messages.',
)]
class SeedDemoDataCommand extends Command
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly ProductRepository $productRepository,
        private readonly OrderRepository $orderRepository,
        private readonly ContactMessageRepository $messageRepository,
        private readonly ProductManager $productManager,
        private readonly EntityManagerInterface $entityManager,
        private readonly KernelInterface $kernel,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        if ('prod' === $this->kernel->getEnvironment()) {
            $io->error('Demo data seeding is disabled in production.');

            return Command::FAILURE;
        }

        $admin = $this->userRepository->findOneBy(['email' => 'admin@roa.com']);
        $staff = $this->userRepository->findOneBy(['email' => 'staff@roa.com']);
        if (null === $admin || null === $staff) {
            $io->error('Create the default admin and staff accounts first by running "php bin/console app:create-user".');

            return Command::FAILURE;
        }

        /** @var array<string, User> $userMap */
        $userMap = [
            'admin@roa.com' => $admin,
            'staff@roa.com' => $staff,
        ];

        // Exact 7 StrideX footwear products from C:\xampp\htdocs\stride\cache\products.json
        $productsToSeed = [
            [
                'name' => 'StrideX Raptor',
                'sku' => 'SX-RAPTOR-007',
                'color' => 'Monochrome',
                'category' => 'Performance',
                'gender' => 'women',
                'badge' => 'LIMITED',
                'bg' => 'bg-gray',
                'price' => '89.99',
                'stock' => 9,
                'image' => 'image/products/prod_6aa46461dfd1d.png',
                'description' => 'Precision-engineered monochrome runner with carbon-infused stability plate and high-traction outsole.',
                'owner' => 'staff@roa.com',
            ],
            [
                'name' => 'StrideX Retro',
                'sku' => 'SX-RETRO-006',
                'color' => 'Rose Gold',
                'category' => 'Heritage',
                'gender' => 'women',
                'badge' => 'NEW DROP',
                'bg' => 'bg-red',
                'price' => '99.99',
                'stock' => 9,
                'image' => 'image/products/prod_6aa46482e8d59.png',
                'description' => 'Court-inspired heritage silhouette updated with Rose Gold accents and responsive dual-density foam.',
                'owner' => 'staff@roa.com',
            ],
            [
                'name' => 'StrideX Shift',
                'sku' => 'SX-SHIFT-005',
                'color' => 'Pure White',
                'category' => 'Everyday',
                'gender' => 'women',
                'badge' => 'BESTSELLER',
                'bg' => 'bg-black',
                'price' => '84.99',
                'stock' => 10,
                'image' => 'image/products/prod_6aa464ea4de79.png',
                'description' => 'Ultra-light breathable mesh upper designed for all-day movement and effortless street styling.',
                'owner' => 'staff@roa.com',
            ],
            [
                'name' => 'STRIDEX DRIVE',
                'sku' => 'SX-DRIVE-004',
                'color' => 'Pearl Gray',
                'category' => 'Running',
                'gender' => 'men',
                'badge' => 'BESTSELLER',
                'bg' => 'bg-red',
                'price' => '79.99',
                'stock' => 10,
                'image' => 'image/men-drive.png',
                'description' => 'High-energy return road runner built for daily mileage and maximum heel-to-toe transition.',
                'owner' => 'staff@roa.com',
            ],
            [
                'name' => 'STRIDEX LUNA',
                'sku' => 'SX-LUNA-003',
                'color' => 'Carbon Gray',
                'category' => 'Street',
                'gender' => 'men',
                'badge' => 'LIMITED',
                'bg' => 'bg-gray',
                'price' => '89.99',
                'stock' => 10,
                'image' => 'image/products/prod_6aa849cbcb00a.png',
                'description' => 'Sculpted street-ready silhouette in Carbon Gray with reflective detailing and plush collar cushioning.',
                'owner' => 'staff@roa.com',
            ],
            [
                'name' => 'StrideX Aura',
                'sku' => 'SX-AURA-002',
                'color' => 'Cloud White',
                'category' => 'Lifestyle',
                'gender' => 'men',
                'badge' => 'NEW DROP',
                'bg' => 'bg-black',
                'price' => '79.99',
                'stock' => 10,
                'image' => 'image/products/prod_6aa84a0d6036f.png',
                'description' => 'Clean minimalist Cloud White sneaker crafted with premium synthetic leather and cushioned footbed.',
                'owner' => 'admin@roa.com',
            ],
            [
                'name' => 'STRIDEX URBAN',
                'sku' => 'SX-URBAN-001',
                'color' => 'Midnight Red',
                'category' => 'Street',
                'gender' => 'men',
                'badge' => 'BESTSELLER',
                'bg' => 'bg-red',
                'price' => '79.99',
                'stock' => 10,
                'image' => 'image/urban-collection.png',
                'description' => 'Flagship StrideX Urban low-top in Midnight Red engineered for grip, durability, and iconic street presence.',
                'owner' => 'admin@roa.com',
            ],
        ];

        $firstProduct = null;
        foreach ($productsToSeed as $item) {
            $existingProduct = $this->productRepository->findOneBy(['sku' => $item['sku']]);
            if (null === $existingProduct) {
                $product = (new Product())
                    ->setName($item['name'])
                    ->setSku($item['sku'])
                    ->setColor($item['color'])
                    ->setCategory($item['category'])
                    ->setGender($item['gender'])
                    ->setBadge($item['badge'])
                    ->setBg($item['bg'])
                    ->setPrice($item['price'])
                    ->setStockQuantity($item['stock'])
                    ->setImage($item['image'])
                    ->setDescription($item['description']);

                $this->productManager->createProduct($product, $userMap[$item['owner']]);
                $firstProduct ??= $product;
            } else {
                $firstProduct ??= $existingProduct;
            }
        }

        if (0 === \count($this->orderRepository->findAll()) && null !== $firstProduct) {
            $order = (new Order())
                ->setUser($userMap['staff@roa.com'])
                ->setFirstName('Juan')
                ->setLastName('Dela Cruz')
                ->setEmail('staff@roa.com')
                ->setAddress('Balugo Road')
                ->setCity('Dumaguete City')
                ->setPostal('6200')
                ->setCountry('PH')
                ->setPaymentMethod('gcash')
                ->setTotal($firstProduct->getPrice())
                ->setStatus(Order::STATUS_PAID)
                ->setTransactionId('TXN-STRIDEX-0001');

            $orderItem = (new OrderItem())
                ->setProduct($firstProduct)
                ->setProductName($firstProduct->getName())
                ->setPrice($firstProduct->getPrice())
                ->setQuantity(1)
                ->setTotal($firstProduct->getPrice());

            $order->addItem($orderItem);
            $this->entityManager->persist($order);
        }

        if (0 === \count($this->messageRepository->findAll())) {
            $msg = (new ContactMessage())
                ->setName('Alex Rivera')
                ->setEmail('alex.rivera@example.com')
                ->setSubject('Size availability for StrideX Raptor')
                ->setMessage('Hi StrideX team! Do you have US Size 10 available for the StrideX Raptor Monochrome drop?');
            $this->entityManager->persist($msg);
        }

        $this->entityManager->flush();

        $io->success('StrideX sneakers, orders, and contact messages have been seeded.');

        return Command::SUCCESS;
    }
}
