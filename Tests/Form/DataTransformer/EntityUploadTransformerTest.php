<?php

namespace KFI\UploadBundle\Tests\Form\DataTransformer;

use KFI\UploadBundle\Form\DataTransformer\EntityUploadTransformer;

class EntityUploadTransformerTest extends \PHPUnit_Framework_TestCase{
    public function testTransform(){
        $repo = $this->getMock('Doctrine\Common\Persistence\ObjectRepository');
        $dt = new EntityUploadTransformer($repo);
        $this->assertEmpty($dt->transform(array()), 'failed to check to empty transform');
    }

    public function testReverseTransform(){
        $repo = $this->getMock('Doctrine\Common\Persistence\ObjectRepository');
        $dt = new EntityUploadTransformer($repo);
        $this->assertEmpty($dt->reverseTransform(array()), 'failed to check to empty transform');
    }
}