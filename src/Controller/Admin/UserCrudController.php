<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('User')
            ->setEntityLabelInPlural('Users')
            ->setDateFormat('...')
            // ...
        ;

    }


    public function configureFields(string $pageName): iterable
    {
    return [
        IdField::new('id')
            ->hideOnForm(),
        TextField::new('fullName'),
        TextField::new('pseudo')
        ->hideOnIndex(),
        TextField::new('email')
            ->hideOnForm(),
        ArrayField::new('roles')
            ->hideOnIndex(),
        DateTimeField::new('createdAt')
            ->hideOnForm()
            
    ];
}
   
}
