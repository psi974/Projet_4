<?php

namespace MdL\BilletterieBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommandeType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dtVisite',       DateType::class, array(
                'label' => 'Date de la visite',
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'dd-MM-yyyy',
                'attr' =>['class' => 'js-datepicker',
                          'readonly' => 'true']))
            ->add('billetJour',       CheckboxType::class, array(
                'label' => 'Journée complète',
                'required' => false))
            ->add('billets',        CollectionType::class, array(
                'entry_type'   => BilletType::class,
                'allow_add'    => true,
                'allow_delete' => true,
                'attr' => ['class' => 'billet']))
            ->add('Commander',      SubmitType::class, array(
                'attr' => ['class' => 'btn btn-primary commander']));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'MdL\BilletterieBundle\Entity\Commande'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'mdl_billetteriebundle_commande';
    }
}
