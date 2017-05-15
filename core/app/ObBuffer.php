<?php

namespace Core\App;

class ObBuffer{

    private $bufferStack;

    private $buffer;
    private $level;
    private $on;

    public function __construct()
    {
        ob_start([$this, 'end']);
        $this->level = ob_get_level();
        $this->on = true;
        $this->bufferStack = [];
    }

    public function unbufferize(): string{

        $this->on = false;

        while(ob_get_level() > $this->level){
            $this->bufferStack[] = ob_get_clean();
        }

        $this->buffer = ob_get_clean();

        //while (ob_get_level()) ob_get_clean();

        $output = "\n<br/>\n<br/>/***********************************Debug****************************/\n<br/>\n<br/>{$this->buffer}";
        if(count($this->bufferStack) > 0){
            $output .= "\n\n<br/><br/>Personnal Debug : \n<br/>";
            foreach ($this->bufferStack as $buff){
                $output .= "{$buff}\n<br/>";
            }
        }

        return $output;
    }

    public function end(string $content){
        if($this->on)
            throw new \Exception('[ObBuffer::end] You can\'t destroy this Buffer ' . $content);

        return $content;
    }



}