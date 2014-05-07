<?php

namespace Newscoop\Form;
use Symfony\Component\HttpFoundation\Request;

interface FormServiceInterface
{
    public function config(array $parameters = array(), Request $request);
    public function getElement($elementName, array $options = array());
    public function getFormStart();
    public function getFormEnd();
    public function getFormObject();
    public function isValid();
}