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
use Symfony\Component\Form\FormBuilder ;

/**
 * Class FormEvent
 */
class DeleteFormEvent extends Event
{

    /**
     * @var mixed
     */
    private $entityId;

    /**
     * @var string
     */
    private $entityName;

    /**
     * @var \Symfony\Component\Form\FormBuilder
     */
    private $form;

    /**
     * @param FormBuilder  $form
     * @param $entityName
     * @param $entityId
     */
    public function __construct(FormBuilder  $form, $entityName, $entityId)
    {
        $this->form = $form;
        $this->entityName = $entityName;
        $this->entityId = $entityId;
    }

    /**
     * @return mixed
     */
    public function getEntityName()
    {
        return $this->entityName;
    }

    /**
     * @return mixed
     */
    public function getEntityId()
    {
        return $this->entityId;
    }

    /**
     * @return FormBuilder
     */
    public function getForm()
    {
        return $this->form;
    }

}