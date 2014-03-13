<?php
final class MetaDebateDays // extends MetaObject
{
    private $m_properties = array( 'time' => null, 'answers' => array(), 'count' => null );

    public function __construct($date = null)
    {
        if (isset($date)) {
            foreach ($date as $key => $value)
            {
                switch (true)
                {
                    case is_array($value) :
                        $this->m_properties['answers'][] = $value;
                        break;
                    case $key == 'time' :
                        $this->m_properties['time'] = $value;
                        break;
                    case $key == 'total_count' :
                        $this->m_properties['count'] = $value;
                        break;
                }
            }
        }
        foreach ($this->m_properties['answers'] as &$answer) {
            $answer['percentage'] = $answer['value']*100/$this->count;
        }
    }

    public function __get($name)
    {
        return isset($this->m_properties[$name]) ? $this->m_properties[$name] : null;
    }
}
