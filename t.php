<?php

class A{
    function __toString()
    {
        return 'B';
    }
}

$b = new A();

echo $b;