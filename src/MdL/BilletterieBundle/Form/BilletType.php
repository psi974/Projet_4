<?php

namespace MdL\BilletterieBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BilletType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom',            TextType::class, array(
                'attr' => ['placeholder' => 'Nom du titulaire',
                           'class' => 'nom']))
            ->add('prenom',         TextType::class, array(
                'attr' =>['placeholder' => 'Prénom du titulaire',
                          'class' => 'prenom']))
            ->add('pays',           CountryType::class, array(
                'preferred_choices' => array('FR')))
            ->add('dtNaissance',    BirthdayType::class, array(
                'label' => 'Date de naissance',
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'dd-MM-yyyy',
                'attr' =>['class' => 'dtNaiss',
                    'readonly' => 'true']))
            ->add('tarifReduit',    CheckboxType::class, array(
                'label' => 'Tarif réduit',
                'required' => false,
                'attr' =>['class' => 'reduction']));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'MdL\BilletterieBundle\Entity\Billet'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'mdl_billetteriebundle_billet';
    }


}
