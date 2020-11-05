<?php

class exclusive extends baseObject
{
    private $var1=1;
    protected $var2=2;
    private function incrementVar1(){
        $this->var1++;
        return __function__;
    }
    protected function decrementVar2(){
        $this->var2--;
        return __function__;
    }
}
