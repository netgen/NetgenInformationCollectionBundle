NetgenInformationCollectionBundle
=================================

[![Build Status](https://img.shields.io/travis/netgen/NetgenInformationCollectionBundle.svg?style=flat-square)](https://travis-ci.org/netgen/NetgenInformationCollectionBundle)
[![Code Coverage](https://img.shields.io/codecov/c/github/netgen/NetgenInformationCollectionBundle.svg?style=flat-square)](https://codecov.io/gh/netgen/NetgenInformationCollectionBundle)
[![Downloads](https://img.shields.io/packagist/dt/netgen/information-collection-bundle.svg?style=flat-square)](https://packagist.org/packages/netgen/information-collection-bundle)
[![Latest stable](https://img.shields.io/packagist/v/netgen/information-collection-bundle.svg?style=flat-square)](https://packagist.org/packages/netgen/information-collection-bundle)
[![License](https://img.shields.io/packagist/l/netgen/information-collection-bundle.svg?style=flat-square)](https://packagist.org/packages/netgen/information-collection-bundle)

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

[Installation instructions](Resources/doc/INSTALL.md)

[Documentation](Resources/doc/DOC.md)

[Changelogs](Resources/doc/CHANGELOG.md)