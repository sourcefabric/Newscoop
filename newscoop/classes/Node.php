<?php

class Node {
    public $id;
    public $pid;
    public $data;

    private $children = array();

    public function __construct($id, $pid, $data) {
        $this->id = $id;
        $this->pid = $pid;
        $this->data = $data;
    }

    public function insertNode(Node $node) {
        if($node->pid == $this->id) {
            $this->children[] = $node;
            return true;
        }

        foreach($this->children as $child) {
            if($child->insertNode($node)) {
                return true;
            }
        }

        return false;
    }

    public function flatten() {
        $aggregate = array($this->data);

        foreach($this->children as $child) {
            foreach($child->flatten() as $flat) {
                $aggregate[] = $flat;
            }
        }

        return $aggregate;
    }
}
