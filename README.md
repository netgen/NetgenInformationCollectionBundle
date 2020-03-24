<h1 align="center">
    <a href="https://www.netgenlabs.com/" target="_blank">
        <img src="https://github.com/netgen/NetgenInformationCollectionBundle/raw/1.x/bundle/Resources/public/admin/images/additional/ic-logo-color.png" />
    </a>
</h1>

<div align="center">
    
[![Build Status](https://img.shields.io/travis/netgen/NetgenInformationCollectionBundle.svg?style=flat-square)](https://travis-ci.com/netgen/NetgenInformationCollectionBundle)
[![Code Coverage](https://img.shields.io/codecov/c/github/netgen/NetgenInformationCollectionBundle.svg?style=flat-square)](https://codecov.io/gh/netgen/NetgenInformationCollectionBundle)
[![Quality Score](https://img.shields.io/scrutinizer/g/netgen/NetgenInformationCollectionBundle.svg?style=flat-square)](https://scrutinizer-ci.com/g/netgen/NetgenInformationCollectionBundle)
[![Downloads](https://img.shields.io/packagist/dt/netgen/information-collection-bundle.svg?style=flat-square)](https://packagist.org/packages/netgen/information-collection-bundle/stats)
[![Latest stable](https://img.shields.io/packagist/v/netgen/information-collection-bundle.svg?style=flat-square)](https://packagist.org/packages/netgen/information-collection-bundle)
[![License](https://img.shields.io/packagist/l/netgen/information-collection-bundle.svg?style=flat-square)](LICENSE)

</div>

The information collection feature makes it possible to gather user input when a node referencing an information collector object is viewed. 
It is typically useful when it comes to the creation of feedback forms, polls, etc.

An object can collect information if at least one of the class attributes is marked as an information collector.
When the object is viewed, each collector attribute will be displayed using the chosen datatype's data collector template. 
Instead of just outputting the attributes' contents, the collector templates provide interfaces for data input. 
The generated input interface depends on the datatype that represents the attribute. ( From eZ [documentation](https://doc.ez.no/eZ-Publish/Technical-manual/3.9/Concepts-and-basics/Content-management/Information-collection)).

This bundle reimplements information collection feature in eZ Publish 5/Platform stack.

License, docs and installation instructions
-------------------------------------

[License](LICENSE)

[Installation instructions](doc/INSTALL.md)

[Upgrade instructions](doc/UPGRADE.md)

[Documentation](doc/DOC.md)

[Captcha](doc/CAPTCHA.md)

[Changelog](doc/CHANGELOG.md)
