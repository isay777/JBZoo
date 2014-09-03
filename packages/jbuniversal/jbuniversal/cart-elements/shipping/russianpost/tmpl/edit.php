<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Alexander Oganov <t_tapak@yahoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

if (!empty($fields)) : ?>

    <?php foreach ($fields as $key => $field) : ?>
        <dt>
            <?php echo JText::_('JBZOO_ORDER_SHIPPING_RUSSIANPOST_' . strtoupper($key)); ?>
        </dt>

        <dd>
            <?php echo $field; ?>
        </dd>

    <?php endforeach; ?>

<?php endif;