<?php
// src/Form/CsvDataFormType.php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class CsvDataFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('identifier', TextType::class, [
                'label' => 'Identifiant',
            ])
            ->add('newData', TextType::class, [
                'label' => 'Nouvelles données',
            ])
            ->add('save', SubmitType::class, ['label' => 'Envoyer']);
    }
}
