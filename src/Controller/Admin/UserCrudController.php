<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\ByteString;

/**
 * https://symfony.com/doc/current/security/passwords.html#hashing-the-password
 */
#[IsGranted('ROLE_ADMIN')]
class UserCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
    ){
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Utilisateur')
            ->setEntityLabelInPlural('Utilisateurs');
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('email'),
            TextField::new('password')->hideOnForm()->hideOnIndex(),
            BooleanField::new('isVerified', 'Vérifié')
                ->renderAsSwitch(false)
                ->setFormattedValue(function ($value) {
                    return $value ? 'Oui' : 'Non';
                })
                ->hideOnForm(),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        $resetPassword = Action::new('resetPassword', 'Créer MDP')
            ->linkToRoute('app_forgot_password_request', static function (User $entity) {
                return [
                    'email' => $entity->getEmail(),
                ];
            });

        $actions->add(Crud::PAGE_INDEX, $resetPassword);

        return $actions;
    }

    /**
     * Méthode appelée lors de la création d'un nouvel utilisateur
     */
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof User) {
            parent::persistEntity($entityManager, $entityInstance);
            return;
        }

        // Générer un mot de passe provisoire aléatoire (12 caractères)
        $temporaryPassword = ByteString::fromRandom(12)->toString();

        // Hacher le mot de passe
        $hashedPassword = $this->passwordHasher->hashPassword($entityInstance, $temporaryPassword);
        $entityInstance->setPassword($hashedPassword);

        $entityInstance->setRoles(['ROLE_ADMIN']);

        // Persister l'entité
        $entityManager->persist($entityInstance);
        $entityManager->flush();

        // Ajouter un message flash avec le mot de passe provisoire
        $this->addFlash('success', 'Utilisateur créé avec succès. Mot de passe provisoire : ' . $temporaryPassword);
    }
}
