<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\AdminUserType;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use App\Service\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/users')]
#[IsGranted(User::ROLE_ADMIN)]
class UserController extends AbstractController
{
    #[Route('', name: 'app_admin_user_index', methods: ['GET'])]
    public function index(Request $request, UserRepository $userRepository): Response
    {
        $this->denyAccessUnlessGranted(User::ROLE_ADMIN);

        $search = $request->query->getString('q');
        $role = $request->query->getString('role');
        $status = $request->query->getString('status');

        $users = $userRepository->findByFilters($search, $role, $status);

        return $this->render('admin/user/index.html.twig', [
            'users' => $users,
            'counts' => $userRepository->getUserCounts(),
            'filters' => [
                'q' => $search,
                'role' => $role,
                'status' => $status,
            ],
        ]);
    }

    #[Route('/new', name: 'app_admin_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, UserManager $userManager): Response
    {
        $this->denyAccessUnlessGranted(User::ROLE_ADMIN);

        $user = new User();
        // FIXED: Changed UserFormType to AdminUserType
        $form = $this->createForm(AdminUserType::class, $user, [
            'is_edit' => false,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = (string) $form->get('plainPassword')->getData();
            /** @var string $role */
            // FIXED: Changed 'role' to 'primaryRole'
            $role = (string) $form->get('primaryRole')->getData();

            $userManager->createUser($user, $plainPassword, $role);

            $this->addFlash('success', \sprintf('Account for "%s" (%s) has been created.', $user->getName(), $user->getRoleLabel()));

            return $this->redirectToRoute('app_admin_user_index');
        }

        return $this->render('admin/user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_user_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(User $user, ProductRepository $productRepository): Response
    {
        $this->denyAccessUnlessGranted(User::ROLE_ADMIN);

        return $this->render('admin/user/show.html.twig', [
            'user' => $user,
            'userProducts' => $productRepository->findByFilters(owner: $user),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_user_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, UserManager $userManager): Response
    {
        $this->denyAccessUnlessGranted(User::ROLE_ADMIN);

        /** @var User $currentUser */
        $currentUser = $this->getUser();
        $isSelf = $currentUser->getId() === $user->getId();

        // FIXED: Changed UserFormType to AdminUserType
        $form = $this->createForm(AdminUserType::class, $user, [
            'is_edit' => true,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $role */
            // FIXED: Changed 'role' to 'primaryRole'
            $role = (string) $form->get('primaryRole')->getData();
            /** @var string|null $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            if ($isSelf && User::ROLE_ADMIN !== $role) {
                $this->addFlash('error', 'You cannot remove administrator privileges from your own active account.');

                return $this->redirectToRoute('app_admin_user_edit', ['id' => $user->getId()]);
            }

            if ($isSelf && User::STATUS_ACTIVE !== $user->getStatus()) {
                $user->setStatus(User::STATUS_ACTIVE);
                $userManager->updateUser($user, User::ROLE_ADMIN, $plainPassword);
                $this->addFlash('error', 'You cannot disable or archive your own currently signed-in account.');

                return $this->redirectToRoute('app_admin_user_edit', ['id' => $user->getId()]);
            }

            $userManager->updateUser($user, $role, $plainPassword);

            $this->addFlash('success', \sprintf('Account "%s" has been updated successfully.', $user->getName()));

            return $this->redirectToRoute('app_admin_user_index');
        }

        return $this->render('admin/user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/status', name: 'app_admin_user_status', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function changeStatus(Request $request, User $user, UserManager $userManager): Response
    {
        $this->denyAccessUnlessGranted(User::ROLE_ADMIN);

        if (!$this->isCsrfTokenValid('user_status_'.$user->getId(), $request->request->getString('_token'))) {
            $this->addFlash('error', 'Invalid CSRF security token.');

            return $this->redirectToRoute('app_admin_user_index');
        }

        /** @var User $currentUser */
        $currentUser = $this->getUser();
        if ($currentUser->getId() === $user->getId()) {
            $this->addFlash('error', 'You cannot disable or archive your own account.');

            return $this->redirectToRoute('app_admin_user_index');
        }

        $newStatus = $request->request->getString('status', User::STATUS_ACTIVE);
        if (!\in_array($newStatus, [User::STATUS_ACTIVE, User::STATUS_DISABLED, User::STATUS_ARCHIVED], true)) {
            $this->addFlash('error', 'Invalid account status specified.');

            return $this->redirectToRoute('app_admin_user_index');
        }

        $userManager->updateStatus($user, $newStatus);
        $this->addFlash('success', \sprintf('Account "%s" status changed to %s.', $user->getName(), ucfirst($newStatus)));

        return $this->redirectToRoute('app_admin_user_index');
    }

    #[Route('/{id}/delete', name: 'app_admin_user_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function delete(Request $request, User $user, UserManager $userManager): Response
    {
        $this->denyAccessUnlessGranted(User::ROLE_ADMIN);

        if (!$this->isCsrfTokenValid('delete_user_'.$user->getId(), $request->request->getString('_token'))) {
            $this->addFlash('error', 'Invalid CSRF security token. Account was not deleted.');

            return $this->redirectToRoute('app_admin_user_index');
        }

        /** @var User $currentUser */
        $currentUser = $this->getUser();
        if ($currentUser->getId() === $user->getId()) {
            $this->addFlash('error', 'You cannot delete your own currently logged-in account.');

            return $this->redirectToRoute('app_admin_user_index');
        }

        $deletedName = $user->getName();
        $userManager->deleteUser($user);

        $this->addFlash('success', \sprintf('User account "%s" has been permanently deleted.', $deletedName));

        return $this->redirectToRoute('app_admin_user_index');
    }
}
