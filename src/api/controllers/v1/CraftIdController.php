<?php

namespace craftcom\api\controllers\v1;

use Craft;
use craft\elements\Entry;
use craftcom\api\controllers\BaseApiController;
use craftcom\plugins\Plugin;
use yii\web\Response;

/**
 * Class CraftIdController
 *
 * @package craftcom\api\controllers\v1
 */
class CraftIdController extends BaseApiController
{
    // Public Methods
    // =========================================================================

    /**
     * Handles /v1/craft-id requests.
     *
     * @return Response
     */
    public function actionIndex(): Response
    {
        // Current user

        $currentUserId = Craft::$app->getRequest()->getParam('userId');
        $currentUser = Craft::$app->getUsers()->getUserById($currentUserId);


        // Plugins

        $plugins = [];

        $pluginElements = Plugin::find()->developerId($currentUser->id)->all();

        foreach($pluginElements as $pluginElement) {
            $plugins[] = $this->pluginTransformer($pluginElement);
        }


        // Craft licenses

        $craftLicenses = [];

        $craftLicenseEntries = Entry::find()->section('licenses')->type('craftLicense')->authorId($currentUser->id)->all();

        foreach($craftLicenseEntries as $craftLicenseEntry) {
            $craftLicense = $craftLicenseEntry->toArray();
            $craftLicense['plugin'] = $craftLicenseEntry->plugin->one()->toArray();
            $craftLicense['author'] = $craftLicenseEntry->getAuthor()->toArray();
            $craftLicense['type'] = $craftLicenseEntry->getType()->handle;
            $craftLicenses[] = $craftLicense;
        }


        // Plugin licenses

        $pluginLicenses = [];

        $pluginLicenseEntries = Entry::find()->section('licenses')->type('pluginLicense')->authorId($currentUser->id)->all();

        foreach($pluginLicenseEntries as $pluginLicenseEntry) {
            $pluginLicense = $pluginLicenseEntry->toArray();

            $pluginId = $pluginLicenseEntry->pluginId;
            $plugin = Plugin::find()->id($pluginId)->one();

            // $pluginLicense['plugin'] = $pluginLicenseEntry->plugin->one()->toArray();
            $pluginLicense['plugin'] = $plugin->toArray();
            $craftLicense = $pluginLicenseEntry->craftLicense->one();

            if($craftLicense) {
                $pluginLicense['craftLicense'] = $craftLicense->toArray();
            } else {
                $pluginLicense['craftLicense'] = null;
            }
            $pluginLicense['type'] = $pluginLicenseEntry->getType()->handle;
            $pluginLicense['author'] = $pluginLicenseEntry->getAuthor()->toArray();

            $pluginLicenses[] = $pluginLicense;
        }


        // Customers

        $customers = [];

        foreach($pluginElements as $pluginElement) {
            $entries = Entry::find()->section('licenses')->relatedTo($pluginElement)->all();

            foreach($entries as $entry) {

                $found = false;

                foreach($customers as $c) {
                    if($c['id'] == $entry->getAuthor()->id) {
                        $found = true;
                    }
                }

                if(!$found) {
                    $customer = [
                        'id' => $entry->getAuthor()->id,
                        'email' => $entry->getAuthor()->email,
                        'username' => $entry->getAuthor()->username,
                        'fullName' => $entry->getAuthor()->fullName
                    ];

                    $customers[] = $customer;
                }
            }
        }


        // Data

        $data = [
            'currentUser' => [
                'id' => $currentUser->id,
                'firstName' => $currentUser->firstName,
                'lastName' => $currentUser->lastName,
                'developerName' => $currentUser->developerName,
                'developerUrl' => $currentUser->developerUrl,
                'location' => $currentUser->location,
                'cardNumber' => $currentUser->cardNumber,
                'cardExpiry' => $currentUser->cardExpiry,
                'cardCvc' => $currentUser->cardCvc,
                'enablePluginDeveloperFeatures' => ($currentUser->enablePluginDeveloperFeatures == 1 ? true : false),
                'enableShowcaseFeatures' => ($currentUser->enableShowcaseFeatures == 1 ? true : false),
                'businessName' => $currentUser->businessName,
                'businessVatId' => $currentUser->businessVatId,
                'businessAddressLine1' => $currentUser->businessAddressLine1,
                'businessAddressLine2' => $currentUser->businessAddressLine2,
                'businessCity' => $currentUser->businessCity,
                'businessState' => $currentUser->businessState,
                'businessZipCode' => $currentUser->businessZipCode,
                'businessCountry' => $currentUser->businessCountry,
                'vendor' => $currentUser->vendor,
            ],
            'plugins' => $plugins,
            'craftLicenses' => $craftLicenses,
            'pluginLicenses' => $pluginLicenses,
            'customers' => $customers,
            'payouts' => $this->_getPayouts(),
            'payoutsScheduled' => $this->_getScheduledPayouts(),
            'payments' => $this->_getPayments(),
        ];

        return $this->asJson($data);
    }

    // Private Methods
    // =========================================================================

    private function _getPayouts() {
        return [
            [
                'id' => 1,
                'amount' => 99.00,
                'date' => '1 year ago',
                'bank' => [
                    'name' => 'BNP Parisbas',
                    'accountNumber' => '2345678923456783456',
                ]
            ],
            [
                'id' => 2,
                'amount' => 99.00,
                'date' => '1 year ago',
                'bank' => [
                    'name' => 'BNP Parisbas',
                    'accountNumber' => '2345678923456783456',
                ]
            ],
            [
                'id' => 3,
                'amount' => 298.00,
                'date' => '1 year ago',
                'bank' => [
                    'name' => 'BNP Parisbas',
                    'accountNumber' => '2345678923456783456',
                ]
            ],
        ];
    }

    private function _getScheduledPayouts() {
        return [
            [
                'id' => 8,
                'amount' => 116.00,
                'date' => 'Tomorrow',
            ],
        ];
    }

    private function _getPayments() {
        return [
            [
                'items' => [['id' => 6, 'name' => 'Analytics']],
                'amount' => 99.00,
                'customer' => [
                'id' => 1,
                    'name' => 'Benjamin David',
                    'email' => 'ben@pixelandtonic.com',
                ],
                'date' => '3 days ago',
            ],
            [
                'items' => [['id' => 6, 'name' => 'Analytics']],
                'amount' => 99.00,
                'customer' => [
                'id' => 15,
                    'name' => 'Andrew Welsh',
                    'email' => 'andrew@nystudio107.com',
                ],
                'date' => '1 year ago',
            ],
            [
                'items' => [['id' => 7, 'name' => 'Videos']],
                'amount' => 99.00,
                'customer' => [
                'id' => 15,
                    'name' => 'Andrew Welsh',
                    'email' => 'andrew@nystudio107.com',
                ],
                'date' => '1 year ago',
            ],
            [
                'items' => [['id' => 6, 'name' => 'Analytics'], ['id' => 7, 'name' => 'Videos']],
                'amount' => 298.00,
                'customer' => [
                'id' => 15,
                    'name' => 'Andrew Welsh',
                    'email' => 'andrew@nystudio107.com',
                ],
                'date' => '1 year ago',
            ],
        ];
    }
}
