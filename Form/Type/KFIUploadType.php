<?php

namespace KFI\UploadBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Doctrine\ORM\EntityManager;

use KFI\UploadBundle\Form\DataTransformer;

class KFIUploadType extends AbstractType
{
    const TYPE_IMAGE = 'image';
    const TYPE_VIDEO = 'video';
    const TYPE_ALL = 'all';

    private static $EXTENSIONS = array(
        self::TYPE_IMAGE => '*.jpg;*.gif;*.png',
        self::TYPE_VIDEO => '*.flv;*.wmv;*.avi;*.mpg',
        self::TYPE_ALL   => "*"
    );
    private static $DESCRIPTIONS = array(
        self::TYPE_IMAGE => 'Image Files (.JPG, .GIF, .PNG)',
        self::TYPE_VIDEO => 'Video Files (.FLV, .WMV, .AVI, .MPG)',
        self::TYPE_ALL   => "*.*"
    );


    protected $entityManager;

    /**
     * @param EntityManager $entityManager
     * @param string        $repositoryName
     */
    public function __construct(EntityManager $entityManager, $repositoryName)
    {
        $this->entityManager = $entityManager;
        $this->repo          = $entityManager->getRepository($repositoryName);
    }

    /** {@inheritdoc} */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'compound'      => false,
                'mode'          => 'multi',
                'type'          => 'image',
                'sortable'      => true,
                'remote'        => false,
                'prototype'     => 'KFIUploadBundle:Upload',
                'add_to_editor' => false
            )
        );
    }

    /** {@inheritdoc} */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $repo = $this->entityManager->getRepository(
            $builder->getOption('prototype')
        );
        $isMulti = ($builder->getOption('mode') == 'multi');
        $transformer =  $isMulti ?
            new DataTransformer\EntityUploadTransformer($repo)
            : new DataTransformer\SingleUploadTransformer($repo);
        $builder->addViewTransformer($transformer);
    }

    /** {@inheritdoc} */
    public function getName()
    {
        return 'kfi_upload';
    }

    /** {@inheritdoc} */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars = array_replace(
            $view->vars,
            array(
                'add_to_editor' => $options['add_to_editor'],
                'js_data' => $this->getJSData(
                    $view->vars['name'],
                    $view->vars['full_name'],
                    $options
                )
            )
        );
    }

    protected function getJSData($name, $fieldName, array $options)
    {
        $mode     = $options['mode'];
        $type     = $options['type'];
        $remote   = $options['remote'];
        $sortable = $options['sortable'];

        $ret = array(
            'name'             => $name,
            'fieldName'        => $fieldName,
            'sessionID'        => session_id(),
            'fileExt'          => self::$EXTENSIONS[$type],
            'fileDesc'         => self::$DESCRIPTIONS[$type],
            'multi'            => ($mode == 'multi'),
            'sortable'         => $sortable,
            'simUploadLimit'   => 1,
            'onComplete'       => $mode . '_onComplete',
            'onCancel'         => $mode . '_onCancel',
            //'reload_callback'    => array($this->facade, 'reloadUploadForm'),
            'allow_remote_url' => $remote ? true : false
        );

        return $ret;
    }

    /**
     * {@inheritdoc}
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['multipart'] = true;
    }
}
