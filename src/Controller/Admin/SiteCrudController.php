<?php

namespace App\Controller\Admin;

use App\Entity\Site\Site;
use App\Form\LinkFormType;
use App\Repository\Site\SiteRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TelephoneField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Vich\UploaderBundle\Form\Type\VichImageType;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;

#[IsGranted('ROLE_ADMIN')]
class SiteCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly SiteRepository $siteRepository,
        private readonly AdminUrlGenerator $adminUrlGenerator,
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return Site::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Site')
            ->setEntityLabelInPlural('Sites')
            ->setPageTitle(Crud::PAGE_INDEX, 'Configuration du site')
            ->setPageTitle(Crud::PAGE_NEW, 'Créer la configuration du site')
            ->setPageTitle(Crud::PAGE_EDIT, 'Modifier la configuration du site')
            ->setPageTitle(Crud::PAGE_DETAIL, 'Détails du site');
    }

    public function configureFields(string $pageName): iterable
    {
        $fields = [
            TextField::new('name', 'Nom du site')
                ->setFormTypeOption('translation_domain', false),
            TextField::new('address', 'Adresse')
                ->setFormTypeOption('translation_domain', false),
            TextField::new('codePostal', 'Code Postal')
                ->setFormTypeOption('translation_domain', false),
            TextField::new('city', 'Ville')
                ->setFormTypeOption('translation_domain', false),
            TextField::new('country', 'Pays')
                ->setFormTypeOption('translation_domain', false),
            TelephoneField::new('tel', 'Téléphone')
                ->setFormTypeOption('translation_domain', false),
            EmailField::new('mail', 'Email')
                ->setFormTypeOption('translation_domain', false),
            UrlField::new('mapUrl', 'Url Google Map/OpenStreetMap')
                ->setFormTypeOption('translation_domain', false),
            TextField::new('mapImage', 'Image de la carte')
                ->setFormType(VichImageType::class)
                ->setFormTypeOptions([
                    'required' => false,
                    'allow_delete' => true,
                    'download_uri' => false,
                    'image_uri' => true,
                    'asset_helper' => true,
                ])
                ->onlyOnForms()
        ];

        $fields[] = CollectionField::new('links', 'Liens réseaux sociaux')
            ->setEntryType(LinkFormType::class)
            ->setFormTypeOptions([
                'by_reference' => false,
                'translation_domain' => false,
            ])
            ->allowAdd()
            ->allowDelete();


        return $fields;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::INDEX, Action::DELETE)
            ->update(Crud::PAGE_NEW, Action::SAVE_AND_RETURN, fn (Action $action) => $action->setLabel('Enregistrer'))
            ->update(Crud::PAGE_EDIT, Action::SAVE_AND_CONTINUE, fn (Action $action) => $action->setLabel('Enregistrer  et continuer'))
            ->remove(Crud::PAGE_NEW, Action::SAVE_AND_ADD_ANOTHER)
            ->remove(Crud::PAGE_EDIT, Action::SAVE_AND_CONTINUE);
    }


    public function index(AdminContext $context): Response
    {
        $site = $this->siteRepository->findOneBy([]);

        $adminUrlGenerator = $this->adminUrlGenerator
            ->setController(self::class);

        if ($site) {
            $adminUrlGenerator
              ->setAction(Action::EDIT)
              ->setEntityId($site->getId());
        } else {
            $adminUrlGenerator
                ->setAction(Action::NEW);
        }

        return $this->redirect($adminUrlGenerator->generateUrl());
    }
}
