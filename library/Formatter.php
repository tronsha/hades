<?php

/*
 * Cerberus IRCBot
 * Copyright (C) 2008 - 2017 Stefan Hüsges
 *
 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation; either version 3 of the License, or (at your option)
 * any later version.
 *
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 * or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License
 * for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, see <http://www.gnu.org/licenses/>.
 */

namespace Hades;

use Cerberus\Formatter\FormatterHtml;

/**
 * Class Fomatter
 * @package Hades
 * @author Stefan Hüsges
 * @link https://github.com/tronsha/hades Project on GitHub
 * @link https://github.com/tronsha/cerberus Cerberus Project on GitHub
 * @license http://www.gnu.org/licenses/gpl-3.0 GNU General Public License
 */
class Formatter extends FormatterHtml
{
    /**
     * @param string $text
     * @return string
     */
    public function irc2html($text)
    {
        if (mb_check_encoding($text, 'UTF-8') === false) {
            $text = utf8_encode($text);
        }
        $text = htmlentities($text);
        $text = $this->underline($text);
        $text = $this->bold($text);
        $text = $this->color($text);
        return $text;
    }
}
