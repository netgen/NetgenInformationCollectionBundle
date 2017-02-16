Changelog
=========

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [dev]
### Added
- ability to render recipient, sender and subject from template
- more descriptive exception classes
- more descriptive error logging
- extracted mailer to a separate class
- Events class is set as final
- factory classes moved to separate services .yml file
- InformationCollectionTrait
- EmailDataFactory does not require ContentTypeService anymore
- InformationCollectionController implements ContainerAwareInterface
- Actions priority
- Action can be marked as crucial with CrucialActionInterface

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
