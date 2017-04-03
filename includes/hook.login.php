<?php
/******************************************************************************
 *
 * Subrion - open source content management system
 * Copyright (C) 2017 Intelliants, LLC <https://intelliants.com>
 *
 * This file is part of Subrion.
 *
 * Subrion is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Subrion is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Subrion. If not, see <http://www.gnu.org/licenses/>.
 *
 *
 * @link https://subrion.org/
 *
 ******************************************************************************/

if (iaView::REQUEST_JSON == $iaView->getRequestType()) {
    $clientId = $iaCore->get('vanilla_client_id');
    $secret = $iaCore->get('vanilla_secret_key');

    // init users class
    $iaUsers = $iaCore->factory('users');

    $vanillaUser = array();
    if (!empty($clientId) && !empty($secret)) {
        // include jsConnect library
        require_once IA_MODULES . 'vanillaforums' . IA_DS . 'includes' . IA_DS . 'functions.jsconnect.php';

        if (iaUsers::hasIdentity()) {
            // fill in the user information in a way that Vanilla can understand
            $vanillaUser['uniqueid'] = iaUsers::getIdentity()->id;
            $vanillaUser['name'] = iaUsers::getIdentity()->username;
            $vanillaUser['email'] = iaUsers::getIdentity()->email;

            if (iaUsers::getIdentity()->avatar) {
                $avatar = unserialize(iaUsers::getIdentity()->avatar);
                $vanillaUser['photourl'] = IA_CLEAR_URL . 'uploads/' . $avatar['path'];
            }
        } elseif (!iaUsers::hasIdentity()) {
            $this->factory('util');
            iaUtil::go_to(IA_URL . 'login/');
        }

        // generate the jsConnect string
        WriteJsConnect($vanillaUser, $_GET, $clientId, $secret, false);
        exit;
    }
}