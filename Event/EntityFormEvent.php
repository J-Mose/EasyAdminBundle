<?php

/*
 * This file is part of the EasyAdminBundle.
 *
 * (c) Javier Eguiluz <javier.eguiluz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JavierEguiluz\Bundle\EasyAdminBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Form\FormBuilder;

/**
 * Class EntityFormEvent
 */
class EntityFormEvent extends Event
{

    /**
     * @var mixed
     */
    private $entity;

    /**
     * @var FormBuilder
     */
    private $form;

    /**
     * @var string
     */
    private $view;

    /**
     * @param FormBuilder $form
     * @param mixed $entity
     * @param string $view
     */
    public function __construct(FormBuilder $form, $entity, $view)
    {
        $this->form = $form;
        $this->entity = $entity;
        $this->view = $view;
    }

    /**
     * @return mixed
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @return FormBuilder
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @return string
     */
    public function getView()
    {
        return $this->view;
    }
}
