<?php

/*
 * This file is part of the EasyAdminBundle.
 *
 * (c) Javier Eguiluz <javier.eguiluz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JavierEguiluz\Bundle\EasyAdminBundle;

/**
 * Class EasyAdminEvents
 */
final class EasyAdminEvents
{
    /**
     * The POST_ENTITY_FORM_CREATE event occurs after Form creation in "edit" and "new" context
     */
    const POST_ENTITY_FORM_CREATE = 'easyadmin.post_entity_form_create';

    /**
     * The POST_DELETE_FORM_CREATE event occurs after Form creation in "delete" context
     */
    const POST_DELETE_FORM_CREATE = 'easyadmin.post_delete_form_create';

}

