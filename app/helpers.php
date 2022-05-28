<?php

function getSystemModelsSymbol(){
    return array_map(function ($item){
        return  substr($item, 11);
    },array_keys(config('enums.models')));
}
