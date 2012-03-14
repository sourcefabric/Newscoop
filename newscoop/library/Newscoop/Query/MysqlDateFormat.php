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
 * Date_Format ::= "DATE_FORMAT" "(" ArithmeticPrimary "," StringPrimary ")"
 */
class MysqlDateFormat extends FunctionNode
{
    public $dateExpression = null;
    public $formatExpression = null;

    public function parse(Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->dateExpression = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_COMMA);
        $this->formatExpression = $parser->StringPrimary();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    public function getSql(SqlWalker $sqlWalker)
    {
        //return 'DATE_FORMAT(' . $sqlWalker->walkArithmeticExpression($this->dateExpression) . ',' . $sqlWalker->walkStringPrimary($this->formatExpression) . ')';
        return 'DATE_FORMAT(' . $this->dateExpression->dispatch($sqlWalker) . ',' . $this->formatExpression->dispatch($sqlWalker) . ')';
    }
}