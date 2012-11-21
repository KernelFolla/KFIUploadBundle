
Installation
------------
Checkout a copy of the code::

    git submodule add https://github.com/KernelFolla/KFIUploadBundle.git src/KFI/UploadBundle
    git submodule add https://github.com/KernelFolla/uploadify.git web/dist/uploadify/

Then register the bundle with your kernel::

    // in AppKernel::registerBundles()
    $bundles = array(
        // ...
        new KFI\UploadBundle\KFIUploadBundle().
        // ...
    );



add in app/config/routing.yml::

    kfi_upload:
        resource: "@KFIUploadBundle/Controller/"
        type:     annotation
        prefix:   /

