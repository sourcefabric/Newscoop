<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * A framework for authentication and authorization in PHP applications
 *
 * LiveUser is an authentication/permission framework designed
 * to be flexible and easily extendable.
 *
 * Since it is impossible to have a
 * "one size fits all" it takes a container
 * approach which should enable it to
 * be versatile enough to meet most needs.
 *
 * PHP version 4 and 5
 *
 * LICENSE: This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston,
 * MA  02111-1307  USA
 *
 *
 * @category authentication
 * @package LiveUser
 * @author  Markus Wolff <wolff@21st.de>
 * @author  Helgi Þormar Þorbjörnsson <dufuz@php.net>
 * @author  Lukas Smith <smith@pooteeweet.org>
 * @author  Arnaud Limbourg <arnaud@php.net>
 * @author  Pierre-Alain Joye  <pajoye@php.net>
 * @author  Bjoern Kraus <krausbn@php.net>
 * @copyright 2002-2006 Markus Wolff
 * @license http://www.gnu.org/licenses/lgpl.txt
 * @version CVS: $Id: Globals.php,v 1.7 2006/02/27 18:05:28 lsmith Exp $
 * @link http://pear.php.net/LiveUser
 */


/**
 * This file holds all our default table/fields name/types/relations,
 * if they should be checked and more which are needed by both
 * LiveUser and LiveUser_Admin
 *
 * You can add to those table or modify options via our table/field
 * options in the config.
 */


$GLOBALS['_LiveUser']['auth']['tables'] = array(
    'users' => array(
        'fields' => array(
            'auth_user_id' => 'seq',
            'handle' => 'unique',
            'passwd' => true,
        ),
    ),
);

$GLOBALS['_LiveUser']['auth']['fields'] = array(
    'auth_user_id' => 'text',
    'handle' => 'text',
    'passwd' => 'text',
);

$GLOBALS['_LiveUser']['auth']['alias'] = array(
    'auth_user_id' => 'auth_user_id',
    'handle' => 'handle',
    'passwd' => 'passwd',
    'users' => 'users',
);

?>