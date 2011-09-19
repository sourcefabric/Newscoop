<?php
final class MetaDebateDays
{
    public $time;
    public $answers;
    public $count;
    public function __construct($date)
    {
        foreach ($date as $key => $value)
        {
            switch (true)
            {
                case is_array($value) :
                    $this->answers[] = $value;
                    break;
                case $key == 'time' :
                    $this->time = $value;
                    break;
                case $key == 'total_count' :
                    $this->count = $value;
                    break;
            }
        }
        foreach ($this->answers as &$answer) {
            $answer['percentage'] = $answer['value']*100/$this->count;
        }
    }
}