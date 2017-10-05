Changelog
=========

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [1.2.0] - 2017-10-05
### Added
- `EmailAction` now supports sending attachments

## [1.1.0] - 2017-09-15
### Added
- Implemented custom field handlers for checkbox, integer and float field value handlers
- Created `CustomLegacyFieldHandlerInterface` for customizing field values
 
## [1.0.6] - 2017-08-17
### Added
- Use hasParameter before fetching it from container fix (by @emodric )

## [1.0.5] - 2017-06-29 
### Added
- Email templates were not properly resolved

## [1.0.4] - 2017-06-27 
### Added
- InformationCollectionMapper, InformationCollectionStruct and InformationCollectionType
- Bundle requires EzFormsBundle in version 1.3.2 or greater
- Enabled Scrutinizer service
- Fixed problem with action configuration by content type

## [1.0.3] - 2017-04-21
### Added
- Symfony 3.x compatibility

## [1.0.2] - 2017-04-18
### Added
- marked bundle as `ezplatform-bundle` in composer.json

### Changed
- allowed eZ Forms Bundle 2.0 to be installed

## [1.0.1] - 2017-02-03
### Added
- `ezinfocollection` and `ezinfocollection_attribute` table indexes inside Entity classes
- bundle now requires `doctrine/orm` by default
- updated install doc

## [1.0] - 2017-17-02
### Added
- ability to render recipient, sender and subject from template.
- more descriptive exception classes.
- more descriptive error logging.
- extracted mailer to a separate class.
- Events class is set as final.
- factory classes moved to separate services .yml file.
- InformationCollectionTrait.
- EmailDataFactory does not require ContentTypeService anymore.
- InformationCollectionController implements ContainerAwareInterface.
- Actions priority.
- Action can be marked as crucial with CrucialActionInterface.
- eZ 5 compatibility (InformationCollectionLegacyController and InformationCollectionLegacyTrait).
- exposed collected data to email and full view template.
- disabled http cache in controllers (thanks @emodric).
- CSRF token enabled by default (thanks @emodric).
- ActionRegistry relies on kernel.debug parameter to throw or ignore exceptions.

## [0.8] - 2017-13-02
### Added
- documentation update.
- every action throws ActionFailedException on failure.
- Start following [SemVer](http://semver.org) properly.
- Changelog.
- Project cleanup.

## [0.7.1] - 2017-22-01
### Changed
- Removed config resolver from EmailDataFactory.

## [0.7.0] - 2016-29-12
### Added
- New configuration for actions.

### Changed
- Tweaked action registry.
- Removed unused email templates.
