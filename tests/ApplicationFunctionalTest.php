<?php

declare(strict_types=1);

namespace App\Tests;

use App\Command\CreateUserCommand;
use App\Command\SeedDemoDataCommand;
use App\Entity\Product;
use App\Entity\User;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use App\Service\ProductManager;
use App\Service\UserManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\HttpFoundation\Response;

class ApplicationFunctionalTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $entityManager;
    private UserManager $userManager;
    private ProductManager $productManager;
    private UserRepository $userRepository;
    private ProductRepository $productRepository;

    private User $adminUser;
    private User $staffOne;
    private User $staffTwo;
    private Product $adminProduct;
    private Product $staffOneProduct;
    private Product $staffTwoProduct;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $container = static::getContainer();

        $this->entityManager = $container->get(EntityManagerInterface::class);
        $this->userManager = $container->get(UserManager::class);
        $this->productManager = $container->get(ProductManager::class);
        $this->userRepository = $container->get(UserRepository::class);
        $this->productRepository = $container->get(ProductRepository::class);

        $schemaTool = new SchemaTool($this->entityManager);
        $metadata = $this->entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool->dropDatabase();
        $schemaTool->createSchema($metadata);

        $this->seedFixtures();
    }

    private function seedFixtures(): void
    {
        $this->adminUser = (new User())
            ->setName('Admin User')
            ->setEmail('admin@test.local')
            ->setStatus(User::STATUS_ACTIVE);
        $this->userManager->createUser($this->adminUser, 'password123', User::ROLE_ADMIN);

        $this->staffOne = (new User())
            ->setName('Staff One')
            ->setEmail('staff1@test.local')
            ->setStatus(User::STATUS_ACTIVE);
        $this->userManager->createUser($this->staffOne, 'password123', User::ROLE_STAFF);

        $this->staffTwo = (new User())
            ->setName('Staff Two')
            ->setEmail('staff2@test.local')
            ->setStatus(User::STATUS_ACTIVE);
        $this->userManager->createUser($this->staffTwo, 'password123', User::ROLE_STAFF);

        $this->adminProduct = (new Product())
            ->setName('Admin Laptop')
            ->setSku('ADM-LAP-01')
            ->setCategory('Electronics')
            ->setPrice('1200.00')
            ->setStockQuantity(5)
            ->setDescription('Admin owned product');
        $this->productManager->createProduct($this->adminProduct, $this->adminUser);

        $this->staffOneProduct = (new Product())
            ->setName('Staff One Mouse')
            ->setSku('ST1-MOU-01')
            ->setCategory('Accessories')
            ->setPrice('25.50')
            ->setStockQuantity(30)
            ->setDescription('Staff One owned product');
        $this->productManager->createProduct($this->staffOneProduct, $this->staffOne);

        $this->staffTwoProduct = (new Product())
            ->setName('Staff Two Desk')
            ->setSku('ST2-DSK-01')
            ->setCategory('Furniture')
            ->setPrice('350.00')
            ->setStockQuantity(12)
            ->setDescription('Staff Two owned product');
        $this->productManager->createProduct($this->staffTwoProduct, $this->staffTwo);
    }

    public function testUnauthorizedAccessIsRedirectedToLogin(): void
    {
        $this->client->request('GET', '/admin');
        self::assertResponseRedirects('/login');

        $this->client->request('GET', '/products');
        self::assertResponseRedirects('/login');

        $this->client->request('GET', '/profile');
        self::assertResponseRedirects('/login');
    }

    public function testLogoutEndsTheAuthenticatedSession(): void
    {
        $this->client->loginUser($this->staffOne);

        $this->client->request('GET', '/logout');
        self::assertResponseRedirects('/login');

        $this->client->request('GET', '/profile');
        self::assertResponseRedirects('/login');
    }

    public function testCustomerCanCreateAnAccountAndLogin(): void
    {
        $crawler = $this->client->request('GET', '/register');
        self::assertResponseIsSuccessful();

        $form = $crawler->selectButton('Sign Up')->form([
            'first_name' => 'Juan',
            'last_name' => 'Dela Cruz',
            'email' => 'juan@test.local',
            'password' => 'password123',
        ]);
        $this->client->submit($form);
        self::assertResponseRedirects('/login');

        $this->client->followRedirect();
        self::assertSelectorTextContains('body', 'Your account has been created. Please sign in.');

        $user = $this->userRepository->findOneBy(['email' => 'juan@test.local']);
        self::assertNotNull($user);
        self::assertSame(User::ROLE_USER, $user->getPrimaryRole());

        $loginForm = $this->client->request('GET', '/login')->selectButton('Sign In')->form([
            '_username' => 'juan@test.local',
            '_password' => 'password123',
        ]);
        $this->client->submit($loginForm);
        self::assertResponseRedirects('/');
    }

    public function testRegistrationRejectsInvalidCsrfToken(): void
    {
        $this->client->request('POST', '/register', [
            '_token' => 'invalid-token',
            'first_name' => 'No',
            'last_name' => 'Token',
            'email' => 'no-token@test.local',
            'password' => 'password123',
        ]);
        self::assertResponseRedirects('/register');

        $this->client->followRedirect();
        self::assertSelectorTextContains('.alert-error', 'Invalid security token');
        self::assertNull($this->userRepository->findOneBy(['email' => 'no-token@test.local']));
    }

    public function testPublicNavigationLabelsTheStorefrontAsHome(): void
    {
        $crawler = $this->client->request('GET', '/login');

        self::assertResponseIsSuccessful();
        self::assertSame('Enter your username', $crawler->filter('#username')->attr('placeholder'));
        self::assertSame('HOME', trim($crawler->filter('header nav a')->first()->text()));
        self::assertStringNotContainsString('Storefront', $crawler->filter('header nav')->text());

        $crawler = $this->client->request('GET', '/collections');
        self::assertResponseIsSuccessful();
        self::assertSame('HOME', trim($crawler->filter('header nav a')->first()->text()));

        $this->client->loginUser($this->staffOne);
        $crawler = $this->client->request('GET', '/collections');
        self::assertResponseIsSuccessful();
        self::assertSame(1, $crawler->filter('a[aria-label="Profile"] svg')->count());
        self::assertSame(1, $crawler->filter('a[aria-label="Cart"] svg')->count());
    }

    public function testPasswordsAreHashedAndLoginWorks(): void
    {
        self::assertNotSame('password123', $this->adminUser->getPassword());
        self::assertTrue($this->userManager->isPasswordValid($this->adminUser, 'password123'));

        $crawler = $this->client->request('GET', '/login');
        self::assertResponseIsSuccessful();

        $form = $crawler->selectButton('Sign In')->form([
            '_username' => 'staff1@test.local',
            '_password' => 'password123',
        ]);
        $this->client->submit($form);
        self::assertResponseRedirects('/');

        $this->client->followRedirect();
        self::assertResponseRedirects('/dashboard');

        $this->client->followRedirect();
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1.staff-title', 'Welcome back, Staff One');
    }

    public function testLoginPageDoesNotShowDemoAccounts(): void
    {
        $crawler = $this->client->request('GET', '/login');

        self::assertResponseIsSuccessful();
        self::assertSame(0, $crawler->filter('.demo-credentials')->count());
        self::assertSame('Enter your username', $crawler->filter('#username')->attr('placeholder'));
    }

    public function testAccountAndSeedCommandsWorkWithoutHardcodedPasswords(): void
    {
        $createUserCommand = static::getContainer()->get(CreateUserCommand::class);
        $createUserTester = new CommandTester($createUserCommand);
        $initialUserCount = \count($this->userRepository->findAll());

        self::assertSame(1, $createUserTester->execute([], ['interactive' => false]));
        self::assertSame($initialUserCount, \count($this->userRepository->findAll()));
        self::assertNull($this->userRepository->findOneBy(['email' => 'admin@roa.com']));
        self::assertNull($this->userRepository->findOneBy(['email' => 'staff@roa.com']));

        $seedCommand = static::getContainer()->get(SeedDemoDataCommand::class);
        $seedTester = new CommandTester($seedCommand);

        self::assertSame(1, $seedTester->execute([]));
        self::assertStringContainsString('app:create-user', $seedTester->getDisplay());
        self::assertSame(3, $this->productRepository->countAllRecords());

        $createUserTester->setInputs([
            'AdminSecret123!',
            'AdminSecret123!',
            'StaffSecret123!',
            'StaffSecret123!',
        ]);

        self::assertSame(0, $createUserTester->execute([], ['interactive' => true]));
        self::assertStringNotContainsString('AdminSecret123!', $createUserTester->getDisplay());
        self::assertStringNotContainsString('StaffSecret123!', $createUserTester->getDisplay());

        $admin = $this->userRepository->findOneBy(['email' => 'admin@roa.com']);
        $staff = $this->userRepository->findOneBy(['email' => 'staff@roa.com']);
        self::assertNotNull($admin);
        self::assertNotNull($staff);
        self::assertNotSame('AdminSecret123!', $admin->getPassword());
        self::assertNotSame('StaffSecret123!', $staff->getPassword());
        self::assertTrue($this->userManager->isPasswordValid($admin, 'AdminSecret123!'));
        self::assertTrue($this->userManager->isPasswordValid($staff, 'StaffSecret123!'));

        self::assertSame(0, $seedTester->execute([]));
        self::assertSame(10, $this->productRepository->countAllRecords());
        self::assertNull($this->userRepository->findOneBy(['email' => 'staff2@roa.com']));
        $seededProduct = $this->productRepository->findOneBy(['sku' => 'SX-DRIVE-004']);
        self::assertNotNull($seededProduct);
        self::assertSame('staff@roa.com', $seededProduct->getCreatedBy()?->getEmail());
    }

    public function testDisabledUserCannotLogin(): void
    {
        $this->userManager->updateStatus($this->staffOne, User::STATUS_DISABLED);

        $crawler = $this->client->request('GET', '/login');
        $form = $crawler->selectButton('Sign In')->form([
            '_username' => 'staff1@test.local',
            '_password' => 'password123',
        ]);
        $this->client->submit($form);
        self::assertResponseRedirects('/login');

        $this->client->followRedirect();
        self::assertSelectorTextContains('.alert-error', 'disabled');
    }

    public function testStaffCannotAccessAdminPagesAndReceives403(): void
    {
        $this->client->loginUser($this->staffOne);

        // Staff navigation should NOT contain Admin Dashboard or Staff & Users links
        $crawler = $this->client->request('GET', '/dashboard');
        self::assertResponseIsSuccessful();
        self::assertSame(0, $crawler->filter('body.portal-page')->count());
        self::assertSelectorTextContains('body', 'My Product Records');
        self::assertSelectorTextContains('body', 'Staff One Mouse');
        self::assertSame(1, $crawler->filter('.sidebar-nav a:contains("My Profile")')->count());
        self::assertSame(1, $crawler->filter('.sidebar-nav a.active:contains("Overview")')->count());
        self::assertSame(0, $crawler->filter('nav a:contains("Admin Dashboard")')->count());
        self::assertSame(0, $crawler->filter('nav a:contains("Staff & Users")')->count());

        $crawler = $this->client->request('GET', '/profile');
        self::assertResponseIsSuccessful();
        self::assertSame(0, $crawler->filter('body.portal-page')->count());
        self::assertSame(1, $crawler->filter('body.staff-profile-page')->count());
        self::assertSame(1, $crawler->filter('.sidebar-nav a:contains("View Home")')->count());
        self::assertSame(1, $crawler->filter('.sidebar-nav a:contains("Logout")')->count());
        self::assertSame(1, $crawler->filter('.sidebar-nav a.active:contains("My Profile")')->count());
        self::assertSame(0, $crawler->filter('.portal-logout')->count());

        $crawler = $this->client->request('GET', '/products');
        self::assertResponseIsSuccessful();
        self::assertSame(0, $crawler->filter('body.portal-page')->count());
        self::assertSame(1, $crawler->filter('.sidebar-nav a.active:contains("Inventory")')->count());
        self::assertSame(1, $crawler->filter('.sidebar-nav a:contains("View Home")')->count());
        self::assertSame(1, $crawler->filter('.sidebar-nav a:contains("Logout")')->count());

        // Attempting to bypass URL manually to /admin or /admin/users returns 403 Forbidden
        $this->client->request('GET', '/admin');
        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);

        $this->client->request('GET', '/admin/users');
        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);

        $this->client->request('GET', '/admin/users/new');
        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testAdminDashboardDisplaysTotalsAndAdminMenu(): void
    {
        $this->client->loginUser($this->adminUser);

        $this->client->request('GET', '/');
        self::assertResponseRedirects('/admin');

        $crawler = $this->client->request('GET', '/admin');
        self::assertResponseIsSuccessful();

        self::assertGreaterThan(0, $crawler->filter('nav a:contains("Dashboard")')->count());
        self::assertGreaterThan(0, $crawler->filter('nav a:contains("Users & Staff")')->count());
        self::assertSame(1, $crawler->filter('.sidebar-nav a:contains("My Profile")')->count());

        self::assertSelectorTextSame('#stat-total-users', '3');
        self::assertSelectorTextSame('#stat-total-staff', '2');
        self::assertSelectorTextSame('#stat-total-records', '3');

        $this->client->click($crawler->filter('.sidebar-nav a:contains("My Profile")')->link());
        self::assertResponseIsSuccessful();
        self::assertSelectorExists('body.admin-profile-page');
        self::assertSelectorExists('.sidebar-nav a.active[aria-current="page"]');
        self::assertSame(1, $this->client->getCrawler()->filter('.sidebar-nav a:contains("View Home")')->count());
        self::assertSame(1, $this->client->getCrawler()->filter('.sidebar-nav a:contains("Logout")')->count());
        self::assertSelectorTextContains('.topbar', 'admin');
        self::assertSelectorTextContains('h1', 'My Account Profile');
    }

    public function testCustomerCannotAccessStaffProductManagement(): void
    {
        $customer = (new User())
            ->setName('Store Customer')
            ->setEmail('customer@test.local')
            ->setStatus(User::STATUS_ACTIVE);
        $this->userManager->createUser($customer, 'password123', User::ROLE_USER);

        $this->client->loginUser($customer);
        $this->client->request('GET', '/products');

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testAdminProductSearchFiltersAndInventoryTotals(): void
    {
        $this->client->loginUser($this->adminUser);

        $cobaltProduct = (new Product())
            ->setName('Cobalt Runner')
            ->setSku('ST1-CBL-03')
            ->setCategory('Running')
            ->setColor('Deep Cobalt')
            ->setPrice('50.00')
            ->setStockQuantity(0)
            ->setDescription('A lightweight running shoe.');
        $this->productManager->createProduct($cobaltProduct, $this->staffOne);

        $crawler = $this->client->request(
            'GET',
            '/products?q=Cobalt&category=Running&creator='.$this->staffOne->getId(),
        );
        self::assertResponseIsSuccessful();
        self::assertSame(1, $crawler->filter('.data-table tbody tr')->count());
        self::assertSelectorTextContains('.data-table', 'Cobalt Runner');
        self::assertSelectorTextSame('#stat-total-products', '4');
        self::assertSelectorTextSame('#stat-low-stock', '2');
        self::assertSelectorTextSame('#stat-inventory-value', '$10,965.00');
        self::assertSame(
            (string) $this->staffOne->getId(),
            $crawler->filter('select[name="creator"] option[selected]')->attr('value'),
        );

        $crawler = $this->client->request('GET', '/products?stock=in_stock');
        self::assertResponseIsSuccessful();
        self::assertSame(3, $crawler->filter('.data-table tbody tr')->count());

        $crawler = $this->client->request('GET', '/products?stock=out');
        self::assertResponseIsSuccessful();
        self::assertSame(1, $crawler->filter('.data-table tbody tr')->count());
        self::assertSelectorTextContains('.data-table', 'Cobalt Runner');
    }

    public function testAdminUserSearchAndStatusFiltersAreCombined(): void
    {
        $this->userManager->updateStatus($this->staffTwo, User::STATUS_DISABLED);
        $this->client->loginUser($this->adminUser);

        $this->client->request('GET', '/admin/users?q=Staff%20Two&status=active');
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('.data-table', 'No users found.');

        $this->client->request('GET', '/admin/users?q=Staff%20Two&status=disabled');
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('.data-table', 'Staff Two');
    }

    public function testAdminCanCreateUpdateAndDeleteStaffAccounts(): void
    {
        $this->client->loginUser($this->adminUser);

        // 1. Create a new staff user
        $crawler = $this->client->request('GET', '/admin/users/new');
        self::assertResponseIsSuccessful();

        $form = $crawler->selectButton('Create Account')->form([
            'admin_user[name]' => 'New Staff Member',
            'admin_user[email]' => 'newstaff@test.local',
            'admin_user[primaryRole]' => User::ROLE_STAFF,
            'admin_user[status]' => User::STATUS_ACTIVE,
            'admin_user[plainPassword][first]' => 'secret123',
            'admin_user[plainPassword][second]' => 'secret123',
        ]);
        $this->client->submit($form);
        self::assertResponseRedirects('/admin/users');
        $this->client->followRedirect();
        self::assertSelectorTextContains('.alert-success', 'New Staff Member');

        $createdUser = $this->userRepository->findOneBy(['email' => 'newstaff@test.local']);
        self::assertNotNull($createdUser);
        self::assertTrue($createdUser->isStaff());
        self::assertNotSame('secret123', $createdUser->getPassword());

        // 2. Edit the user (change name, role, and reset password)
        $crawler = $this->client->request('GET', '/admin/users/'.$createdUser->getId().'/edit');
        self::assertResponseIsSuccessful();

        $editForm = $crawler->selectButton('SAVE CHANGES')->form([
            'admin_user[name]' => 'Updated Staff Name',
            'admin_user[email]' => 'newstaff@test.local',
            'admin_user[primaryRole]' => User::ROLE_ADMIN,
            'admin_user[status]' => User::STATUS_ACTIVE,
            'admin_user[plainPassword][first]' => 'newpass456',
            'admin_user[plainPassword][second]' => 'newpass456',
        ]);
        $this->client->submit($editForm);
        self::assertResponseRedirects('/admin/users');

        $this->entityManager->clear();
        $updatedUser = $this->userRepository->find($createdUser->getId());
        self::assertNotNull($updatedUser);
        self::assertSame('Updated Staff Name', $updatedUser->getName());
        self::assertTrue($updatedUser->isAdmin());
        self::assertTrue($this->userManager->isPasswordValid($updatedUser, 'newpass456'));

        $ownedProduct = (new Product())
            ->setName('Owned Inventory Item')
            ->setSku('NEWSTF-001')
            ->setCategory('Accessories')
            ->setPrice('20.00')
            ->setStockQuantity(4)
            ->setDescription('Product owned by the account being deleted');
        $this->productManager->createProduct($ownedProduct, $updatedUser);

        $crawler = $this->client->request('GET', '/admin/users/'.$updatedUser->getId());
        self::assertResponseIsSuccessful();
        $detailDeleteForm = $crawler->filter(\sprintf('form[action="/admin/users/%d/delete"]', $updatedUser->getId()));
        self::assertStringContainsString('1 owned product record(s)', $detailDeleteForm->attr('onsubmit') ?? '');

        // 3. Delete the user
        $crawler = $this->client->request('GET', '/admin/users');
        $listDeleteForm = $crawler->filter(\sprintf('form[action="/admin/users/%d/delete"]', $updatedUser->getId()));
        self::assertStringContainsString('1 owned product record(s)', $listDeleteForm->attr('onsubmit') ?? '');
        $deleteForm = $listDeleteForm->form();
        $this->client->submit($deleteForm);
        self::assertResponseRedirects('/admin/users');

        $this->entityManager->clear();
        self::assertNull($this->userRepository->find($createdUser->getId()));
        self::assertNull($this->productRepository->find($ownedProduct->getId()));
    }

    public function testStaffCanCreateAndEditOwnRecordsButCannotEditOrDeleteOthers(): void
    {
        $this->client->loginUser($this->staffOne);

        // 1. Staff creates a new product
        $crawler = $this->client->request('GET', '/products/new');
        self::assertResponseIsSuccessful();

        $form = $crawler->selectButton('Create Product')->form([
            'product_form[name]' => 'Staff One Webcam',
            'product_form[sku]' => 'ST1-CAM-99',
            'product_form[category]' => 'Electronics',
            'product_form[price]' => '59.99',
            'product_form[stockQuantity]' => 14,
            'product_form[description]' => '1080p HD Webcam',
        ]);
        $this->client->submit($form);
        self::assertResponseRedirects('/products');

        $newProduct = $this->productRepository->findOneBy(['sku' => 'ST1-CAM-99']);
        self::assertNotNull($newProduct);
        self::assertSame($this->staffOne->getId(), $newProduct->getCreatedBy()?->getId());

        // 2. Staff edits their own product
        $crawler = $this->client->request('GET', '/products/'.$this->staffOneProduct->getId().'/edit');
        self::assertResponseIsSuccessful();

        $editForm = $crawler->selectButton('Save Changes')->form([
            'product_form[name]' => 'Staff One Pro Mouse',
            'product_form[sku]' => 'ST1-MOU-01',
            'product_form[category]' => 'Accessories',
            'product_form[price]' => '35.00',
            'product_form[stockQuantity]' => 20,
            'product_form[description]' => 'Updated description',
        ]);
        $this->client->submit($editForm);
        self::assertResponseRedirects('/products');

        // 3. Staff CANNOT edit Admin's product or Staff Two's product (403 Forbidden)
        $this->client->request('GET', '/products/'.$this->adminProduct->getId().'/edit');
        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);

        $this->client->request('GET', '/products/'.$this->staffTwoProduct->getId().'/edit');
        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);

        // 4. Staff CANNOT delete Admin's product or Staff Two's product (403 Forbidden)
        $this->client->request('POST', '/products/'.$this->adminProduct->getId().'/delete', [
            '_token' => 'invalid',
        ]);
        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);

        $this->client->request('POST', '/products/'.$this->staffTwoProduct->getId().'/delete', [
            '_token' => 'invalid',
        ]);
        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);

        $crawler = $this->client->request('GET', '/products');
        $deleteForm = $crawler->filter(\sprintf('form[action="/products/%d/delete"]', $this->staffOneProduct->getId()))->form();
        $this->client->submit($deleteForm);
        self::assertResponseRedirects('/products');

        $this->entityManager->clear();
        self::assertNull($this->productRepository->find($this->staffOneProduct->getId()));
    }

    public function testAdminCanEditAndDeleteAnyStaffProduct(): void
    {
        $this->client->loginUser($this->adminUser);

        // Admin edits Staff One's product
        $crawler = $this->client->request('GET', '/products/'.$this->staffOneProduct->getId().'/edit');
        self::assertResponseIsSuccessful();

        $editForm = $crawler->selectButton('Save Changes')->form([
            'product_form[name]' => 'Admin Edited Staff Mouse',
            'product_form[sku]' => 'ST1-MOU-01',
            'product_form[category]' => 'Accessories',
            'product_form[price]' => '29.99',
            'product_form[stockQuantity]' => 18,
            'product_form[description]' => 'Edited by Admin',
        ]);
        $this->client->submit($editForm);
        self::assertResponseRedirects('/products');

        // Admin deletes Staff Two's product
        $crawler = $this->client->request('GET', '/products');
        $deleteForm = $crawler->filter(\sprintf('form[action="/products/%d/delete"]', $this->staffTwoProduct->getId()))->form();
        $this->client->submit($deleteForm);
        self::assertResponseRedirects('/products');

        $this->entityManager->clear();
        self::assertNull($this->productRepository->find($this->staffTwoProduct->getId()));
    }

    public function testUserCanViewProfileAndChangeOwnPassword(): void
    {
        $this->client->loginUser($this->staffOne);

        $crawler = $this->client->request('GET', '/profile');
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('body', 'staff1@test.local');

        $form = $crawler->selectButton('Update Password')->form([
            'change_password_form[currentPassword]' => 'password123',
            'change_password_form[newPassword][first]' => 'mynewpass789',
            'change_password_form[newPassword][second]' => 'mynewpass789',
        ]);
        $this->client->submit($form);
        self::assertResponseRedirects('/profile');

        $this->entityManager->clear();
        $reloadedStaff = $this->userRepository->find($this->staffOne->getId());
        self::assertNotNull($reloadedStaff);
        self::assertTrue($this->userManager->isPasswordValid($reloadedStaff, 'mynewpass789'));
    }

    public function testCustomerProfileUsesTheSharedAccountPanelDesign(): void
    {
        $customer = (new User())
            ->setName('Store Customer')
            ->setEmail('customer@test.local')
            ->setStatus(User::STATUS_ACTIVE);
        $this->userManager->createUser($customer, 'password123', User::ROLE_USER);
        $this->client->loginUser($customer);

        $crawler = $this->client->request('GET', '/profile');

        self::assertResponseIsSuccessful();
        self::assertSame(1, $crawler->filter('body.portal-page.account-panel-page')->count());
        self::assertGreaterThan(0, $crawler->filter('.profile-card')->count());
    }

    public function testStrideXStorefrontCheckoutAndAdminOrderManagement(): void
    {
        // Public storefront pages work
        $this->client->request('GET', '/home');
        self::assertResponseIsSuccessful();

        $this->client->request('GET', '/store');
        self::assertResponseRedirects('/home', Response::HTTP_MOVED_PERMANENTLY);

        $this->client->request('GET', '/shop');
        self::assertResponseIsSuccessful();

        $this->client->request('GET', '/collections');
        self::assertResponseIsSuccessful();

        // About Us page loads without the contact form
        $crawler = $this->client->request('GET', '/about');
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('body', 'About Us');
        self::assertCount(0, $crawler->filter('form:contains("Send Message")'));

        // Logged-in user can add to cart and checkout
        $this->client->loginUser($this->staffOne);
        $this->client->request('GET', '/cart/action?action=add&id='.$this->adminProduct->getId());
        self::assertResponseRedirects('/cart');

        $crawler = $this->client->request('GET', '/checkout');
        self::assertResponseIsSuccessful();

        $checkoutForm = $crawler->selectButton('PLACE ORDER')->form([
            'first_name' => 'Staff',
            'last_name' => 'One',
            'email' => 'staff1@test.local',
            'address' => '123 Balugo Road',
            'city' => 'Dumaguete City',
            'postal' => '6200',
            'country' => 'PH',
            'payment' => 'gcash',
        ]);
        $this->client->submit($checkoutForm);
        self::assertResponseRedirects();
        $this->client->followRedirect();
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('body', 'ORDER CONFIRMED');

        // Admin can view orders, inventory, and messages
        $this->client->loginUser($this->adminUser);
        $this->client->request('GET', '/admin/orders');
        self::assertResponseIsSuccessful();

        $this->client->request('GET', '/admin/inventory');
        self::assertResponseIsSuccessful();

        $this->client->request('GET', '/admin/messages');
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('body', 'No messages found.');
    }
}
