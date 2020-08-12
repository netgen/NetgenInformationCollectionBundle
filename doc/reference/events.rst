Events
======

Netgen Layouts dispatches some events in a lifecycle of displaying the page with
a resolved layout that you can listen to and act upon.

The following lists all available events.

netgen_information_collection.events.information_collected
----------------------------------------------------------

**Event class**: ``Netgen\InformationCollection\API\Value\Event\InformationCollected``

This event will be dispatched when the view of a value is being rendered. It can
be used to inject custom variables into the view **before** the view is sent to
Twig for rendering.

