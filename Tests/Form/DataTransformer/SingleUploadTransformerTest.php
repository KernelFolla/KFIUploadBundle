<?php

namespace KFI\UploadBundle\Tests\Form\DataTransformer;

use KFI\UploadBundle\Entity\Upload;
use KFI\UploadBundle\Form\DataTransformer\SingleUploadTransformer;

class SingleUploadTransformerTest extends \PHPUnit_Framework_TestCase
{
    public function testTransform()
    {
        $repo = $this->getMock('Doctrine\Common\Persistence\ObjectRepository');
        $dt   = new SingleUploadTransformer($repo);
        $this->assertEmpty($dt->transform(array()), 'failed to check to empty transform');

        $upload = new Upload();
        $ret = $dt->transform($upload);
        $this->assertEquals($upload, array_pop($ret),'failed to check upload transform');
    }

    public function testReverseTransform()
    {
        $repo = $this->getMock('Doctrine\Common\Persistence\ObjectRepository');
        $dt   = new SingleUploadTransformer($repo);

        $this->assertEmpty($dt->reverseTransform(array()), 'failed to check to empty transform');

        $upload = new Upload();

        $repo->expects($this->once())
            ->method('find')
            ->with($this->equalTo('123'))
            ->will($this->returnValue($upload));
        $ret  = $dt->reverseTransform(
            array(
                0 => array(
                    'id'     => '123',
                    'title'  => 'test',
                    'url'    => '/image/2013/03/05/test.jpg',
                    'remote' => '',
                    'type'   => 'image'
                )
            )
        );
        $this->assertEquals($ret,$upload,'failed to reverse single upload');
    }
}