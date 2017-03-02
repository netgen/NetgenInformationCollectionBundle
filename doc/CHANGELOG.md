Changelog
=========

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

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
