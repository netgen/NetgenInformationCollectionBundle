How to override something on info collector form
================================================

.. code-block:: php

    <?php

    declare(strict_types=1);

    namespace Acme\Form;

    use Acme\Validator\Constraints\MyValidator;
    use Netgen\Bundle\EzFormsBundle\Form\Type\InformationCollectionType;
    use Netgen\Bundle\EzPlatformSiteApiBundle\View\ContentValueView;
    use Symfony\Component\Form\AbstractTypeExtension;
    use Symfony\Component\Form\Extension\Core\Type\TextType;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\RequestStack;

    final class InvoiceNumberFormExtension extends AbstractTypeExtension
    {
        /**
         * @var \Symfony\Component\HttpFoundation\RequestStack
         */
        private $requestStack;

        public function __construct(RequestStack $requestStack)
        {
            $this->requestStack = $requestStack;
        }

        public function getExtendedType(): string
        {
            return InformationCollectionType::class;
        }

        public function buildForm(FormBuilderInterface $builder, array $options): void
        {
            $request = $this->requestStack->getCurrentRequest();

            if (!$request instanceof Request || !$builder->has('invoice_number')) {
                return;
            }

            if (!$request->attributes->has('view')) {
                return;
            }

            $view = $request->attributes->get('view');
            if (!$view instanceof ContentValueView) {
                return;
            }

            $invoiceNumberOptions = $builder->get('invoice_number')->getOptions();
            $invoiceNumberOptions['constraints'][] = new MyValidator();

            $builder->add('invoice_number', TextType::class, $invoiceNumberOptions);
        }
    }

.. code-block:: yaml

    acme.form.invoice_number_extension:
        class: Acme\Form\InvoiceNumberFormExtension
        arguments:
            - '@request_stack'
        tags:
            - { name: form.type_extension, extended_type: Netgen\Bundle\EzFormsBundle\Form\Type\InformationCollectionType }
