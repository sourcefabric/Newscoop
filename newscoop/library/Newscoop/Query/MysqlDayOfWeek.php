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
 * DayOfWeek ::= "DAYOFWEEK" "(" ArithmeticPrimary ")"
 */
class MysqlDayOfWeek extends FunctionNode
{
    public $dateExpression = null;

    public function parse(Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->dateExpression = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    public function getSql(SqlWalker $sqlWalker)
    {
        return 'DAYOFWEEK(' . $this->dateExpression->dispatch($sqlWalker) . ')';
    }
}