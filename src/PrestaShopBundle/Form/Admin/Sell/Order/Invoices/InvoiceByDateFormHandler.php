<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Form\Admin\Sell\Order\Invoices;

use DateTime;
use PrestaShop\PrestaShop\Core\DataProvider\OrderInvoiceDataProviderInterface;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;
use PrestaShop\PrestaShop\Core\Form\FormHandler;
use PrestaShop\PrestaShop\Core\PDF\InvoicePDFGeneratorInterface;
use PrestaShopBundle\Service\Hook\HookDispatcher;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class InvoiceByDateFormHandler manages the data manipulated using "By date" form
 * in "Sell > Orders > Invoices" page
 */
class InvoiceByDateFormHandler extends FormHandler
{
    /**
     * @var OrderInvoiceDataProviderInterface
     */
    private $orderInvoiceDataProvider;

    /**
     * @var InvoicePDFGeneratorInterface
     */
    private $invoicePDFGenerator;

    public function __construct(
        FormBuilderInterface $formBuilder,
        HookDispatcher $hookDispatcher,
        FormDataProviderInterface $formDataProvider,
        array $formTypes,
        $hookName,
        OrderInvoiceDataProviderInterface $orderInvoiceDataProvider,
        InvoicePDFGeneratorInterface $invoicePDFGenerator
    ) {
        parent::__construct($formBuilder, $hookDispatcher, $formDataProvider, $formTypes, $hookName);
        $this->orderInvoiceDataProvider = $orderInvoiceDataProvider;
        $this->invoicePDFGenerator = $invoicePDFGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function save(array $data)
    {
        if ($errors = parent::save($data)) {
            return $errors;
        }

        // Get invoices by submitted date interval
        $invoiceCollection = $this->orderInvoiceDataProvider->getByDateInterval(
            new DateTime($data['generate_by_date']['date_from']),
            new DateTime($data['generate_by_date']['date_to'])
        );

        // Generate PDF out of found invoices
        $this->invoicePDFGenerator->generateInvoicesPDF($invoiceCollection);

        return [];
    }
}
