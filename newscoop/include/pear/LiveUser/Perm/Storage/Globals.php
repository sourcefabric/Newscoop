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
 * @author  Pierre-Alain Joye <pajoye@php.net>
 * @author  Bjoern Kraus <krausbn@php.net>
 * @copyright 2002-2006 Markus Wolff
 * @license http://www.gnu.org/licenses/lgpl.txt
 * @version CVS: $Id: Globals.php,v 1.20 2006/02/27 18:05:28 lsmith Exp $
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

$GLOBALS['_LiveUser']['perm']['tables'] = array(
    'perm_users' => array(
        'fields' => array(
            'perm_user_id' => 'seq',
            'auth_user_id' => 'auth_id',
            'auth_container_name' => 'auth_id',
            'perm_type' => false,
         ),
        'joins' => array(
            'userrights' => 'perm_user_id',
            'groupusers' => 'perm_user_id',
            'area_admin_areas' => 'perm_user_id',
        ),
    ),
    'userrights' => array(
        'fields' => array(
            'perm_user_id' => 'id',
            'right_id' => 'id',
            'right_level' => false,
        ),
        'joins' => array(
            'perm_users' => 'perm_user_id',
            'rights' => 'right_id',
        ),
    ),
    'rights' => array(
        'fields' => array(
            'right_id' => 'seq',
            'area_id' => 'define_name',
            'right_define_name' => 'define_name',
            'has_implied' => null,
        ),
        'joins' => array(
            'areas' => 'area_id',
            'userrights' => 'right_id',
            'grouprights' => 'right_id',
            'right_implied' => array(
                'right_id' => 'right_id',
            ),
            'translations' => array(
                'right_id' => 'section_id',
                LIVEUSER_SECTION_RIGHT => 'section_type',
            ),
        ),
    ),
    'right_implied' => array(
        'fields' => array(
            'right_id' => 'id',
            'implied_right_id' => 'id',
        ),
        'joins' => array(
            'rights' => array(
                'right_id' => 'right_id',
                'implied_right_id' => 'right_id',
            ),
        ),
    ),
    'translations' => array(
        'fields' => array(
            'translation_id' => 'seq',
            'section_id' => 'translation',
            'section_type' => 'translation',
            'language_id' => 'translation',
            'name' => false,
            'description' => null,
        ),
        'joins' => array(
            'rights' => array(
                'section_id' => 'right_id',
                'section_type' => LIVEUSER_SECTION_RIGHT,
            ),
            'areas' => array(
                'section_id' => 'area_id',
                'section_type' => LIVEUSER_SECTION_AREA,
            ),
            'applications' => array(
                 'section_id' => 'application_id',
                 'section_type' => LIVEUSER_SECTION_APPLICATION,
            ),
            'groups' => array(
                'section_id' => 'group_id',
                'section_type' => LIVEUSER_SECTION_GROUP,
            ),
        ),
    ),
    'areas' => array(
        'fields' => array(
            'area_id' => 'seq',
            'application_id' => 'define_name',
            'area_define_name' => 'define_name',
        ),
        'joins' => array(
            'rights' => 'area_id',
            'applications' => 'application_id',
            'translations' => array(
                'area_id' => 'section_id',
                LIVEUSER_SECTION_AREA => 'section_type',
            ),
            'area_admin_areas' => 'area_id',
        ),
    ),
    'area_admin_areas' => array(
        'fields' => array(
            'area_id' => 'id',
            'perm_user_id' => 'id',
        ),
        'joins' => array(
            'perm_users' => 'perm_user_id',
            'areas' => 'area_id',
        )
    ),
    'applications' => array(
        'fields' => array(
            'application_id' => 'seq',
            'application_define_name' => 'define_name',
        ),
        'joins' => array(
            'areas' => 'application_id',
            'translations' => array(
                'application_id' => 'section_id',
                LIVEUSER_SECTION_APPLICATION => 'section_type',
            ),
        ),
    ),
    'groups' => array(
        'fields' => array(
            'group_id' => 'seq',
            'group_type' => false,
            'group_define_name' => 'define_name',
        ),
        'joins' => array(
            'groupusers' => 'group_id',
            'grouprights' => 'group_id',
            'group_subgroups' => 'group_id',
            'translations' => array(
                'group_id' => 'section_id',
                LIVEUSER_SECTION_GROUP => 'section_type',
            ),
        ),
    ),
    'groupusers' => array(
        'fields' => array(
            'perm_user_id' => 'id',
            'group_id' => 'id',
        ),
        'joins' => array(
            'groups' => 'group_id',
            'perm_users' => 'perm_user_id',
            'grouprights' => 'group_id',
        ),
    ),
    'grouprights' => array(
        'fields' => array(
            'group_id' => 'id',
            'right_id' => 'id',
            'right_level' => false,
        ),
        'joins' => array(
            'rights' => 'right_id',
            'groups' => 'group_id',
            'groupusers' => 'group_id',
        ),
    ),
    'group_subgroups' => array(
        'fields' => array(
            'group_id' => 'id',
            'subgroup_id' => 'id',
        ),
        'joins' => array(
            'groups' => 'group_id',
        ),
    ),
);

$GLOBALS['_LiveUser']['perm']['fields'] = array(
    'perm_user_id' => 'integer',
    'auth_user_id' => 'text',
    'auth_container_name' => 'text',
    'perm_type' => 'integer',
    'right_id' => 'integer',
    'right_level' => 'integer',
    'area_id' => 'integer',
    'application_id' => 'integer',
    'right_define_name' => 'text',
    'area_define_name' => 'text',
    'application_define_name' => 'text',
    'translation_id' => 'integer',
    'section_id' => 'integer',
    'section_type' => 'integer',
    'name' => 'text',
    'description' => 'text',
    'language_id' => 'text',
    'group_id' => 'integer',
    'group_type' => 'integer',
    'group_define_name' => 'text',
    'has_implied' => 'boolean',
    'implied_right_id' => 'integer',
    'subgroup_id' => 'integer'
);

$GLOBALS['_LiveUser']['perm']['alias'] = array(
    'perm_user_id' => 'perm_user_id',
    'auth_user_id' => 'auth_user_id',
    'auth_container_name' => 'auth_container_name',
    'perm_type' => 'perm_type',
    'right_id' => 'right_id',
    'right_level' => 'right_level',
    'area_id' => 'area_id',
    'application_id' => 'application_id',
    'right_define_name' => 'right_define_name',
    'area_define_name' => 'area_define_name',
    'application_define_name' => 'application_define_name',
    'translation_id' => 'translation_id',
    'section_id' => 'section_id',
    'section_type' => 'section_type',
    'name' => 'name',
    'description' => 'description',
    'language_id' => 'language_id',
    'group_id' => 'group_id',
    'group_type' => 'group_type',
    'group_define_name' => 'group_define_name',
    'has_implied' => 'has_implied',
    'implied_right_id' => 'implied_right_id',
    'subgroup_id' => 'subgroup_id',
    'perm_users' => 'perm_users',
    'userrights' => 'userrights',
    'applications' => 'applications',
    'areas' => 'areas',
    'area_admin_areas' => 'area_admin_areas',
    'rights' => 'rights',
    'groups' => 'groups',
    'groupusers' => 'groupusers',
    'grouprights' => 'grouprights',
    'right_implied' => 'right_implied',
    'group_subgroups' => 'group_subgroups',
    'translations' => 'translations',
);

?>