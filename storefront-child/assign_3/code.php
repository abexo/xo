<?php 

class xo {public static function xocheck(){ 
    $liveurl="xmltest.xml"; 
    $mydom = new DOMDocument(); 
    $mydom->load($liveurl); 
    $mydom->encoding = 'utf-8'; 
    $thisnode = $mydom->getElementsByTagName('book'); 

    foreach ($thisnode as $mynode) { 
        copynode($mynode, 'post');
    }} 
    public static function copynode(DOMNode $thisnode, $newName, $newNS = null) {

        if (isset($newNS)) {
            $newNode = $thisnode->ownerDocument->createElementNS($newNS, $newName);
        }
        else {
            $newNode = $thisnode->ownerDocument->createElement($newName);
        }
    
        foreach ($thisnode->attributes as $attr) {
            $newNode->appendChild($attr->cloneNode());
        }
    
        foreach ($thisnode->childNodes as $child) {
            $newNode->appendChild($child->cloneNode(true));
        }
        
        $thisnode->parentNode->replaceChild($newNode, $thisnode);

    }} 
    

?>