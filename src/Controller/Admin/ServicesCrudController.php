<?php

namespace App\Controller\Admin;

use App\Entity\Services;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Constraints\NotNull;
use Vich\UploaderBundle\Form\Type\VichImageType;

#[IsGranted('ROLE_ADMIN')]
class ServicesCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Services::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name', 'Nom')
                ->setFormTypeOption('translation_domain', false),
            TextField::new('summary', 'Résumé')
                ->setFormTypeOption('translation_domain', false),
            TextareaField::new('description', 'Description')
                ->setFormTypeOption('translation_domain', false),
            TextField::new('image', 'Image')
                ->setFormType(VichImageType::class)
                ->setFormTypeOptions([
                    'required' => false,
                    'allow_delete' => true,
                    'download_uri' => false,
                    'image_uri' => true,
                    'asset_helper' => true,
                    'constraints' => [new NotNull(['groups' => ['create']])]
                ])
                ->onlyOnForms()
        ];
    }
}