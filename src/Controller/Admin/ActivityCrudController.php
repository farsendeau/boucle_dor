<?php

namespace App\Controller\Admin;

use App\Entity\Activity;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Constraints\NotNull;
use Vich\UploaderBundle\Form\Type\VichImageType;

#[IsGranted('ROLE_ADMIN')]
class ActivityCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Activity::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            FormField::addTab('Infos générales'),
            TextField::new('name', 'Nom')
                ->setFormTypeOption('translation_domain', false),
            TextField::new('summary', 'Résumé')
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
                ->onlyOnForms(),
            
            FormField::addTab('Traductions Anglaises'),
            TextField::new('summaryEn', 'Résumé EN')
                ->setFormTypeOption('translation_domain', false)
                ->onlyOnForms()
        ];
    }
}