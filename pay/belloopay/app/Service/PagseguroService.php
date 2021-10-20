<?php
/*
* IyzipayService.php - Main component file
*
* This file is part of the Account component.
*-----------------------------------------------------------------------------*/

namespace App\Service;

use PagSeguro;
use Exception;
use PagSeguro\Configuration;

class PagseguroService
{
	/**
	 * @var configData - configData
	 */
	protected $configData;

	/**
	 * @var configItem - configItem
	 */
	protected $configItem;

	/**
	 * Constructor
	 *
	 * @return void
	 *-----------------------------------------------------------------------*/

	function __construct()
	{
		$this->configData = configItem();
		//collect pagseguro data in config array
		$this->configItem = getArrayItem($this->configData, 'payments.gateway_configuration.pagseguro', []);
		// Check if config item exists
		if (!empty($this->configItem)) {
			if ($this->configItem['enable'] == true) {
				if ($this->configItem['testMode'] == true) {
					$pagseguroEmail = $this->configItem['email'];
					$pagseguroToken = $this->configItem['testToken'];
				} else {
					$pagseguroEmail = $this->configItem['email'];
					$pagseguroToken = $this->configItem['liveToken'];
				}

				PagSeguro\Library::initialize();
				PagSeguro\Library::cmsVersion()->setName("Nome")->setRelease("1.0.0");
				PagSeguro\Library::moduleVersion()->setName("Nome")->setRelease("1.0.0");
				Configuration\Configure::setEnvironment($this->configItem['environment']);
				Configuration\Configure::setAccountCredentials($pagseguroEmail, $pagseguroToken);
			}
		}
	}

	/**
	 * Request Payment
	 *
	 * @return json object
	 *---------------------------------------------------------------- */
	public function processPagseguroRequest($requestData)
	{
		// Instantiate a new payment request
		$payment = new \PagSeguro\Domains\Requests\Payment();

		// Set the currency
		$payment->setCurrency($this->configItem['currency']);


		// Add one or mmore an item for this payment request
		$payment->addItems()->withParameters(
			$requestData['item_id'], //item id
			$requestData['item_name'], //item name
			$requestData['item_qty'], //item quantity
			$requestData['amounts'][$this->configItem['currency']] //item amount
		);

		//Set Extra Amount
		//$payment->setExtraAmount(11.5);

		//Set unique Refrence Id
		$referenceID = $this->configItem['reference_id'];

		// Set a reference code for this payment request, it is useful to identify this payment
		// in future notifications.
		$payment->setReference($referenceID);

		// Set your customer information.
		//customer name
		$payment->setSender()->setName($requestData['payer_name']);
		//customer email
		$payment->setSender()->setEmail($requestData['payer_email']);
		//customer number
		$payment->setSender()->setPhone()->withParameters($requestData['mobile_code'], $requestData['mobile_number']);
		//customer document credentials
		$payment->setSender()->setDocument()->withParameters(
			'CPF',
			//enter a valid CPF number
			$requestData['cpf_number']
		);

		//add shipping address details
		$payment->setShipping()->setAddress()->withParameters(
			$requestData['shipping_address']['address'],
			$requestData['shipping_address']['week_number'],
			$requestData['shipping_address']['name'],
			$requestData['shipping_address']['zip_code'],
			$requestData['shipping_address']['state'],
			$requestData['shipping_address']['highway_code'],
			$requestData['shipping_address']['country_code'],
			$requestData['shipping_address']['appartment_number']
		);

		//Add shipping cost
		//$payment->setShipping()->setCost()->withParameters($requestData['shipping_cost']);

		//define shipping type
		$payment->setShipping()->setType()->withParameters(\PagSeguro\Enum\Shipping\Type::SEDEX);

		// Set the url used by PagSeguro to redirect user after checkout process ends
		$payment->setRedirectUrl(getAppUrl($this->configItem['callbackUrl']) . '?paymentOption=pagseguro&reference_id=' . $referenceID);

		// Another way to set checkout parameters
		// Set the notification url used by PagSeguro to redirect user after checkout process ends
		$payment->addParameter()->withArray(['notificationURL', getAppUrl($this->configItem['callbackUrl']) . '?paymentOption=pagseguro&reference_id=' . $referenceID]);

		//Add items by parameter using an array
		$payment->setNotificationUrl(getAppUrl($this->configItem['callbackUrl']) . '?paymentOption=pagseguro&reference_id=' . $referenceID);

		//Add discount
		$payment->addPaymentMethod()->withParameters(
			PagSeguro\Enum\PaymentMethod\Group::CREDIT_CARD,
			PagSeguro\Enum\PaymentMethod\Config\Keys::DISCOUNT_PERCENT,
			20.00 // (float) Percent
		);

		//Add installments with no interest
		$payment->addPaymentMethod()->withParameters(
			PagSeguro\Enum\PaymentMethod\Group::CREDIT_CARD,
			PagSeguro\Enum\PaymentMethod\Config\Keys::MAX_INSTALLMENTS_NO_INTEREST,
			4 // (int) qty of installment
		);

		//Add a limit for installment
		$payment->addPaymentMethod()->withParameters(
			PagSeguro\Enum\PaymentMethod\Group::CREDIT_CARD,
			PagSeguro\Enum\PaymentMethod\Config\Keys::MAX_INSTALLMENTS_LIMIT,
			6 // (int) qty of installment
		);

		// Add a group and/or payment methods name
		$payment->acceptPaymentMethod()->groups(
			\PagSeguro\Enum\PaymentMethod\Group::CREDIT_CARD,
			\PagSeguro\Enum\PaymentMethod\Group::BALANCE
		);

		//Add a payment methods name
		$payment->acceptPaymentMethod()->name(\PagSeguro\Enum\PaymentMethod\Name::DEBITO_ITAU);

		//Remove a group and/or payment methods name
		$payment->excludePaymentMethod()->group(\PagSeguro\Enum\PaymentMethod\Group::BOLETO);

		try {

			/**
			 * @todo For checkout with application use:
			 * \PagSeguro\Configuration\Configure::getApplicationCredentials()
			 *  ->setAuthorizationCode("Transaction Code")
			 */
			$redirectUrl = $payment->register(
				Configuration\Configure::getAccountCredentials()
			);

			return [
				'status'        => 'success',
				'referenceID'   => $referenceID,
				'redirect_url'  => $redirectUrl
			];
		} catch (Exception $e) {
			return [
				'status'   => 'error',
				'message'  => $e->getMessage()
			];
		}
	}

	/**
	 * fetch transaction by refrence ID
	 *
	 * @return json object
	 *---------------------------------------------------------------- */
	public function captureTransactionByReferenceId($referenceID)
	{
		$options = [
			'initial_date' => substr(date('c', strtotime("-1 days")), 0, 16) //'2020-04-01T14:55', //enter initial date // date('yyyy-MM-ddTHH:mm')
			// 'final_date' => '2020-04-T09:55', //Optional
			// 'page' => 1, //Optional
			// 'max_per_page' => 20, //Optional
		];

		//check reference id exist or not
		if (isset($referenceID) and !empty($referenceID)) {
			try {
				$response = \PagSeguro\Services\Transactions\Search\Reference::search(
					\PagSeguro\Configuration\Configure::getAccountCredentials(),
					$referenceID,
					$options
				);

				return [
					'status'        => 'success',
					'responseData'  => $response
				];
			} catch (Exception $e) {
				return [
					'status'   => 'error',
					'message'  => $e->getMessage()
				];
			}
		} else {
			return [
				'status'   => 'error',
				'message'  => 'Reference id not exists.'
			];
		}
	}

	/**
	 * fetch transaction object by Txn ID
	 *
	 * @return json object
	 *---------------------------------------------------------------- */
	public function captureTransactionByTxnCode($transactionCode)
	{
		$transactionCode = $transactionCode;

		//check reference id exist or not
		if (isset($transactionCode) and !empty($transactionCode)) {
			try {
				$response = \PagSeguro\Services\Transactions\Search\Code::search(
					\PagSeguro\Configuration\Configure::getAccountCredentials(),
					$transactionCode
				);

				return [
					'status'        => 'success',
					'responseData'  => $response
				];
			} catch (Exception $e) {
				return [
					'status'   => 'error',
					'message'  => $e->getMessage()
				];
			}
		} else {
			return [
				'status'   => 'error',
				'message'  => 'Transaction code not exists.'
			];
		}
	}
}
