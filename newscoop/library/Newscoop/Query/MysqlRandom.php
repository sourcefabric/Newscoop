<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Query;

use \Doctrine\ORM\Query\AST\Functions\FunctionNode,
    \Doctrine\ORM\Query\Parser,
    \Doctrine\ORM\Query\Lexer,
    \Doctrine\ORM\Query\SqlWalker;

/**
 * "RAND" "(" ")"
 */
class MysqlRandom extends FunctionNode
{
    /**
     * Parser function
     */
    public function parse(Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    /**
     * Get sql
     */
    public function getSql(SqlWalker $sqlWalker)
    {
        return 'RAND()';
    }
}
