<?php

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
        $text = htmlentities($text);
        $text = $this->underline($text);
        $text = $this->bold($text);
        $text = $this->color($text);

        return $text;
    }

}
