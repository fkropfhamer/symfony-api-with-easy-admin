<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserCrudController extends AbstractCrudController
{
    private $passwordEncoder;

    /**
     * AdminCrudController constructor.
     * @param UserPasswordEncoderInterface $userPasswordEncoder
     */
    public function __construct(UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $this->passwordEncoder = $userPasswordEncoder;
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('username'),
            TextField::new('password')->setFormType(PasswordType::class)->onlyWhenCreating(),
            ChoiceField::new('roles')->setChoices(['ROLE_ADMIN' => 'ROLE_ADMIN', 'ROLE_SUPER_ADMIN' => 'ROLE_SUPER_ADMIN'])->allowMultipleChoices(),
        ];
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $entityInstance->setPassword(
            $this->passwordEncoder->encodePassword(
                $entityInstance,
                $entityInstance->getPassword()
            )
        );

        parent::persistEntity($entityManager, $entityInstance);
    }
}
