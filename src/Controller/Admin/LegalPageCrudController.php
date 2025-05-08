<?php

namespace App\Controller\Admin;

use App\Entity\Site\LegalPage;
use App\Repository\Site\LegalPageRepository;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Cache\CacheInterface;

#[IsGranted('ROLE_ADMIN')]
class LegalPageCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly LegalPageRepository $legalContentRepository,
        private readonly AdminUrlGenerator   $adminUrlGenerator,
        private readonly CacheInterface      $cache,
    ){
    }

    public static function getEntityFqcn(): string
    {
        return LegalPage::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Pages légales')
            ->setPageTitle(Crud::PAGE_NEW, 'Créer les pages légales')
            ->setPageTitle(Crud::PAGE_EDIT, 'Modifier les pages légales')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            FormField::addTab('Page À propos'),
            TextField::new('aboutTitleFr', 'Titre fr'),
            TextEditorField::new('aboutContentFr', 'Contenu fr'),
            TextField::new('aboutTitleEn', 'Titre en'),
            TextEditorField::new('aboutContentEn', 'Contenu en'),

            FormField::addTab('Page Conditions générales de vente'),
            TextField::new('gcsTitleFr', 'Titre fr'),
            TextEditorField::new('gcsContentFr', 'Contenu fr'),
            TextField::new('gcsTitleEn', 'Titre en'),
            TextEditorField::new('gcsContentEn', 'Contenu en'),

            FormField::addTab('Page Mentions légales'),
            TextField::new('legalNoticeTitleFr', 'Titre fr'),
            TextEditorField::new('legalNoticeContentFr', 'Contenu fr'),
            TextField::new('legalNoticeTitleEn', 'Titre en'),
            TextEditorField::new('legalNoticeContentEn', 'Contenu en'),

            FormField::addTab('Page Données personnelles'),
            TextField::new('personalDataTitleFr', 'Titre fr'),
            TextEditorField::new('personalDataContentFr', 'Contenu fr'),
            TextField::new('personalDataTitleEn', 'Titre en'),
            TextEditorField::new('personalDataContentEn', 'Contenu en'),
        ];
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
        $legalPages = $this->legalContentRepository->findOneBy([]);

        $adminUrlGenerator = $this->adminUrlGenerator
            ->setController(self::class);

        if ($legalPages) {
            $adminUrlGenerator
                ->setAction(Action::EDIT)
                ->setEntityId($legalPages->getId());
        } else {
            $adminUrlGenerator
                ->setAction(Action::NEW);
        }

        return $this->redirect($adminUrlGenerator->generateUrl());
    }

    /**
     * @throws InvalidArgumentException
     */
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        parent::persistEntity($entityManager, $entityInstance);
        $this->invalidCache();
    }

    /**
     * @throws InvalidArgumentException
     */
    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        parent::updateEntity($entityManager, $entityInstance);
        $this->invalidCache();
    }

    /**
     * @throws InvalidArgumentException
     */
    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        parent::deleteEntity($entityManager, $entityInstance);
        $this->invalidCache();
    }

    /**
     * @throws InvalidArgumentException
     */
    private function invalidCache(): void
    {
        $this->cache->delete('legal_page');
    }
}
