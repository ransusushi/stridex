<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\ContactMessage;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\User;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class StorefrontController extends AbstractController
{
    #[Route('/store', name: 'app_storefront', methods: ['GET'])]
    public function home(ProductRepository $productRepository): Response
    {
        return $this->render('store/home.html.twig', [
            'featuredProducts' => $productRepository->findRecent(6),
        ]);
    }

    #[Route('/shop', name: 'app_shop', methods: ['GET'])]
    public function shop(Request $request, ProductRepository $productRepository): Response
    {
        $category = $request->query->getString('category');
        $search = $request->query->getString('q');

        $products = $productRepository->findByFilters(
            search: $search,
            category: $category,
        );

        return $this->render('store/shop.html.twig', [
            'products' => $products,
            'selectedCategory' => $category,
            'searchQuery' => $search,
        ]);
    }

    #[Route('/collections', name: 'app_collections', methods: ['GET'])]
    public function collections(): Response
    {
        $collections = [
            [
                'name' => 'VELOCITY 01',
                'category' => 'Running',
                'description' => 'Ultra-light performance for everyday miles.',
                'price' => 160,
                'image' => 'image/urban-collection.png',
            ],
            [
                'name' => 'AERO MAX',
                'category' => 'Performance',
                'description' => 'Maximum energy return. Minimum weight.',
                'price' => 210,
                'image' => 'image/performance-collection.png',
            ],
            [
                'name' => 'STREETFORM',
                'category' => 'Lifestyle',
                'description' => 'Performance comfort. Everyday style.',
                'price' => 140,
                'image' => 'image/heritage-collection.png',
            ],
        ];

        return $this->render('store/collections.html.twig', [
            'collections' => $collections,
        ]);
    }

    #[Route('/about', name: 'app_about', methods: ['GET'])]
    public function about(): Response
    {
        $about = [
            'company_name' => 'StrideX',
            'email' => 'stridex@gmail.com',
            'phone' => '+631234567',
            'address' => 'Dumaguete City, Balugo Road',
            'description' => 'StrideX crafts high-performance athletic and street footwear engineered for speed, agility, and everyday comfort.',
            'faq' => [
                ['question' => 'What materials are used in StrideX shoes?', 'answer' => 'We use high-grade synthetic leather, breathable mesh, and durable rubber outsoles.'],
                ['question' => 'How do I find my size?', 'answer' => 'Check our size guide on the product page. We offer standard US sizing.'],
                ['question' => 'What is your return policy?', 'answer' => 'We offer a 30-day return policy for all unworn items.'],
                ['question' => 'Do you ship internationally?', 'answer' => 'Yes, we ship to over 50 countries worldwide.'],
            ],
        ];

        return $this->render('store/about.html.twig', [
            'about' => $about,
        ]);
    }

    #[Route('/contact', name: 'app_contact', methods: ['GET', 'POST'])]
    public function contact(Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($request->isMethod('POST')) {
            $name = trim($request->request->getString('name'));
            $email = trim($request->request->getString('email'));
            $subject = trim($request->request->getString('subject'));
            $messageText = trim($request->request->getString('message'));

            if ('' === $name || '' === $email || '' === $subject || '' === $messageText) {
                $this->addFlash('error', 'All contact form fields are required.');
            } elseif (!filter_var($email, \FILTER_VALIDATE_EMAIL)) {
                $this->addFlash('error', 'Please enter a valid email address.');
            } else {
                $msg = (new ContactMessage())
                    ->setName($name)
                    ->setEmail($email)
                    ->setSubject($subject)
                    ->setMessage($messageText);

                $entityManager->persist($msg);
                $entityManager->flush();

                $this->addFlash('success', 'Your message has been sent to the StrideX team!');

                return $this->redirectToRoute('app_contact');
            }
        }

        return $this->render('store/contact.html.twig', [
            'contact' => [
                'email' => 'stridex@gmail.com',
                'phone' => '+631234567',
                'address' => 'Dumaguete City, Balugo Road',
            ],
        ]);
    }

    #[Route('/cart', name: 'app_cart', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function cart(Request $request): Response
    {
        /** @var array<int, array{id: int, name: string, price: string, image: string, quantity: int}> $cart */
        $cart = $request->getSession()->get('cart', []);
        $total = 0.0;
        foreach ($cart as $item) {
            $total += (float) $item['price'] * (int) $item['quantity'];
        }

        return $this->render('store/cart.html.twig', [
            'cartItems' => $cart,
            'total' => $total,
        ]);
    }

    #[Route('/cart/action', name: 'app_cart_action', methods: ['POST', 'GET'])]
    #[IsGranted('ROLE_USER')]
    public function cartAction(Request $request, ProductRepository $productRepository): Response
    {
        $session = $request->getSession();
        /** @var array<int, array{id: int, name: string, price: string, image: string, quantity: int}> $cart */
        $cart = $session->get('cart', []);

        $action = $request->query->getString('action', $request->request->getString('action', 'add'));
        $id = $request->query->getInt('id', $request->request->getInt('id', 0));
        $quantity = max(1, $request->query->getInt('quantity', $request->request->getInt('quantity', 1)));

        if ('clear' === $action) {
            $session->set('cart', []);
            $this->addFlash('success', 'Your shopping cart has been cleared.');

            return $this->redirectToRoute('app_cart');
        }

        $product = $productRepository->find($id);
        if (null === $product) {
            $this->addFlash('error', 'Product not found.');

            return $this->redirectToRoute('app_shop');
        }

        if ('add' === $action) {
            if (isset($cart[$id])) {
                $cart[$id]['quantity'] += $quantity;
            } else {
                $cart[$id] = [
                    'id' => (int) $product->getId(),
                    'name' => $product->getName(),
                    'price' => $product->getPrice(),
                    'image' => $product->getImage() ?? 'image/urban-collection.png',
                    'quantity' => $quantity,
                ];
            }
            $this->addFlash('success', \sprintf('Added "%s" to your cart.', $product->getName()));
        } elseif ('update' === $action) {
            $newQty = (int) $request->get('quantity', 1);
            if ($newQty > 0) {
                $cart[$id]['quantity'] = $newQty;
            } else {
                unset($cart[$id]);
            }
        } elseif ('remove' === $action) {
            unset($cart[$id]);
            $this->addFlash('success', 'Item removed from cart.');
        }

        $session->set('cart', $cart);

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/checkout', name: 'app_checkout', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function checkout(
        Request $request,
        ProductRepository $productRepository,
        EntityManagerInterface $entityManager,
    ): Response {
        $session = $request->getSession();
        /** @var array<int, array{id: int, name: string, price: string, image: string, quantity: int}> $cart */
        $cart = $session->get('cart', []);

        if ([] === $cart) {
            return $this->redirectToRoute('app_cart');
        }

        $total = 0.0;
        foreach ($cart as $item) {
            $total += (float) $item['price'] * (int) $item['quantity'];
        }

        /** @var User $user */
        $user = $this->getUser();

        if ($request->isMethod('POST')) {
            $firstName = trim($request->request->getString('first_name', $user->getFirstName()));
            $lastName = trim($request->request->getString('last_name', $user->getLastName()));
            $email = trim($request->request->getString('email', $user->getEmail()));
            $address = trim($request->request->getString('address'));
            $city = trim($request->request->getString('city'));
            $postal = trim($request->request->getString('postal'));
            $country = trim($request->request->getString('country', 'PH'));
            $payment = trim($request->request->getString('payment', 'cod'));

            if ('' === $firstName || '' === $address || '' === $city || '' === $postal) {
                $this->addFlash('error', 'Please fill in all required shipping details.');
            } else {
                $order = (new Order())
                    ->setUser($user)
                    ->setFirstName($firstName)
                    ->setLastName($lastName)
                    ->setEmail($email)
                    ->setAddress($address)
                    ->setCity($city)
                    ->setPostal($postal)
                    ->setCountry($country)
                    ->setPaymentMethod($payment)
                    ->setTotal($total)
                    ->setStatus(Order::STATUS_PAID)
                    ->setTransactionId('TXN-'.date('Ymd').'-'.strtoupper(bin2hex(random_bytes(3))));

                foreach ($cart as $item) {
                    $product = $productRepository->find($item['id']);
                    if (null !== $product) {
                        if ($product->getStockQuantity() < $item['quantity']) {
                            $this->addFlash('error', \sprintf('Insufficient stock for "%s".', $product->getName()));

                            return $this->redirectToRoute('app_checkout');
                        }
                        $product->setStockQuantity($product->getStockQuantity() - $item['quantity']);
                    }

                    $orderItem = (new OrderItem())
                        ->setProduct($product)
                        ->setProductName($item['name'])
                        ->setPrice($item['price'])
                        ->setQuantity($item['quantity'])
                        ->setTotal((float) $item['price'] * $item['quantity']);

                    $order->addItem($orderItem);
                }

                $entityManager->persist($order);
                $entityManager->flush();
                $session->set('cart', []);

                $this->addFlash('success', \sprintf('Order #%d placed successfully!', $order->getId()));

                return $this->redirectToRoute('app_order_receipt', ['id' => $order->getId()]);
            }
        }

        return $this->render('store/checkout.html.twig', [
            'cartItems' => $cart,
            'total' => $total,
            'user' => $user,
        ]);
    }

    #[Route('/order-confirmation/{id}', name: 'app_order_receipt', requirements: ['id' => '\d+'], methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function receipt(Order $order): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();
        if (!$this->isGranted(User::ROLE_ADMIN) && $order->getUser()?->getId() !== $currentUser->getId()) {
            throw $this->createAccessDeniedException('You can only view your own order receipts.');
        }

        return $this->render('store/receipt.html.twig', [
            'order' => $order,
        ]);
    }
}
