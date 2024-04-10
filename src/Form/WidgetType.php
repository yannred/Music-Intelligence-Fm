<?php

namespace App\Form;

use App\Entity\Widget;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Validator as ValidatorCustom;

class WidgetType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options): void
  {
    $builder
      ->setAction($options['action'])
      ->add('wording', null, ['label' => 'Title', 'required' => true])
      ->add('comment', null, ['label' => 'Comments', 'required' => false])


      ->add('typeWidget', ChoiceType::class, [
        'choices'  => [ '' => 0, ...Widget::TYPES],
        'label' => 'Choice the statistic type',
        'required' => true,
      ])
      ->add('subTypeWidget', ChoiceType::class, [
        'choices'  => [ '' => 0, ...Widget::SUB_TYPES],
        'label' => 'Choice the chart render type',
        'required' => true,
      ])


      ->add('dateType', ChoiceType::class, [
        'choices'  => Widget::DATE_TYPES,
        'label' => 'Choice the date type',
        'required' => true,
        'attr' => ['onchange' => 'onChangeDateType(this)']
      ])
      ->add('dateFrom', null, [
        'label' => 'Date from',
        'required' => false,
        'attr'   =>  ['class'   => 'period-custom', 'style' =>  'display: none;']
      ])
      ->add('dateTo', null, [
        'label' => 'Date to',
        'required' => false,
        'attr'   =>  ['class'   => 'period-custom', 'style' =>  'display: none;']
      ])


      ->add('fontColor', ColorType::class, [
        'label' => 'Font Color',
        'required' => true
      ])
      ->add('backgroundColor', ColorType::class, [
        'label' => 'Background color',
        'required' => true
      ])


      ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event): void {
        /** @var Widget $widget */
        $widget = $event->getData();
        /** @var Form $form */
        $form = $event->getForm();
        if ($widget['dateType'] == Widget::DATE_TYPE__CUSTOM){
          $form->remove('dateFrom');
          $form->remove('dateTo');
          $form
            ->add('dateFrom', null, [
              'label' => 'Date from',
              'required' => false,
              'attr'   =>  ['class'   => 'period-custom', 'style' => ''],
              'constraints' => [
                new ValidatorCustom\CustomDateRangeForWidgetForm(
                  ValidatorCustom\CustomDateRangeForWidgetForm::FROM_FIELD,
                  $widget['dateFrom'],
                  $widget['dateTo']
                ),
              ]
            ])
            ->add('dateTo', null, [
              'label' => 'Date to',
              'required' => false,
              'attr'   =>  ['class'   => 'period-custom', 'style' => ''],
              'constraints' => [
                new ValidatorCustom\CustomDateRangeForWidgetForm(
                  ValidatorCustom\CustomDateRangeForWidgetForm::TO_FIELD,
                  $widget['dateFrom'],
                  $widget['dateTo']
                ),
              ]
            ]);
        }
      })



      /** Make visible the dateFrom and dateTo fields if the dateType is "custom" */
//      ->addEventListener(FormEvents::SUBMIT, function (FormEvent $event): void {
//        /** @var Widget $widget */
//        $widget = $event->getData();
//        /** @var Form $form */
//        $form = $event->getForm();
//
//        if ($widget != null && $widget->getDateType() == Widget::DATE_TYPE__CUSTOM) {
//          $form->remove('dateFrom');
//          $form->remove('dateTo');
//          $form
//            ->add('dateFrom', null, [
//              'label' => 'Date from',
//              'required' => false,
//              'attr'   =>  ['class'   => 'period-custom', 'style' => '']
//            ])
//            ->add('dateTo', null, [
//              'label' => 'Date to',
//              'required' => false,
//              'attr'   =>  ['class'   => 'period-custom', 'style' => '']
//            ]);
//        }
//      })
      /** Set the default value for fontColor and backgroundColor */
      /** Make visible the dateFrom and dateTo fields if the dateType is "custom" */
      ->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event): void {
        /** @var Widget $widget */
        $widget = $event->getData();
        /** @var Form $form */
        $form = $event->getForm();

        if ($widget != null && $widget->getDateType() == Widget::DATE_TYPE__CUSTOM) {
          $form->remove('dateFrom');
          $form->remove('dateTo');
          $form
            ->add('dateFrom', null, [
              'label' => 'Date from',
              'required' => false,
              'attr'   =>  ['class'   => 'period-custom', 'style' => '']
            ])
            ->add('dateTo', null, [
              'label' => 'Date to',
              'required' => false,
              'attr'   =>  ['class'   => 'period-custom', 'style' => '']
            ]);
        }

        if ($widget == null || $widget->getFontColor() == '' || $widget->getFontColor() == null) {
          $form->remove('fontColor');
          $form->remove('fontColor');
          $form
            ->add('fontColor', ColorType::class, [
              'label' => 'Font Color',
              'data' => Widget::WIDGET_DEFAULT_FONT_COLOR,
              'required' => true
            ]);
        }
        if ($widget == null || $widget->getBackgroundColor() == '' || $widget->getBackgroundColor() == null) {
          $form->remove('backgroundColor');
          $form->remove('backgroundColor');
          $form
            ->add('backgroundColor', ColorType::class, [
              'label' => 'Background color',
              'data' => Widget::WIDGET_DEFAULT_BACKGROUND_COLOR,
              'required' => true
            ]);
        }
      })
    ;
  }

  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver->setDefaults([
      'data_class' => Widget::class,
    ]);
  }


  private function deduceIncompleteDate (){

  }
}
